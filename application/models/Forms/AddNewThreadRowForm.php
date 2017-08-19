<?php

namespace app\models\Forms;
use Exception;
use Yii;
use yii\base\Model;
use app\components\Helpers\SimpleImage;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Users|null $user This property is read-only.
 *
 */
class AddNewThreadRowForm extends Model
{
    public $body_text;
    public $status;
    public $body_embedded;
    public $body_embedded_file;
    public $body_embedded_file_name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['body_text'], 'required'],
            [['body_text'], 'string', 'max' => 2000],
            [['status'], 'boolean'],
            [['body_embedded'], 'string', 'max' => 255],
            [['body_embedded'], 'url'],
            [
                ['body_embedded_file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, gif',
                'maxSize' => 1024 * 1024 * 2,
                'tooBig' => 'Obraz jest za duży, max rozmiar to 2MB'
            ],
        ];
    }

    public function attributeLabels()
    {
        return (new \app\models\ThreadsRows)->attributeLabels();
    }

    public function upload()
    {
        if (is_null($this->body_embedded_file)) {
            return true;
        }

        if ($this->validate()) {
            $fileName = md5($this->body_embedded_file->baseName);
            $this->body_embedded_file_name = $fileName . '.' . $this->body_embedded_file->extension;

            if ($this->body_embedded_file->saveAs(UPLOAD_PATH . $this->body_embedded_file_name)) {
                chmod(UPLOAD_PATH . $this->body_embedded_file_name, 0777);

                $this->_resizeImage(UPLOAD_PATH, $fileName, $this->body_embedded_file->extension);
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    private function _resizeImage($filePath, $fileName, $fileExtension)
    {
        try {
            $si = new SimpleImage($filePath . $fileName . '.' . $fileExtension);
            $si->fit_to_width('200');
            $si->save($filePath . '200_' . $fileName . '.' . $fileExtension, 90);
        } catch (Exception $e) {
            echo $e->getMessage();die;
            throw new Exception('Plik nie został zapisany!');
        }
    }
}
