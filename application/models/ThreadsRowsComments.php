<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads_rows_comments".
 *
 * @property integer $id
 * @property integer $thread_row_id
 * @property integer $author_id
 * @property integer $thread_row_comment_id
 * @property string $body_text
 * @property string $body_embedded
 * @property string $body_embedded_file
 * @property integer $sort_order
 * @property integer $status
 * @property string $sent_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ThreadsRows $threadRow
 * @property Users $author
 */
class ThreadsRowsComments extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads_rows_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['thread_row_id', 'author_id', 'body_text'], 'required'],
            [['thread_row_id', 'author_id', 'thread_row_comment_id', 'sort_order', 'status'], 'integer'],
            [['body_text'], 'string', 'max' => 2000],
            [['sent_at', 'created_at', 'updated_at'], 'safe'],
            [['body_embedded'], 'string', 'max' => 255],
            [['body_embedded_file'], 'string', 'max' => 75],
            [['thread_row_id'], 'exist', 'skipOnError' => true, 'targetClass' => ThreadsRows::className(), 'targetAttribute' => ['thread_row_id' => 'id']],
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
            'thread_row_id' => 'Thread Row ID',
            'author_id' => 'Autor',
            'body_text' => 'Treść wpisu',
            'body_embedded' => 'Link do multimediów',
            'body_embedded_file' => 'Wgraj plik',
            'sort_order' => 'Sortowanie',
            'status' => 'Status',
            'sent_at' => 'Data wysłania',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data zmiany',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadRow()
    {
        return $this->hasOne(ThreadsRows::className(), ['id' => 'thread_row_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Users::className(), ['id' => 'author_id']);
    }
}