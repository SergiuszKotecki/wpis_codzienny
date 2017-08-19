<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Wkop\Factory;
use Wkop\Helpers;
use app\models\Repository\ThreadsRepository;
use app\models\Repository\UsersRepository;
use app\models\Repository\ThreadsRowsRepository;
use app\models\Users;

class IndexController extends Controller
{
    private $_config;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'danger' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->_config = Yii::$app->params['wykop_api'];

        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new ThreadsRepository();

        // Generate connect url
        $url = Helpers::getConnectUrl($this->_config['redirect_url'], $this->_config['app_key'], $this->_config['secret_key']);

        $method = (\Yii::$app->user->isGuest) ? 'index' : 'index_loggedin';

        return $this->render($method,[
            'model' => $model,
            'url' => $url
        ]);
    }

    /**
     * Cron entry put
     *
     * @return string
     */
    public function actionEntry()
    {
        if (Yii::$app->request->get('id') != 'polska_gola') {
            return;
        }

        $threadsResult = (new ThreadsRepository)->getAllActiveThreads();

        foreach ($threadsResult as $userId => $threads) {

            $user = Users::findOne(['id' => $userId]);
            $client = Factory::get($this->_config['app_key'], $this->_config['secret_key']);
            $client->setUserCredentials($user->login, $user->account_key);

            if ($client->logIn()) {

                foreach ($threads as $thread) {

                    $threadsRows = (new ThreadsRowsRepository)->getAllActiveThreadsRows($thread['id']);

                    foreach ($threadsRows as $threadsRow) {
                        $apiParams = [
                            'appkey' => $this->_config['app_key'],
                            'userkey' => $user->user_key
                        ];
                        var_dump($client->post('entries', ['add'], $apiParams, ['body' => $threadsRow['text']]));
                    }
                }
            }
        }
    }

    public function actionAuthenticate()
    {
        $result = Yii::$app->request->get('connectData');

        if (!$result) {
            Yii::$app->getSession()->setFlash('danger', 'Nie udało się zalogować do aplikacji,,!');
            return $this->redirect('/');
        }

        $connectData = json_decode(base64_decode($result), true);

        $client = Factory::get($this->_config['app_key'], $this->_config['secret_key']);
        $client->setUserCredentials($connectData['login'], $connectData['token']);

        if ($client->logIn()) {
            $user = (new UsersRepository)->findUserByLogin($connectData['login']);

            $save = false;
            if (is_null($user)) {
                $user = new Users;
                $user->login = $connectData['login'];
                $user->account_key = $connectData['token'];
                $user->created_at = date('Y-m-d H:i:s');

                if ($user->insert()) {
                    $save = true;
                }
            } else {
                $user->account_key = $connectData['token'];

                if ($user->save()) {
                    $save = true;
                }
            }

            if ($save && Yii::$app->user->login($user)) {
                Yii::$app->getSession()->setFlash('success', 'Zostałeś pomyślnie zalogowany!');
                return $this->redirect('/');
            }
        }

        Yii::$app->getSession()->setFlash('danger', 'Nie udało się zalogować do aplikacji!');
        return $this->redirect('/');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
