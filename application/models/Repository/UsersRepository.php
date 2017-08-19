<?php
namespace app\models\Repository;
use app\models\Users;

class UsersRepository
{
    public function findUserByLogin($login)
    {
        return Users::find()
            ->where('login = :login', ['login' => $login])
            ->one();
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
            ORDER BY
                t.id DESC
            LIMIT $limit";

        return \Yii::$app->db->createCommand($query)->queryAll();
    }
}