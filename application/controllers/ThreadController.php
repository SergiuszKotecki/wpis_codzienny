<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Repository\ThreadsRepository;
use app\models\Repository\ThreadsModeratorsRepository;
use app\models\Forms\AddNewThreadForm;
use app\models\Threads;
use app\models\ThreadsModerators;
use app\models\ThreadsFollowers;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use app\models\Enum\ThreadsEnum;
use app\models\ThreadsFollowersBan;
use yii\web\Response;

class ThreadController extends Controller
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
                'only' => ['add', 'change-status', 'delete', 'edit', 'view',],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add', 'change-status', 'delete', 'edit', 'view',],
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionAdd()
    {
        $threadsNumber = (new ThreadsRepository)->getThreadsCount(\Yii::$app->user->id);
        if ($threadsNumber['cnt'] > 5) {
            Yii::$app->getSession()->setFlash('danger', 'Przekroczono limit pięciu tematów na użytkownika, usuń stare tematy aby móc dodać nowe!');
            return $this->redirect('/');
        }

        $model = new AddNewThreadForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $thread = new Threads;
                $thread->user_id = Yii::$app->user->id;
                $thread->name = $model->name;
                $thread->hour_send = $model->hour_send .':00';
                $thread->status = $model->status;
                $thread->flag_call_followers = $model->flag_call_followers;
                $thread->created_at = date('Y-m-d H:i:s');

                if ($thread->insert() === false) {
                    throw new \Exception('Error while updating thread');
                }

                if (!empty($model->field_call_followers)) {
                    $this->_addFieldCallFollowers($thread->id, $model->field_call_followers);
                }

                if ($model->flag_call_followers && !empty($model->field_call_followers_ban)) {
                    $this->_addFieldCallFollowersBan($thread->id, $model->field_call_followers_ban);
                }

                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', 'Pomyślnie dodano nowy temat!');
                return $this->redirect('/thread/view/' . $thread->id);
            } catch (\Exception $e) {
                echo $e->getMessage();die;
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('danger', 'Nie udało się dodanie nowego tematu!');
                return $this->redirect('/thread/add');
            }
        }

        return $this->render('add', [
            'model' => $model
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $thread = $this->loadModel($id);

        $model = new AddNewThreadForm();
        $model->attributes = $thread->attributes;
        $model->hour_send = substr($thread->hour_send, 0, 5);

        $threadsFollowersResult = ThreadsFollowers::findAll(['thread_id' => $thread->id]);
        if (!empty($threadsFollowersResult)) {
            $model->field_call_followers = implode(' ', ArrayHelper::map($threadsFollowersResult, 'follower_name', 'follower_name'));
        }

        $threadsFollowersBanResult = ThreadsFollowersBan::findAll(['thread_id' => $thread->id]);
        if (!empty($threadsFollowersBanResult)) {
            $model->field_call_followers_ban = implode(' ', ArrayHelper::map($threadsFollowersBanResult, 'follower_name', 'follower_name'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $thread->name = $model->name;
                $thread->hour_send = $model->hour_send .':00';
                $thread->status = $model->status;
                $thread->flag_call_followers = $model->flag_call_followers;

                if ($thread->update() === false) {
                    throw new \Exception('Error while updating thread');
                }

                $threadsFollowersResult = ThreadsFollowers::findAll(['thread_id' => $thread->id]);
                foreach ($threadsFollowersResult as $row) {
                    if ($row->delete() === false) {
                        throw new \Exception('Error while inserting thread moderator');
                    }
                }

                if (!empty($model->field_call_followers)) {
                    $this->_addFieldCallFollowers($thread->id, $model->field_call_followers);
                }

                if ($model->flag_call_followers && !empty($model->field_call_followers_ban)) {
                    $this->_addFieldCallFollowersBan($thread->id, $model->field_call_followers_ban);
                }

                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', 'Pomyślnie zapisano zmiany!');
                return $this->redirect('/thread/view/' . $id);
            } catch (\Exception $e) {
                echo $e->getMessage();die;
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('danger', 'Nie udało się zapisać zmian!');
                return $this->redirect('/thread/edit/' . $id);
            }
        }

        return $this->render('edit', [
            'model' => $model
        ]);
    }

    /**
     * @return Response
     */
    public function actionDelete()
    {
        if (!Yii::$app->request->isPost) {
            Yii::$app->getSession()->setFlash('danger', 'Nie poprawne wywołanie metody!');
            return $this->redirect('/', 400);
        }

        $thread = $this->loadModel(Yii::$app->request->post('id'));
        $thread->status = ThreadsEnum::STATUS_DELETED;

        if ($thread->save() === false) {
            Yii::$app->getSession()->setFlash('danger', 'Nie udało się usunąć tematu!');
            return $this->redirect('/');
        }

        Yii::$app->getSession()->setFlash('success', 'Temat został pomyślnie usunięty!');
        return $this->redirect('/');
    }

    /**
     * @return string
     */
    public function actionView()
    {
        $thread = $this->loadModelForAll(Yii::$app->request->get('id'));

        $moderators = (new ThreadsModeratorsRepository)->getAllModeratorsFromUserThreads();

        return $this->render('view', [
            'model' => $thread,
            'moderators' => $moderators
        ]);
    }

    /**
     *
     */
    public function actionChangeStatus()
    {
        if (!Yii::$app->request->isAjax) {
            echo 'Nie udało się zmienić statusu';
            exit;
        }

        $thread = $this->loadModel(Yii::$app->getRequest()->getBodyParam('id'), true);
        $thread->status = (Yii::$app->getRequest()->getBodyParam('status') ? 0 : 1);

        if ($thread->update() === false) {
            echo 'Nie udało się zmienić statusu';
            exit;
        }

        echo 'Status został zmieniony!';
        exit;
    }

    /**
     * @param $id
     * @param bool $ajax
     * @return null|Threads
     * @throws NotFoundHttpException
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
     * @param $id
     * @return null|Threads
     * @throws NotFoundHttpException
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
     * @deprecated dont use it
     * @param int $threadId
     * @param string $moderators
     * @throws \Exception
     */
    private function _addFieldModerators($threadId, $moderators)
    {
        $stringCleanDoubleSpaces = strtr($moderators, [',' => ' ', '  ' => ' ']);
        $stringCleanSpecialCharacters = preg_replace('/[^a-zA-Z0-9_\-\,\ ]/', '', $stringCleanDoubleSpaces);
        $stringExplode = explode(' ', $stringCleanSpecialCharacters);

        $threadsModeratorsResult = ThreadsModerators::findAll([
            'thread_id' => $threadId,
            'moderator_name' => $stringExplode,
        ]);

        $threadsModerators = ArrayHelper::map($threadsModeratorsResult, 'moderator_name', 'moderator_name');

        $cleanedModerators = array_unique(array_diff($stringExplode, $threadsModerators));

        foreach ($cleanedModerators as $moderator) {
            if (!empty($moderator)) {
                $model = new ThreadsModerators();
                $model->thread_id = $threadId;
                $model->moderator_id = $moderator;

                if ($model->insert() === false) {
                    throw new \Exception('Error while adding thread');
                }
            }
        }
    }

    /**
     *
     * @param int $threadId
     * @param string $field_call_followers
     * @throws \Exception
     */
    private function _addFieldCallFollowers($threadId, $field_call_followers)
    {
        $stringCleanDoubleSpaces = strtr($field_call_followers, [',' => ' ', '  ' => ' ']);
        $stringCleanSpecialCharacters = preg_replace('/[^a-zA-Z0-9_\-\,\ ]/', '', $stringCleanDoubleSpaces);
        $stringExplode = explode(' ', $stringCleanSpecialCharacters);

        $threadsFollowersResult = ThreadsFollowers::findAll([
            'thread_id' => $threadId,
        ]);

        foreach ($threadsFollowersResult as $row) {
            $row->delete();
        }

        foreach ($stringExplode as $follower) {
            if (!empty($follower)) {
                $threadFollower = new ThreadsFollowers;
                $threadFollower->thread_id = $threadId;
                $threadFollower->follower_name = $follower;

                if ($threadFollower->insert() === false) {
                    throw new \Exception('Error while adding thread');
                }
            }
        }
    }

    /**
     *
     * @param int $threadId
     * @param string $field_call_followers_baned
     * @throws \Exception
     */
    private function _addFieldCallFollowersBan($threadId, $field_call_followers_baned)
    {
        $stringCleanDoubleSpaces = strtr($field_call_followers_baned, [',' => ' ', '  ' => ' ']);
        $stringCleanSpecialCharacters = preg_replace('/[^a-zA-Z0-9_\-\,\ ]/', '', $stringCleanDoubleSpaces);
        $stringExplode = explode(' ', $stringCleanSpecialCharacters);

        $threadsFollowersResult = ThreadsFollowersBan::findAll([
            'thread_id' => $threadId,
        ]);

        foreach ($threadsFollowersResult as $row) {
            $row->delete();
        }

        foreach ($stringExplode as $follower) {
            if (!empty($follower)) {
                $threadFollowerBan = new ThreadsFollowersBan();
                $threadFollowerBan->thread_id = $threadId;
                $threadFollowerBan->follower_name = $follower;

                if ($threadFollowerBan->insert() === false) {
                    throw new \Exception('Error while adding thread');
                }
            }
        }
    }
}
