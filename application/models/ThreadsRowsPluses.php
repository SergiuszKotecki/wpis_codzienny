<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "threads_rows_pluses".
 *
 * @property integer $thread_row_id
 * @property integer $hour
 * @property integer $pluses
 *
 * @property ThreadsRows $threadRow
 */
class ThreadsRowsPluses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_rows_pluses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_row_id', 'hour'], 'required'],
            [['thread_row_id', 'hour', 'pluses'], 'integer'],
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
            'pluses' => 'Plusy',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThreadRow()
    {
        return $this->hasOne(ThreadsRows::className(), ['id' => 'thread_row_id']);
    }
}