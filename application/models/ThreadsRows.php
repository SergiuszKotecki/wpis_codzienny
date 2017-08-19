<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_rows".
 *
 * @property integer $id
 * @property integer $thread_id
 * @property integer $author_id
 * @property integer $thread_row_id
 * @property string $body_text
 * @property string $body_embedded
 * @property string $body_embedded_file
 * @property integer $status
 * @property string $sent_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Threads $thread
 * @property Users $author
 * @property ThreadsRowsComments[] $threadsRowsComments
 * @property ThreadsRowsPluses[] $threadsRowsPluses
 */
class ThreadsRows extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_rows';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_id', 'author_id', 'body_text', 'status'], 'required'],
            [['thread_id', 'author_id', 'thread_row_id', 'status'], 'integer'],
            [['body_text', 'body_embedded'], 'string', 'max' => 2000],
            [['sent_at', 'created_at', 'updated_at'], 'safe'],
            [['body_embedded'], 'string', 'max' => 255],
            [['body_embedded_file'], 'string', 'max' => 75],
            [['thread_id'], 'exist', 'skipOnError' => true, 'targetClass' => Threads::className(), 'targetAttribute' => ['thread_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['author_id' => 'id']],
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
            'author_id' => 'Author ID',
            'body_text' => 'Treść wpisu',
            'body_embedded' => 'Link do multimediów',
            'body_embedded_file' => 'Wgrany plik',
            'status' => 'Status',
            'sent_at' => 'Data wysłania',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data modyfikacji',
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
    public function getAuthor()
    {
        return $this->hasOne(Users::className(), ['id' => 'author_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsRowsComments()
    {
        return $this->hasMany(ThreadsRowsComments::className(), ['thread_row_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsRowsPluses()
    {
        return $this->hasMany(ThreadsRowsPluses::className(), ['thread_row_id' => 'id']);
    }
}