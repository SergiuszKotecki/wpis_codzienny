<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_rows_cron".
 *
 * @property integer $id
 * @property integer $thread_id
 * @property integer $thread_row_id
 * @property string $created_at
 *
 * @property Threads $thread
 * @property ThreadsRows $threadRow
 */
class ThreadsRowsCron extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_rows_cron';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_id', 'thread_row_id'], 'required'],
            [['thread_id', 'thread_row_id'], 'integer'],
            [['created_at'], 'safe'],
            [['thread_id'], 'exist', 'skipOnError' => true, 'targetClass' => Threads::className(), 'targetAttribute' => ['thread_id' => 'id']],
            [['thread_row_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThreadsRows::className(), 'targetAttribute' => ['thread_row_id' => 'id']],
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
            'thread_row_id' => 'Thread Row ID',
            'created_at' => 'Date Create',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getThread()
    {
        return $this->hasOne(Threads::className(), ['id' => 'thread_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadRow()
    {
        return $this->hasOne(ThreadsRows::className(), ['id' => 'thread_row_id']);
    }
}