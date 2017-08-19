<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Enum\ThreadsRowsEnum;

$this->title = 'WpisCodzienny - Edycja wpisu';
$this->params['breadcrumbs'][] = ['label' => $thread->name, 'url' => '/thread/view/' . $thread->id];
$this->params['breadcrumbs'][] = ['label' => 'Lista wpisów', 'url' => '/thread-row/list/' . $thread->id];
$this->params['breadcrumbs'][] = 'Edycja wpisu';

$statuses = (new ThreadsRowsEnum)->getBegining();
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Edycja wpisu</h4></div>
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

                <?= $form->field($model, 'status')->dropDownList($statuses); ?>

                <?= $form->field($model, 'body_embedded')->textInput(); ?>

                <?php if ($model->body_embedded_file_name) : ?>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <?= Html::img('/uploads/' . $model->body_embedded_file_name, ['style' => 'width:220px']); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?= $form->field($model, 'body_embedded_file')->fileInput()->label('Wgraj plik'); ?>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <span class="help-block">Podaj link do multimediów lub wgraj plik obrazu (max 2Mb, format:PNG, GIF, JPG)</span>
                    </div>
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

<script src="/js/jquery.wysibb.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/css/wbbtheme.css" type="text/css" />
<script>
    $(function () {
        $('input[name="AddNewThreadRowForm[body_embedded_file]"]').change(function () {
            if ($(this).val() != '') {
                $('#addnewthreadrowform-body_embedded').val('');
            }
        });
        $('#addnewthreadrowform-body_embedded').change(function () {
            if ($(this).val() != '') {
                $('input[name="AddNewThreadRowForm[body_embedded_file]"]').val('');
            }
        });

        var wbbOpt = {
            buttons: "bold,italic,|,link,|,code,spoiler",
            allButtons: {
                bold: {
                    transform: {
                        '<b>{SELTEXT}</b>':'\*\*{SELTEXT}\*\*'
                    }
                },
                italic: {
                    transform: {
                        '<i>{SELTEXT}</i>':'_{SELTEXT}_'
                    }
                },
                code: {
                    transform: {
                        '<code>{SELTEXT}</code>':'`{SELTEXT}`'
                    }
                },
                link: {
                    transform: {
                        '<a href="{URL}">{SELTEXT}</a>':'\[{SELTEXT}\]\({URL}\)'
                    }
                },
                spoiler: {
                    title: 'Spoiler',
                    buttonText: 'spoiler',
                    transform: {
                        "<a class=\"showSpoiler\">pokaż spoiler</a>\r\n<code class=\"dnone\">{SELTEXT}</code>":'\! {SELTEXT}'
                    }
                }
            }
        };
        $("#addnewthreadrowform-body_text").wysibb(wbbOpt);

        $('.showSpoiler').click(function () {
            $(this).closest('.dnone').show();
            $(this).hide();
        });
    });
</script>