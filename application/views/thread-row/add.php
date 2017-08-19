<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Enum\ThreadsRowsEnum;

$this->title = 'WpisCodzienny - Dodanie nowego wpisu';
$this->params['breadcrumbs'][] = ['label' => $thread->name, 'url' => '/thread/view/' . $thread->id];
$this->params['breadcrumbs'][] = ['label' => 'Lista wpisów', 'url' => '/thread-row/list/' . $thread->id];
$this->params['breadcrumbs'][] = 'Dodanie nowego wpisu';


$statuses = (new ThreadsRowsEnum)->getBegining();
$model->status = ThreadsRowsEnum::STATUS_READY_TO_SEND;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Dodanie nowego wpisu</h4></div>
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

<script src="/js/jquery.wysibb.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/css/wbbtheme.css" type="text/css" />

<script>
    $(function () {
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
                        "<a class=\"showSpoiler\">pokaż spoiler</a>\r\n\<code class=\"dnone\">{SELTEXT}</code>":'\! {SELTEXT}'
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