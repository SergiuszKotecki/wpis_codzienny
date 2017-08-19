<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Repository\ThreadsRowsRepository;
use app\models\Enum\ThreadsRowsEnum;
use app\models\ThreadsFollowersBan;

$this->title = 'WpisCodzienny - Szczegóły tematu';
$this->params['breadcrumbs'][] = 'Szczegóły tematu';

$statuses = (new ThreadsRowsEnum)->getAll();
$rowsStyles = (new ThreadsRowsEnum)->getRowStyles();

if ($model->flag_call_followers) {
    $followersBaned = (new ThreadsFollowersBan)->findAll(['thread_id' => $model->id]);
    $followersBanedArray = ArrayHelper::map($followersBaned, 'follower_name', 'follower_name');
}
$isOwner = ($model->user_id == \Yii::$app->user->id);
?>

<div class="row">
    <div class="col-lg-6">
        <?php if ($isOwner) : ?>
            <p><?= Html::a('Edytuj temat', ['thread/edit/'.$model->id], ['class' => 'btn btn-success']); ?></p>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Szczegóły tematu</h4></div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="col-lg-4">Nazwa tematu</th>
                            <td class="col-lg-8"><?= Html::encode($model->name); ?></td>
                        </tr>
                        <tr>
                            <th>Godzina wysyłania</th>
                            <td><?= Html::encode($model->hour_send); ?></td>
                        </tr>
                        <tr>
                            <th>Lista osób, którzy będą wołani</th>
                            <td>
                                <div style="max-height:220px; overflow-y: scroll">
                                    <ul>
                                    <?php foreach ($model->getThreadsFollowers()->all() as $row) : ?>
                                        <li>
                                            <?= Html::a($row->follower_name, 'http://www.wykop.pl/ludzie/'.$row->follower_name, ['target' => '_blank']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Wołanie plusujących</th>
                            <td><?= ($model->flag_call_followers) ? 'Tak' : 'Nie'; ?></td>
                        </tr>
                        <?php if ($model->flag_call_followers) : ?>
                        <tr>
                            <th>Lista plusujących, którzy nie będą wołani</th>
                            <td>
                                <div style="max-height:220px; overflow-y: scroll">
                                    <ul>
                                    <?php foreach ($followersBanedArray as $name) : ?>
                                        <li>
                                            <?= Html::a($name, 'http://www.wykop.pl/ludzie/'.$name, ['target' => '_blank']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($isOwner) : ?>
                                    <?php
                                        if (array_key_exists($model->status, (new ThreadsRowsEnum)->getBegining())) {
                                            echo '<input type="checkbox" id="status" name="status" '.(($model->status == 1) ? 'checked' : '').'>';
                                        } else {
                                            echo '<span style="color: red">Nieaktywny</span>';
                                        }
                                    ?>
                                <?php else: ?>
                                    <?= $statuses[$model->status]; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Date utworzenia</th>
                            <td>
                                <?php if ($model->created_at) : ?>
                                    <?= Html::encode($model->created_at); ?>
                                <?php else : ?>
                                    <span class="not-set">(not set)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Przypisani moderatorzy</th>
                            <td>
                                <ul>
                                <?php foreach ($moderators as $id => $name) : ?>
                                    <li>
                                        <?= Html::a($name, 'http://www.wykop.pl/ludzie/'.$name, ['target' => '_blank']); ?>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <p><?= Html::a('Dodaj nowy wpis', ['thread-row/add/'.$model->id], ['class' => 'btn btn-success']); ?></p>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Lista wpisów</h4> <span style="font-size: 10px">(ostatnich 10)</span>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lp</th>
                            <th>Tekst</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    <?php $threadRows = (new ThreadsRowsRepository)->getThreadsRows($model->id); ?>
                    <?php foreach ($threadRows as $threadRow) : ?>
                        <tr style="<?= $rowsStyles[$threadRow['status']]; ?>">
                            <td><?= $i++; ?></td>
                            <td><?= substr($threadRow['body_text'], 0 ,50); ?></td>
                            <td><?= $statuses[$threadRow['status']]; ?></td>
                            <td>
                                <ul class="list-inline">
                                    <li>
                                        <a href="/thread-row/view/<?= Html::encode($threadRow['id']); ?>" title="Szczegóły wpisu">
                                            <i class="glyphicon glyphicon-eye-open"></i>
                                        </a>
                                    </li>
                                    <?php if (array_key_exists($threadRow['status'], (new ThreadsRowsEnum)->getBegining())) : ?>
                                    <li>
                                        <a href="/thread-row/edit/<?= Html::encode($threadRow['id']); ?>" title="Edytuj wpis">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="/thread-row/delete" method="post">
                                            <input type="hidden" name="id" value="<?= Html::encode($threadRow['id']); ?>" />
                                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken()?>" />
                                            <button style="border: none; background: none; color:red" title="Usuń wpis" onclick="return confirm('Czy napewno chcesz usunąć ten wpis?')">
                                                <i class="glyphicon glyphicon-trash"></i>
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#status').change(function () {
            status = $(this).is(':checked') ? '0' : '1';
            self = $(this);

            $.ajax({
                method: "POST",
                url: "/thread/change-status",
                data: { id: "<?= $model->id; ?>", status: status }
            })
            .done(function(msg) {
                alert(msg);
            })
            .fail(function( jqXHR, textStatus ) {
                $(self).prop('checked', !$(self).is(':checked'));
                alert(textStatus);
            });
        });
    });
</script>