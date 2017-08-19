<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Threads;
use app\models\ThreadsLists;
use app\models\Enum\ThreadsEnum;
use yii\web\NotFoundHttpException;

class ListController extends Controller
{
    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionGenerate()
    {
        $id = $this->_getThreadId();

        $thread = $this->loadModel($id);

        $threadList = ThreadsLists::findOne(['thread_id' => $thread->id]);

        if (is_null($threadList)) {
            $threadList = new ThreadsLists;
            $threadList->thread_id = $thread->id;
            $threadList->hash = substr(md5($thread->id), 0, 15);

            if (!$threadList->insert()) {
                var_dump($threadList);die;
                Yii::$app->getSession()->setFlash('danger', 'Nie udało się wygenerowanie nowej listy!');
                return $this->redirect('/');
            }
        }

        return $this->render('generate', [
            'model' => $threadList
        ]);
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
}
