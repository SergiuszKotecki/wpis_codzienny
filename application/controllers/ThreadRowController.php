<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Forms\AddNewThreadRowForm;
use app\models\Threads;
use app\models\ThreadsRows;
use app\models\ThreadsModerators;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\UploadedFile;
use app\models\Search\ThreadsRowsSearch;
use app\models\Enum\ThreadsEnum;

/**
 *
 */
class ThreadRowController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add', 'delete', 'view', 'edit', 'list'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add', 'delete', 'view', 'edit', 'list'],
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

    public function actionList()
    {
        $id = $this->_getThreadId();

        $thread = $this->loadModelForAll($id);
        $threadRowsId = ArrayHelper::map($thread->getThreadsRows()->all(), 'id', 'id');

        $searchModel = new ThreadsRowsSearch();
        $dataProvider = $searchModel->search($threadRowsId, Yii::$app->request->queryParams);

        return $this->render('list', [
            'thread' => $thread,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionAdd()
    {
        $id = $this->_getThreadId();

        $thread = $this->loadModelForAll($id);

        $model = new AddNewThreadRowForm();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->body_embedded_file = UploadedFile::getInstance($model, 'body_embedded_file');

                if ($model->validate() && $model->upload()) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    $threadRow = new ThreadsRows;
                    $threadRow->thread_id = $thread->id;
                    $threadRow->author_id = Yii::$app->user->id;
                    $threadRow->body_text = $model->body_text;
                    $threadRow->status = $model->status;
                    $threadRow->created_at = date('Y-m-d H:i:s');

                    if (!empty($model->body_embedded)) {
                        $threadRow->body_embedded = $model->body_embedded;
                    } else {
                        $threadRow->body_embedded_file = $model->body_embedded_file_name;
                    }

                    if ($threadRow->insert()) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('success', 'Pomyślnie dodano nowy wpis!');
                        return $this->redirect('/thread-row/view/' . $threadRow->id);
                    } else {
                        $transaction->rollBack();
                    }
                }
            }
            Yii::$app->getSession()->setFlash('danger', 'Nie udało się dodanie nowego wpisu!');
            return $this->redirect('/thread-row/add/' . $id);
        }

        return $this->render('add', [
            'thread' => $thread,
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
        $id = $this->_getThreadRowId();
        $threadRow = $this->loadThreadRowModelForAll($id);

        $model = new AddNewThreadRowForm();
        $model->body_text = $threadRow->body_text;
        $model->status = $threadRow->status;
        $model->body_embedded = $threadRow->body_embedded;
        $model->body_embedded_file_name = $threadRow->body_embedded_file;

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->body_embedded_file = UploadedFile::getInstance($model, 'body_embedded_file');

                if ($model->validate() && $model->upload()) {
                    $transaction = \Yii::$app->db->beginTransaction();

                    $threadRow->body_text = $model->body_text;
                    $threadRow->body_embedded = $model->body_embedded;
                    $threadRow->body_embedded_file = $model->body_embedded_file_name;
                    $threadRow->status = $model->status;

                    if (!empty($model->body_embedded)) {
                        if ($threadRow->body_embedded_file) {
                            if (file_exists(UPLOAD_PATH . $id . '/' . $threadRow->body_embedded_file)) {
                                if (!unlink(UPLOAD_PATH . $id . '/' . $threadRow->body_embedded_file)) {
                                    Yii::$app->getSession()->setFlash('danger', 'Nie udało się zapisać zmian1!');
                                    return $this->redirect('/thread-row/edit/' . $id);
                                }
                            }
                            if (file_exists(UPLOAD_PATH . $id . '/' . '200_' . $threadRow->body_embedded_file)) {
                                if (!unlink(UPLOAD_PATH . $id . '/' . '200_' . $threadRow->body_embedded_file)) {
                                    Yii::$app->getSession()->setFlash('danger', 'Nie udało się zapisać zmian2!');
                                    return $this->redirect('/thread-row/edit/' . $id);
                                }
                            }
                        }
                        $threadRow->body_embedded_file = null;
                    }

                    if ($threadRow->update() !== false) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('success', 'Zmiany zostały pomyślnie zapisane!');
                        return $this->redirect('/thread-row/view/' . $id);
                    } else {
                        $transaction->rollBack();
                        var_dump($threadRow->getErrors());die;
                        Yii::$app->getSession()->setFlash('danger', 'Nie udało się zapisać zmian,!');
                        return $this->redirect('/thread-row/edit/' . $id);
                    }
                }
            }
            Yii::$app->getSession()->setFlash('danger', 'Nie udało się zapisać zmian.!');
            return $this->redirect('/thread-row/edit/' . $id);
        }

        return $this->render('edit', [
            'thread' => $threadRow->getThread()->one(),
            'threadRow' => $threadRow,
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

        $id = $this->_getThreadRowId('post');
        $threadRow = $this->loadThreadRowModelForAll($id);
        $threadRow->status = \app\models\Enum\ThreadsRowsEnum::STATUS_DELETED;

        if (!$threadRow->save()) {
            Yii::$app->getSession()->setFlash('danger', 'Operacja usunięcia wpisu, niepowiodła się!');
            return $this->redirect('/thread-row/list/' . $threadRow->thread_id);
        }

        Yii::$app->getSession()->setFlash('success', 'Wpis został pomyślnie usunięty!');
        return $this->redirect('/thread-row/list/' . $threadRow->thread_id, 200);
    }

    /**
     * @return string
     */
    public function actionView()
    {
        $id = $this->_getThreadRowId();
        $threadRow = $this->loadThreadRowModelForAll($id);

        return $this->render('view', [
            'model' => $threadRow
        ]);
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
     * @param $id
     * @return null|ThreadsRows
     * @throws NotFoundHttpException
     */
    public function loadThreadRowModelForAll($id)
    {
        $threadRow = ThreadsRows::findOne(['id' => $id]);

        $thread = $threadRow->getThread()->one();

        if ($thread->user_id != Yii::$app->user->id) {

            $threadModerator = ThreadsModerators::findOne(['thread_id' => $thread->id, 'moderator_id' => Yii::$app->user->id]);

            if (!$threadModerator) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        if ($thread->status == ThreadsEnum::STATUS_DELETED) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }


        return $threadRow;
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

    /**
     * Method will return thread row id from get
     *
     * @return int
     */
    private function _getThreadRowId($method = 'get')
    {
        $id = Yii::$app->request->{$method}('thread_row_id');
        if (!$id) {
            Yii::$app->getSession()->setFlash('danger', 'Przekazano nieprawidłowe parametry!');
            return $this->redirect('/');
        }

        return $id;
    }
}
