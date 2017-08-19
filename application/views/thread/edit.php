<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Enum\ThreadsEnum;

$this->title = 'WpisCodzienny - Edytuj temat';
$this->params['breadcrumbs'][] = 'Edytuj temat';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Edytuj temat</h4></div>
            <div class="panel-body">
                <?php
                    $statuses = (new ThreadsEnum)->getBegining();

                    $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'options' => ['class' => 'form-horizontal'],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-5\">{input}</div>\n<div class=\"col-lg-5\">{error}</div>",
                            'labelOptions' => ['class' => 'col-lg-2 control-label'],
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                    <?= $form->field($model, 'hour_send')->textInput(); ?>

                    <?= $form->field($model, 'status')->dropDownList($statuses); ?>

                    <?= $form->field($model, 'field_call_followers')->textarea(['rows' => 10, 'cols' => 10]) ?>

                    <?= $form->field($model, 'flag_call_followers')->checkbox([
                        'template' => "<div class=\"col-lg-offset-2 col-lg-5\">{input} {label}</div>\n<div class=\"col-lg-5\">{error}</div>",
                    ]) ?>

                    <div class="col-lg-offset-2 col-lg-10 alert alert-warning" id="flag_call_followers_info" style="display: none">
                        <strong>Uwaga!</strong>
                        <ul>
                            <li>
                                Opcja "Wołaj plusujących", umożliwia wołanie osób, które zaplusują jedynie wpisy dodane poprzez WpisCodzienny
                            </li>
                            <li>
                                Konto użytkownika ma limit wołań/komentarz oraz limit dodawania komentarzy/15min,
                                więc może się zdarzyć, że nie wszyscy plusujący zostaną zawołani
                            </li>
                            <li>
                                Pamiętaj że wołanie plusujących, może ich zdenerwować, o ile to możliwe rozważ inną opcję wołania użytkowników
                            </li>
                        </ul>
                    </div>

                    <div id="flag_call_followers_ban" style="display: none">
                        <?= $form->field($model, 'field_call_followers_ban')->textarea(['rows' => 10, 'cols' => 10]) ?>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <?= Html::submitButton('Zapisz zmiany', ['class' => 'btn btn-primary', 'name' => 'edit-button']) ?>
                        </div>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/css/wickedpicker.min.css" type="text/css" />
<script type="text/javascript" src="/js/wickedpicker/wickedpicker.min.js"></script>
<script>
    $(function() {
        if ($('#addnewthreadform-flag_call_followers').is(':checked')) {
            $('#flag_call_followers_info').show();
            $('#flag_call_followers_ban').show();
        }

        $('#addnewthreadform-flag_call_followers').click(function () {
            if ($(this).is(':checked')) {
                $('#flag_call_followers_info').show();
                $('#flag_call_followers_ban').show();
            } else {
                $('#flag_call_followers_info').hide();
                $('#flag_call_followers_ban').hide();
            }
        });

        var options = {
            now: "<?= $model->hour_send; ?>",
            twentyFour: true,
            close: 'wickedpicker__close',
            upArrow: 'wickedpicker__controls__control-up',
            downArrow: 'wickedpicker__controls__control-down',
            hoverState: 'hover-state',
            showSeconds: false,
            beforeShow: null,
            minutesInterval: 5
        };
        $('#addnewthreadform-hour_send').wickedpicker(options);
    });
</script>