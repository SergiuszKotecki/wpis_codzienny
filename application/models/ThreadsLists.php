<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_lists".
 *
 * @property integer $id
 * @property integer $thread_id
 * @property string $hash
 * @property string $created_at
 *
 * @property Threads $thread
 */
class ThreadsLists extends ActiveRecord
{
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_lists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_id', 'hash'], 'required'],
            [['thread_id'], 'integer'],
            [['created_at'], 'safe'],
            [['hash'], 'string', 'max' => 50],
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
            'hash' => 'Hash',
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
}