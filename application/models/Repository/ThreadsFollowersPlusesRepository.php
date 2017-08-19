<?php
namespace app\models\Repository;
use yii\helpers\ArrayHelper;

class ThreadsFollowersPlusesRepository
{
    /**
     * @param $threadId
     * @return array
     */
    public function getAll($threadId)
    {
        $query = "
            SELECT
                tfp.*
            FROM
                threads_followers_pluses tfp
            WHERE
                tfp.thread_id = :threadId
            ";

        $result = \Yii::$app->db->createCommand($query, ['threadId' => $threadId])->queryAll();

        return ArrayHelper::map($result, 'follower_name', 'follower_name');
    }
}