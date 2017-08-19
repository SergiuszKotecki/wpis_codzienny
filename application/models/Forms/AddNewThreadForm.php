<?php

namespace app\models\Forms;
use Yii;
use yii\base\Model;
use yii\helpers\Html;
use app\models\Repository\ThreadsModeratorsRepository;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Users|null $user This property is read-only.
 *
 */
class AddNewThreadForm extends Model
{
    public $name;
    public $hour_send = '01';
    public $day_run = 1;
    public $status;
    public $flag_call_followers;
    public $field_call_followers;
    public $field_call_followers_ban;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'hour_send'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['day_run', 'status', 'flag_call_followers'], 'boolean'],
            ['field_call_followers', 'string', 'max' => 500],
            ['field_call_followers', 'validateFieldCallFollowers'],
            ['field_call_followers_ban', 'validateFieldCallFollowersBan'],
            ['hour_send', 'validateHourRun'],
        ];
    }

    public function attributeLabels()
    {
        $labels = (new \app\models\Threads)->attributeLabels();
        $labels['field_call_followers'] = 'Lista osób, którzy będą wołani';
        $labels['field_call_followers_ban'] = 'Lista plusujących, którzy nie będą wołani';

        return $labels;
    }

    public function validateHourRun($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->hour_send = strtr($this->hour_send, [' ' => '']);
            if (!strlen($this->hour_send) == 5 || !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $this->hour_send)) {
                $this->addError($attribute, 'Godzina wysyłania wpisu jest nieprawidłowa!');
            }
        }
    }

    public function validateFieldCallFollowers($attribute, $params)
    {
        if (!$this->hasErrors() && !empty($this->field_call_followers)) {
            $stringClean = strtr($this->field_call_followers, [',' => ' ', '  ' => ' ']);
            $stringClean = preg_replace('/[^a-zA-Z0-9_\-\,\ ]/', '', $stringClean);

            if (empty($stringClean)) {
                $this->addError($attribute, 'Niepoprawna wartość pola!');
            }

            $explode = explode(' ', $stringClean);
            if (count($explode) > 200) {
                $this->addError($attribute, 'Przekroczono limit 200 użytkowników, którzy zostaną wołani!');
            }

            foreach ($explode as $row) {
                if (strlen($row) > 20) {
                    $this->addError($attribute, 'Przekroczono limit 20 znaków nazwy użytkownika (' . Html::encode($row) . ')!');
                }
            }
        }
    }

    public function validateFieldCallFollowersBan($attribute, $params)
    {
        if (!$this->hasErrors() && !empty($this->field_call_followers_ban)) {
            $stringClean = strtr($this->field_call_followers_ban, [',' => ' ', '  ' => ' ']);
            $stringClean = preg_replace('/[^a-zA-Z0-9_\-\,\ ]/', '', $stringClean);

            if (empty($stringClean)) {
                $this->addError($attribute, 'Niepoprawna wartość pola!');
            }

            $explode = explode(' ', $stringClean);
            if (count($explode) > 200) {
                $this->addError($attribute, 'Przekroczono limit 200 użytkowników!');
            }

            foreach ($explode as $row) {
                if (strlen($row) > 20) {
                    $this->addError($attribute, 'Przekroczono limit 20 znaków nazwy użytkownika (' . Html::encode($row) . ')!');
                }
            }
        }
    }
}
