<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use app\models\Threads;
use app\models\ThreadsModeratorsHash;
use app\models\Enum\ThreadsEnum;
use app\models\Repository\UsersRepository;
use app\models\ThreadsModerators;
use Wkop\Helpers;
use Wkop\Factory;


class ModeratorController extends Controller
{
    private $_config;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add', 'delete', 'list',],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add', 'delete', 'list',],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

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

    public function actionList()
    {
        $id = $this->_getThreadId();
        $thread = $this->loadModel($id);

        $model = (new ThreadsModeratorsHash)->findAll(['thread_id' => $thread->id]);

        return $this->render('list', [
            'id' => $id,
            'model' => $model,
            'config' => $this->_config
        ]);
    }

    public function actionAdd()
    {
        $threadId = Yii::$app->request->get('thread_id');
        $moderatorName = Yii::$app->request->post('name');

        if (is_null($threadId) || is_null($moderatorName)) {
            Yii::$app->getSession()->setFlash('danger', 'Niepoprawne wywołanie metody!');
            return $this->redirect('/');
        }

        if (strtolower($moderatorName) === strtolower(Yii::$app->user->identity->login) ) {
            Yii::$app->getSession()->setFlash('danger', 'Nie możesz dodać siebie jako moderatora!');
            return $this->redirect('/moderator/list/' . $threadId);
        }

        $this->loadModel($threadId);

        $threadModeratorHash = ThreadsModeratorsHash::findOne([
            'thread_id' => $threadId,
            'moderator_name' => $moderatorName
        ]);

        if (!is_null($threadModeratorHash)) {
            Yii::$app->getSession()->setFlash('danger', 'Taki moderator został już przypisany!');
            return $this->redirect('/moderator/list/' . $threadId);
        }

        $hash = $this->_generateHash($moderatorName, $threadId);


        $threadModeratorHash = new ThreadsModeratorsHash;
        $threadModeratorHash->thread_id = $threadId;
        $threadModeratorHash->moderator_name = $moderatorName;
        $threadModeratorHash->moderator_hash = $hash;
        $threadModeratorHash->status = 0;

        if (!$threadModeratorHash->insert()) {
            Yii::$app->getSession()->setFlash('danger', 'Nieudało się przypisać moderatora!');
            return $this->redirect('/moderator/list/' . $threadId);
        }

        Yii::$app->getSession()->setFlash('success', 'Udało się przypisać moderatora!');
        return $this->redirect('/moderator/list/' . $threadId);
    }

    public function actionDelete()
    {
        $threadId = Yii::$app->request->get('thread_id');
        $id = Yii::$app->request->post('id');

        if (is_null($id)) {
            Yii::$app->getSession()->setFlash('danger', 'Niepoprawne wywołanie metody!');
            return $this->redirect('/');
        }

        $threadModeratorHash = ThreadsModeratorsHash::findOne([
            'id' => $id,
            'thread_id' => $threadId
        ]);

        if (is_null($threadModeratorHash)) {
            Yii::$app->getSession()->setFlash('danger', 'Niepoprawne wywołanie metody!');
            return $this->redirect('/');
        }

        $this->loadModel($threadModeratorHash->thread_id);

        if (!$threadModeratorHash->delete()) {
            Yii::$app->getSession()->setFlash('danger', 'Nieudało się usunięcie moderatora!');
            return $this->redirect('/moderator/list/' . $threadId);
        }

        $user = \app\models\Users::findOne([
            'login' => $threadModeratorHash->moderator_name
        ]);

        if (!$user) {
            Yii::$app->getSession()->setFlash('danger', 'Nieudało się usunięcie moderatora!');
            return $this->redirect('/moderator/list/' . $threadId);
        }

        $threadModerator = ThreadsModerators::findOne([
            'thread_id' => $threadModeratorHash->thread_id,
            'moderator_id' => $user->id
        ]);

        if ($threadModerator) {
            if (!$threadModerator->delete()) {
                Yii::$app->getSession()->setFlash('danger', 'Nieudało się usunięcie moderatora!');
                return $this->redirect('/moderator/list/' . $threadId);
            }
        }

        Yii::$app->getSession()->setFlash('success', 'Udało się usunąć moderatora!');
        return $this->redirect('/moderator/list/' . $threadId);
    }

    public function actionRedirect()
    {
        $hash = Yii::$app->request->get('hash');
        if (!$hash) {
            Yii::$app->getSession()->setFlash('danger', 'Niepoprawne wywołanie metody!');
            return $this->redirect('/');
        }

        $url = Helpers::getConnectUrl($this->_config['redirect_url_moderators'] . $hash, $this->_config['app_key'], $this->_config['secret_key']);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
    }

    /**
     *
     * @return type
     * @throws \Exception
     */
    public function actionAuthenticateModerator()
    {
        $hash = Yii::$app->request->get('hash');
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

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (is_null($user)) {
                    $user = new Users;
                    $user->login = $connectData['login'];
                    $user->account_key = $connectData['token'];
                    $user->created_at = date('Y-m-d H:i:s');

                    if (!$user->insert()) {
                        throw new \Exception('Nieudało się zalogować');
                    }
                } else {
                    $user->account_key = $connectData['token'];

                    if (!$user->save()) {
                    }
                }

                $threadModeratorHash = $this->_changeStatusThreadModeratorHash($connectData, $hash);
                $this->_addThreadModerator($user->id, $threadModeratorHash->thread_id);

                if (!Yii::$app->user->login($user)) {
                    throw new \Exception('Nieudało się zalogować');
                }

                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', 'Zostałeś pomyślnie zalogowany!');
                return $this->redirect('/');
            } catch (\Exception $e) {
                $transaction->rollBack();
            }
        }

        Yii::$app->getSession()->setFlash('danger', 'Nie udało się zalogować do aplikacji,!');
        return $this->redirect('/');
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function loadModel($id, $ajax = false)
    {
        if (!$id) {
            if ($ajax) {
                echo json_encode(['error' => 'Nie udało się zmienić statusu']);
                exit;
            }
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = Threads::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if (!$model) {
            if ($ajax) {
                echo json_encode(['error' => 'Nie udało się zmienić statusu']);
                exit;
            }
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->status == ThreadsEnum::STATUS_DELETED) {
            if ($ajax) {
                echo json_encode(['error' => 'Nie udało się zmienić statusu']);
                exit;
            }
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $model;
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function loadModelForAll($id)
    {
        if (!$id) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = Threads::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);

        if (!$model) {

            $threadModerator = ThreadsModerators::findOne(['thread_id' => $id, 'moderator_id' => Yii::$app->user->id]);

            if (!$threadModerator) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $model = Threads::findOne(['id' => $id]);
        }

        if ($model->status == ThreadsEnum::STATUS_DELETED) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Method will return thread id from get
     *
     * @return int
     */
    private function _getThreadId($method = 'get')
    {
        $id = Yii::$app->request->{$method}('thread_id');
        if (!$id) {
            Yii::$app->getSession()->setFlash('danger', 'Przekazano nieprawidłowe parametry!');
            return $this->redirect('/');
        }

        return $id;
    }

    private function _changeStatusThreadModeratorHash($connectData, $hash)
    {
        $threadModeratorHash = (new ThreadsModeratorsHash)->findOne([
            'moderator_name' => $connectData['login'],
            'moderator_hash' => $hash,
        ]);

        if (!is_null($threadModeratorHash)) {
            $threadModeratorHash->status = 1;

            if (!$threadModeratorHash->save()) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $threadModeratorHash;
    }

    private function _addThreadModerator($userId, $threadId)
    {
        $threadModerator = (new \app\models\ThreadsModerators)->findOne([
            'moderator_id' => $userId,
            'thread_id' => $threadId
        ]);

        if (is_null($threadModerator)) {
            $threadModerator = new ThreadsModerators;
            $threadModerator->moderator_id = $userId;
            $threadModerator->thread_id = $threadId;
            $threadModerator->insert();
        }
    }

    private function _generateHash($moderatorName, $threadId)
    {
        $hash = substr(md5($moderatorName . $threadId . uniqid()), 0, 25);
        $threadModeratorHash = ThreadsModeratorsHash::findOne([
            'moderator_hash' => $hash
        ]);

        if (!is_null($threadModeratorHash)) {
            return $this->_generateHash($moderatorName, $threadId);
        }
        return $hash;
    }
}
