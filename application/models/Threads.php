<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "threads".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $hour_send
 * @property integer $flag_call_followers
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 * @property ThreadsFollowers[] $threadsFollowers
 * @property ThreadsModerators[] $threadsModerators
 * @property Users[] $moderators
 * @property ThreadsModeratorsHash[] $threadsModeratorsHashes
 * @property ThreadsRows[] $threadsRows
 * @property ThreadsTemplates[] $threadsTemplates
 */
class Threads extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'threads';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name', 'hour_send'], 'required'],
            [['user_id', 'flag_call_followers', 'status'], 'integer'],
            [['hour_send', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Nazwa tematu',
            'hour_send' => 'Godzina wysłania',
            'flag_call_followers' => 'Wołaj plusujących',
            'status' => 'Status',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data modyfikacji',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsFollowers()
    {
        return $this->hasMany(ThreadsFollowers::className(), ['thread_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsModerators()
    {
        return $this->hasMany(ThreadsModerators::className(), ['thread_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getModerators()
    {
        return $this->hasMany(Users::className(), ['id' => 'moderator_id'])->viaTable('threads_moderators', ['thread_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsModeratorsHashes()
    {
        return $this->hasMany(ThreadsModeratorsHash::className(), ['thread_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsRows()
    {
        return $this->hasMany(ThreadsRows::className(), ['thread_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsTemplates()
    {
        return $this->hasMany(ThreadsTemplates::className(), ['thread_id' => 'id']);
    }
}