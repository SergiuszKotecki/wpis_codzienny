<?php
namespace app\models\Repository;
use app\models\Enum\ThreadsEnum;
use app\models\Enum\ThreadsRowsEnum;

class ThreadsRepository
{
    public function getAllPopular($limit = 5)
    {
        $query = "
            SELECT
                t.id,
                u.image,
                u.login,
                t.name,
                (SELECT COUNT(tr.id) FROM threads_rows tr WHERE t.id = tr.thread_id) as cnt
            FROM
                threads t
                JOIN users u ON (u.id = t.user_id)
            WHERE
                t.status != 2
            ORDER BY
                cnt DESC
            LIMIT $limit";

        return \Yii::$app->db->createCommand($query)->queryAll();
    }

    public function getNewest($limit = 5)
    {
        $query = "
            SELECT
                t.id,
                u.image,
                u.login,
                t.name,
                (SELECT COUNT(tr.id) FROM threads_rows tr WHERE t.id = tr.thread_id) as cnt
            FROM
                threads t
                LEFT JOIN users u ON (u.id = t.user_id)
            WHERE
                t.status != 2
            ORDER BY
                t.id DESC
            LIMIT $limit";

        return \Yii::$app->db->createCommand($query)->queryAll();
    }

    public function getUserThreads()
    {
        $userId = \Yii::$app->user->identity->id;

        $query = "
            SELECT
                t.id,
                u.image,
                u.login,
                t.name,
                t.hour_send,
                t.status,
                (SELECT COUNT(tr.id) FROM threads_rows tr WHERE t.id = tr.thread_id) as cnt_total,
                (SELECT COUNT(tr.id) FROM threads_rows tr WHERE t.id = tr.thread_id AND tr.status IN (0,1) AND tr.sent_at IS NULL) as cnt_wait
            FROM
                threads t
                LEFT JOIN users u ON (u.id = t.user_id)
            WHERE
                t.user_id = :user_id AND
                t.status != 2
            ORDER BY t.id DESC";

        return \Yii::$app->db->createCommand($query, ['user_id' => $userId])->queryAll();
    }


    public function getModeratorThreads()
    {
        $userId = \Yii::$app->user->identity->id;

        $query = "
            SELECT
                t.id,
                u.image,
                u.login,
                t.name,
                t.hour_send,
                t.status,
                (SELECT COUNT(tr.id) FROM threads_rows tr WHERE t.id = tr.thread_id) as cnt_total,
                (SELECT COUNT(tr.id) FROM threads_rows tr WHERE t.id = tr.thread_id AND tr.status IN (0,1) AND tr.sent_at IS NULL) as cnt_wait
            FROM
                threads t
                LEFT JOIN users u ON (u.id = t.user_id)
                LEFT JOIN threads_moderators tm ON (tm.thread_id = t.id)
            WHERE
                tm.moderator_id = :user_id AND
                t.status != 2";

        return \Yii::$app->db->createCommand($query, ['user_id' => $userId])->queryAll();
    }

    /**
     * @return array
     */
    public function getAllActiveThreads()
    {
        $query = "
            SELECT
                t.id, tr.author_id as user_id, t.flag_call_followers
            FROM
                threads t,
                threads_rows tr
            WHERE
                t.status = :thread_status AND
                EXTRACT(HOUR FROM t.hour_send) = :hour AND
                EXTRACT(MINUTE FROM t.hour_send) <= :minute AND
                tr.sent_at IS NULL AND
                tr.status = :thread_row_status AND
                tr.thread_id = t.id AND
                (SELECT count(trc.id) FROM threads_rows_cron trc WHERE trc.thread_id = t.id AND trc.created_at = :date) = 0 ";

        $result = \Yii::$app->db->createCommand($query, [
            'date' => date('Y-m-d'),
            'hour' => date('H'),
            'minute' => date('i'),
            'thread_status' => ThreadsEnum::STATUS_ACTIVE,
            'thread_row_status' => ThreadsRowsEnum::STATUS_READY_TO_SEND,
        ])->queryAll();

        $return = [];
        foreach ($result as $row) {
            $return[$row['user_id']][$row['id']] =  $row;
        }

        return $return;
    }

    /**
     *
     * @param int $userId
     * @return array|false
     */
    public function getThreadsCount($userId)
    {
        $query = "
            SELECT
                COUNT(t.id) as cnt
            FROM
                threads t
            WHERE
                t.user_id = :user_id AND
                t.status != 2";

        return \Yii::$app->db->createCommand($query, ['user_id' => $userId])->queryOne();
    }
}