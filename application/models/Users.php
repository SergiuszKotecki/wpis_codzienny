<?php
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $login
 * @property string $account_key
 * @property string $auth_key
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Threads[] $threads
 * @property ThreadsModerators[] $threadsModerators
 * @property Threads[] $threads0
 * @property ThreadsRows[] $threadsRows
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'account_key'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['login', 'account_key'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 75],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'account_key' => 'Account Key',
            'auth_key' => 'Auth Key',
            'image' => 'Image',
            'created_at' => 'Date Create',
            'updated_at' => 'Date Update',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getThreads()
    {
        return $this->hasMany(Threads::className(), ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreadsModerators()
    {
        return $this->hasMany(ThreadsModerators::className(), ['moderator_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getThreads0()
    {
        return $this->hasMany(Threads::className(), ['id' => 'thread_id'])->viaTable('threads_moderators', ['moderator_id' => 'id']);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
