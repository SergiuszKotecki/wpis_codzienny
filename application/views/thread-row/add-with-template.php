<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Dodaj nowy wpis';
$this->params['breadcrumbs'][] = ['label' => 'Lista wpisów', 'url' => '/thread-row/list/' . $thread->id];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><?= Html::encode($this->title) ?></h4></div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-7\">{input}</div>\n<div class=\"col-lg-3\">{error}</div>",
                            'labelOptions' => ['class' => 'col-lg-2 control-label'],
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'body_text')->textarea(['autofocus' => true, 'rows' => 10, 'cols' => 10]) ?>

                    <?= $form->field($model, 'body_embedded')->textInput(); ?>
                    <?= $form->field($model, 'body_embedded_file')->fileInput(); ?>


                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'add-button']) ?>
                        </div>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>