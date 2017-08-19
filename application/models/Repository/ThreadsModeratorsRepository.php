<?php
namespace app\models\Repository;
use yii\helpers\ArrayHelper;

class ThreadsModeratorsRepository
{
    public function getAllModeratorsFromUserThreads()
    {
        $userId = \Yii::$app->user->identity->id;

        $query = "
            SELECT
                u.*
            FROM
                users u
                JOIN threads_moderators tm ON (tm.moderator_id = u.id)
                JOIN threads t ON (t.id = tm.thread_id AND t.user_id = :user_id AND t.status != 2)
            ";

        $result = \Yii::$app->db->createCommand($query, ['user_id' => $userId])->queryAll();

        return ArrayHelper::map($result, 'id', 'login');
    }
}