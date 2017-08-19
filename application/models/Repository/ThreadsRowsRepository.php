<?php
namespace app\models\Repository;
use app\models\Enum\ThreadsRowsEnum;

class ThreadsRowsRepository
{
    public function getAllActiveThreadsRows($threadId)
    {
        $query = "
            SELECT
                tr.*
            FROM
                threads_rows tr
            WHERE
                tr.status = :status AND
                tr.sent_at IS NULL AND
                tr.thread_id = :thread_id
            ORDER BY tr.id ASC
            LIMIT 1";

        return \Yii::$app->db->createCommand($query, ['thread_id' => $threadId, 'status' => ThreadsRowsEnum::STATUS_READY_TO_SEND])->queryOne();
    }

    public function getThreadsRows($threadId, $limit = 10)
    {
        $query = "
            SELECT
                tr.*
            FROM
                threads_rows tr
            WHERE
                tr.thread_id = :thread_id
            ORDER BY tr.id DESC
            LIMIT :limit";

        return \Yii::$app->db->createCommand($query, ['thread_id' => $threadId, 'limit' => $limit])->queryAll();
    }

    public function getAllSendThreadsRowsForInfo()
    {
        $query = "
            SELECT
                tr.*
            FROM
                threads_rows tr
            WHERE
                tr.status IN (2, 3) AND
                tr.sent_at IS NOT NULL AND
                tr.thread_row_id IS NOT NULL AND
                (SELECT count(trp.thread_row_id) FROM threads_rows_pluses trp WHERE trp.thread_row_id = tr.id) < 24 AND
                (SELECT count(trp2.thread_row_id) FROM threads_rows_pluses trp2 WHERE trp2.thread_row_id = tr.id AND trp2.hour = :hour) = 0
            ORDER BY tr.id ASC";

        return \Yii::$app->db->createCommand($query, ['hour' => date('G')])->queryAll();
    }

    public function getAllSendThreadsRows()
    {
        $query = "
            SELECT
                tr.*
            FROM
                threads_rows tr
                JOIN threads t ON (t.id = tr.thread_id)
            WHERE
                tr.status IN (2, 3) AND
                tr.sent_at IS NOT NULL AND
                tr.thread_row_id IS NOT NULL AND
                t.flag_call_followers = 1";

        return \Yii::$app->db->createCommand($query)->queryAll();
    }
}