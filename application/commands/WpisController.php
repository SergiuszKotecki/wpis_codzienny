<?php

namespace app\commands;

use app\models\ThreadsFollowers;
use app\models\ThreadsFollowersBan;
use app\models\ThreadsFollowersPluses;
use app\models\ThreadsRows;
use app\models\ThreadsRowsCron;
use app\models\ThreadsRowsPluses;
use Yii;
use yii\base\Action;
use yii\console\Controller;
use Wkop\Factory;
use app\models\Repository\ThreadsRepository;
use app\models\Repository\ThreadsRowsRepository;
use app\models\Users;
use app\models\Enum\ThreadsRowsEnum;
use app\components\WykopApi\Wapi;
use yii\helpers\ArrayHelper;

date_default_timezone_set('Europe/Warsaw');
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WpisController extends Controller
{
    /**
     * @var array
     */
    private $config;

    /**
     * @todo replace with template
     * @var string
     */
    private $_body_text_end_call_followers = "\r\r\r! (zaplusuj ten wpis aby zostać wołany do następnych)\r\r";

    /**
     * @todo replace with template
     * @var string
     */
    private $_body_text_end = "\r\r\r  -----------------------------\r ";

    /**
     * @param Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->config = Yii::$app->params['wykop_api'];

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->addFollowers();
        $this->sendThreadRows();
        $this->getInfo();
    }

    private function addFollowers()
    {
        $client = Factory::get($this->config['app_key'], $this->config['secret_key']);

        $threadsResult = (new ThreadsRowsRepository)->getAllSendThreadsRows();

        foreach ($threadsResult as $row) {
            $response = $client->get(
                'entries',
                ['index', $row['thread_row_id'],],
                ['appkey' => $this->config['app_key'],]
            );

            if (!isset($response['voters'])) {
                continue;
            }

            $votersToRemove = ThreadsFollowersPluses::findAll(['thread_id' => $row['thread_id']]);
            $votersFromDb = ArrayHelper::map($votersToRemove, 'follower_name', 'follower_name');

            foreach ($response['voters'] as $v) {
                if (array_key_exists($v['author'], $votersFromDb)) {
                    continue;
                }

                $threadRowPluses = new ThreadsFollowersPluses;
                $threadRowPluses->thread_id = $row['thread_id'];
                $threadRowPluses->follower_name = $v['author'];

                if (!$threadRowPluses->insert()) {
                    continue;
                }
            }
        }
    }

    private function sendThreadRows()
    {
        $threadsResult = (new ThreadsRepository)->getAllActiveThreads();

        $client = new Wapi($this->config['app_key'], $this->config['secret_key']);

        foreach ($threadsResult as $userId => $row) {
            $user = Users::findOne(['id' => $userId]);

            $loginParams = [
                'login' => $user->login,
                'accountkey' => $user->account_key
            ];

            $loginResponse = $client->doRequest('user/login', $loginParams);

            if (!isset($loginResponse['userkey'])) {
                continue;
            }

            $client->setUserKey($loginResponse['userkey']);

            foreach ($row as $threadId => $threadData) {

                $threadRow = (new ThreadsRowsRepository)->getAllActiveThreadsRows($threadId);

                $apiParams = [
                    'appkey' => $this->config['app_key'],
                    'userkey' => $this->config['secret_key'],
                    'body' => $threadRow['body_text'],
                ];

                if ($threadData['flag_call_followers']) {
                    $apiParams['body'] .= $this->_body_text_end_call_followers;
                }

                $apiParams['body'] .= $this->_body_text_end;

                $file = null;
                if ($threadRow['body_embedded']) {
                    $apiParams['embed'] = $threadRow['body_embedded'];
                } elseif ($threadRow['body_embedded_file']) {
                    $file = [
                        'embed' => new \CURLFile(UPLOAD_PATH . $threadRow['body_embedded_file'])
                    ];
                }

                $response = $client->doRequest('entries/add', $apiParams, $file);

                if (isset($response['id'])) {
                    $tr = ThreadsRows::findOne(['id' => $threadRow['id']]);
                    $tr->thread_row_id = $response['id'];
                    $tr->sent_at = date('Y-m-d H:i:s');
                    $tr->status = ThreadsRowsEnum::STATUS_SEND;
                } else {
                    $tr = ThreadsRows::findOne(['id' => $threadRow['id']]);
                    $tr->status = ThreadsRowsEnum::STATUS_SEND_ERROR;
                }

                if (!$tr->update()) {
                    throw new \Exception('Error');
                }

                $trc = new ThreadsRowsCron;
                $trc->thread_id = $threadId;
                $trc->thread_row_id = $threadRow['id'];
                $trc->created_at = date('Y-m-d H:i:s');
                $trc->insert();

                if ($tr->status == ThreadsRowsEnum::STATUS_SEND) {
                    $this->callFollowers($client, $threadData, $response['id']);
                }
            }
        }
    }

    private function callFollowers(Wapi $client, $threadData, $threadRowId)
    {
        $threadFollowers = ThreadsFollowers::findAll(['thread_id' => $threadData['id']]);
        $threadFollowers = ArrayHelper::map($threadFollowers, 'follower_name', 'follower_name');

        $callFollowersPluses = [];
        if ($threadData['flag_call_followers']) {
            $threadFollowersPluses = ThreadsFollowersPluses::findAll(['thread_id' => $threadData['id']]);
            $threadFollowersPluses = ArrayHelper::map($threadFollowersPluses, 'follower_name', 'follower_name');

            $threadFollowersBaned = ThreadsFollowersBan::findAll(['thread_id' => $threadData['id']]);
            $threadFollowersBaned = ArrayHelper::map($threadFollowersBaned, 'follower_name', 'follower_name');

            foreach ($threadFollowersPluses as $followerName) {
                if (array_key_exists($followerName, $threadFollowersBaned)) {
                    unset($threadFollowers[$followerName]);
                    unset($threadFollowersPluses[$followerName]);
                    continue;
                }

                if (array_key_exists($followerName, $threadFollowers)) {
                    unset($threadFollowers[$followerName]);
                    continue;
                }
                $callFollowersPluses[$followerName] = $followerName;
            }
        }

        $i = $k = 1;
        $body = [];
        foreach ($threadFollowers as $row) {
            if ($i >= 15) {
                $k++;
                $i = 1;
            }

            if (!isset($body[$k])) {
                /**
                 * @todo move to templates
                 */
                $body[$k] = '! Wołam osoby z listy: ';
            }
            $body[$k] .= '@' . $row .' ';
            $i++;
        }

        $k++;
        $i = 1;
        foreach ($callFollowersPluses as $row) {
            if ($i >= 15) {
                $k++;
                $i = 1;
            }

            if (!isset($body[$k])) {
                /**
                 * @todo move to templates
                 */
                $body[$k] = '! Wołam plusujących poprzednie wpisy: ';
            }
            $body[$k] .= '@' . $row .' ';
            $i++;
        }

        foreach ($body as $bodyText) {
            $apiParams = [
                'appkey' => $this->config['app_key'],
                'userkey' => $this->config['secret_key'],
                'body' => $bodyText
            ];

            $response = $client->doRequest('entries/addComment/' . $threadRowId, $apiParams);

            if (!isset($response['id'])) {
                throw new \Exception($client->getError());
            }
        }
    }

    private function getInfo()
    {
        $client = Factory::get($this->config['app_key'], $this->config['secret_key']);

        $threadsResult = (new ThreadsRowsRepository)->getAllSendThreadsRowsForInfo();

        foreach ($threadsResult as $row) {
            $apiParams = [
                'appkey' => $this->config['app_key'],
            ];

            $response = $client->get('entries', ['index', $row['thread_row_id']], $apiParams);

            if (isset($response['vote_count'])) {
                $threadRowPluses = new ThreadsRowsPluses;
                $threadRowPluses->thread_row_id = $row['id'];
                $threadRowPluses->hour = date('G');
                $threadRowPluses->pluses = (int) $response['vote_count'];
                $threadRowPluses->insert();
            }
        }
    }
}
