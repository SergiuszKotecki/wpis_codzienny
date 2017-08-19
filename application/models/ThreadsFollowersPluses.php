<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_followers_pluses".
 *
 * @property integer $thread_id
 * @property string $follower_name
 *
 * @property Threads $thread
 */
class ThreadsFollowersPluses extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_followers_pluses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_id', 'follower_name'], 'required'],
            [['thread_id'], 'integer'],
            [['follower_name'], 'string', 'max' => 50],
            [['thread_id'], 'exist', 'skipOnError' => true, 'targetClass' => Threads::className(), 'targetAttribute' => ['thread_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'thread_id' => 'Thread ID',
            'follower_name' => 'Follower Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getThread()
    {
        return $this->hasOne(Threads::className(), ['id' => 'thread_id']);
    }
}