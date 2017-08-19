<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_moderators".
 *
 * @property integer $thread_id
 * @property integer $moderator_id
 *
 * @property Threads $thread
 */
class ThreadsModerators extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_moderators';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_id', 'moderator_id'], 'required'],
            [['thread_id', 'moderator_id'], 'integer'],
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
            'moderator_id' => 'Moderator ID',
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