<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_moderators_hash".
 *
 * @property integer $id
 * @property integer $thread_id
 * @property string $moderator_name
 * @property string $moderator_hash
 * @property integer $status
 *
 * @property Threads $thread
 */
class ThreadsModeratorsHash extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_moderators_hash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_id', 'moderator_name', 'moderator_hash', 'status'], 'required'],
            [['thread_id', 'status'], 'integer'],
            [['moderator_name', 'moderator_hash'], 'string', 'max' => 50],
            [['thread_id'], 'exist', 'skipOnError' => true, 'targetClass' => Threads::className(), 'targetAttribute' => ['thread_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'thread_id' => 'Thread ID',
            'moderator_name' => 'Użytkownik',
            'moderator_hash' => 'Hash',
            'status' => 'Status'
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