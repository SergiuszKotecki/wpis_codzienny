<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\models\Enum\ThreadsRowsEnum;
use app\components\Helpers\BbCodeParser;

$this->title = 'Lista wpisów';
$this->params['breadcrumbs'][] = ['label' => $thread->name, 'url' => '/thread/view/' . $thread->id];
$this->params['breadcrumbs'][] = $this->title;

$statuses = (new ThreadsRowsEnum)->getAll();
?>
<div class="row">
    <div class="col-lg-12">
        <p><?= Html::a('Dodaj nowy wpis', ['thread-row/add/'.$thread->id], ['class' => 'btn btn-success']); ?></p>
        <div class="panel panel-default">
            <div class="panel-heading"><h4><?= Html::encode($this->title); ?></h4></div>
            <div class="panel-body">
                <div style="overflow: auto">
                <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'rowOptions' => function ($model, $index, $widget, $grid){
                        return ['style' => (new ThreadsRowsEnum)->getRowStyles()[$model->status]];
                    },
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'contentOptions' => ['style' => 'width:50px;'],
                        ],
                        [
                            'attribute' => 'author',
                            'label' => 'Autor',
                            'value' => 'author.login',
                        ],
                        [
                            'attribute' => 'body_text',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $substr = substr($model->body_text, 0, 150);
                                if (strlen($substr) === 150) {
                                    $substr .= ' (...)';
                                }
                                return BbCodeParser::parse($substr);
                            },
                            'contentOptions' => ['style' => 'width:400px; min-width:200px'],
                        ],
                        [
                            'attribute' => 'body_embedded',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if ($model->body_embedded) {
                                    $text = substr($model->body_embedded, 0, 20);
                                    return Html::a($text, $model->body_embedded);
                                }
                                return '---';
                            }
                        ],
                        [
                            'attribute' => 'body_embedded_file',
                            'label' => 'Załączony obraz',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return ($model->body_embedded_file)
                                    ? yii\helpers\Html::img('/uploads/'.$model->body_embedded_file, ['style' => 'max-width:140px;max-height:90px'])
                                    : '---';
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'label' => 'Status',
                            'filter' => Html::activeDropDownList($searchModel, 'status', $statuses, ['class'=>'form-control', 'prompt' => 'Wybierz status']),
                            'value' => function ($model) use ($statuses) {
                                return $statuses[$model->status];
                            }
                        ],
                        'sent_at',
                        [
                            'attribute' => 'created_at',
                            'label' => 'Data utworzenia'
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visibleButtons' => [
                                'update' => function ($model, $key, $index) {
                                    return (array_key_exists($model->status, (new ThreadsRowsEnum)->getBegining()));
                                },
                                'delete' => function ($model, $key, $index) {
                                    return (array_key_exists($model->status, (new ThreadsRowsEnum)->getBegining()));
                                }
                            ],
                            'urlCreator' => function ($action, $model, $key, $index) {
                                switch ($action) {
                                    case 'update':
                                        return Url::toRoute(['/thread-row/edit/' . $model->id]);
                                    case 'view':
                                        return Url::toRoute(['/thread-row/view/' . $model->id]);
                                    case 'delete':
                                        return Url::toRoute(['/thread-row/delete/' . $model->id]);
                                    default:
                                        return Url::toRoute([$action, 'id' => $model->id]);
                                }
                            }
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>