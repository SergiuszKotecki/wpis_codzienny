<?php
use yii\helpers\Html;
use app\models\Repository\ThreadsRowsPlusesRepository;
use app\models\Repository\ThreadsRowsCommentsCntRepository;
use app\components\Helpers\BbCodeParser;
use app\models\Enum\ThreadsRowsEnum;

$this->title = 'WpisCodzienny - Szczegóły wpisu';
$this->params['breadcrumbs'][] = ['label' => $model->getThread()->one()->name, 'url' => '/thread/view/' . $model->thread_id];
$this->params['breadcrumbs'][] = ['label' => 'Lista wpisów', 'url' => '/thread-row/list/' . $model->thread_id];
$this->params['breadcrumbs'][] = 'Szczegóły wpisu';

$statuses = (new ThreadsRowsEnum)->getAll();

$pluses = json_encode((new ThreadsRowsPlusesRepository)->getPlusesIn24h($model->id));
$comments = json_encode((new ThreadsRowsCommentsCntRepository)->getCommentsIn24h($model->id));
?>

<div class="row">
    <div class="col-lg-12">
        <?php if (array_key_exists($model->status, (new ThreadsRowsEnum)->getBegining())) : ?>
            <p><?= Html::a('Edytuj wpis', ['thread-row/edit/'.$model->id], ['class' => 'btn btn-success']); ?></p>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Szczegóły wpisu</h4></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <th class="col-lg-4">Treść wpisu</th>
                            <td class="col-lg-8"><?= BbCodeParser::parse($model->body_text); ?></td>
                        </tr>
                        <tr>
                            <th>Link do multimediów</th>
                            <td>
                                <?= ($model->body_embedded)
                                        ? Html::a($model->body_embedded, $model->body_embedded, ['target' => '_blank'])
                                        : '---';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Uploadowany obraz</th>
                            <td>
                                <?= ($model->body_embedded_file)
                                        ? Html::img('/uploads/200_'.$model->body_embedded_file, ['style' => 'max-width:220px'])
                                        : '---';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?= $statuses[$model->status]; ?>
                            </td>
                        </tr>
                        <?php if ($model->sent_at) : ?>
                        <tr>
                            <th>Data wysłania</th>
                            <td>
                                <?= Html::encode($model->sent_at); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Data utworzenia</th>
                            <td>
                                <?= ($model->created_at) ? Html::encode($model->created_at) : '---'; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($model->status == ThreadsRowsEnum::STATUS_SEND) : ?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Zebrane plusy <span style="color:grey; font-size: 10px">(na przestrzeni pierwszych 24h)</span></h4></div>
            <div class="panel-body">
                <div id="pluses_chart"></div>
            </div>
        </div>
    </div>
</div>

<?php /*
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Ilość komentarzy <span style="color:grey; font-size: 10px">(na przestrzeni pierwszych 24h)</span></h4></div>
            <div class="panel-body">
                <div id="faved_chart"></div>
            </div>
        </div>
    </div>
</div> */ ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(function () { return drawChart(document.getElementById('pluses_chart'), <?= $pluses; ?>);});
    <?php /*google.charts.setOnLoadCallback(function () { return drawChart(document.getElementById('faved_chart'), <?= $comments; ?>); }); */ ?>

    function drawChart(element, d) {

        var data = google.visualization.arrayToDataTable(d);
        var hour = '<?= (new \DateTime($model->sent_at))->format('H'); ?>:00:00';
        var options = {
            curveType: 'function',
            legend: {position: 'none'}
        };

        if (data.getNumberOfRows() == 0) { // if you have no data, add a data point and make the series transparent
            data.addRow([hour, 100])
            options.series = {
                0: {
                    color: 'transparent'
                }
            }
        }

        var chart = new google.visualization.LineChart(element);
        chart.draw(data, options);
    }
</script>
<?php endif; ?>

<script>
    $(function () {
        $('.showSpoiler').on('click', function () {
            $(this).next('code').css('display', 'inline');
            $(this).css('display', 'none');
        });
    });
</script>