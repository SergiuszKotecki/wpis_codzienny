<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_rows_comments_cnt".
 *
 * @property integer $thread_row_id
 * @property integer $hour
 * @property integer $cnt
 *
 * @property ThreadsRows $threadRow
 */
class ThreadsRowsCommentsCnt extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_rows_comments_cnt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_row_id', 'hour'], 'required'],
            [['thread_row_id', 'hour', 'cnt'], 'integer'],
            [['thread_row_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThreadsRows::className(), 'targetAttribute' => ['thread_row_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'thread_row_id' => 'ID',
            'hour' => 'Godzina',
            'cnt' => 'Ilość',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadRow()
    {
        return $this->hasOne(ThreadsRows::className(), ['id' => 'thread_row_id']);
    }
}