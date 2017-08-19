<?php
use yii\helpers\Html;

$this->title = 'WpisCodzienny - Main';
?>

<div class="row">
    <div class="col-lg-12">
        <?php $cnt = $model->getThreadsCount(\Yii::$app->user->id); ?>
        <?php if ($cnt['cnt'] >= 5) : ?>
            <a href="#" class="btn btn-success disabled">
                Dodaj nowy temat <br />
                <span style="font-size: 10px">(Limit 5 tematów został osiągnięty)</span>
            </a>
        <?php else : ?>
            <a href="/thread/add" class="btn btn-success">
                Dodaj nowy temat
            </a>
        <?php endif; ?>
    </div>
</div>
<br />
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Twoje tematy</h4></div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Lp</th>
                            <th>Nazwa</th>
                            <th>Całkowita ilość wpisów</th>
                            <th>Ilość wpisów oczekujących</th>
                            <th>Godzina wysyłania</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model->getUserThreads() as $i => $row) : ?>
                        <tr>
                            <td><?= $i+1; ?></td>
                            <td>
                                <?php if (strlen($row['name']) > 25) : ?>
                                    <?= Html::encode(substr($row['name'], 0, 25) . ' (...)'); ?>
                                <?php else: ?>
                                    <?= Html::encode($row['name']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['cnt_total']; ?></td>
                            <td><?= $row['cnt_wait']; ?></td>
                            <td><?= Html::encode($row['hour_send']); ?></td>
                            <td><?=
                                    ($row['status'] == 1)
                                        ? '<span style="color: green">Aktywny</span>'
                                        : '<span style="color: red">Nieaktywny</span>';
                                ?>
                            </td>
                            <td>
                                <ul class="list-inline">
                                    <li>
                                        <a href="/thread-row/list/<?= Html::encode($row['id']); ?>" title="Lista wpisów">
                                            <i class="glyphicon glyphicon-menu-hamburger"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/thread/view/<?= Html::encode($row['id']); ?>" title="Szczegóły tematu">
                                            <i class="glyphicon glyphicon-eye-open"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/thread/edit/<?= Html::encode($row['id']); ?>" title="Edytuj temat">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/moderator/list/<?= Html::encode($row['id']); ?>" title="Lista moderatorów">
                                            <i class="glyphicon glyphicon-user"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="/thread/delete" method="post">
                                            <input type="hidden" name="id" value="<?= Html::encode($row['id']); ?>" />
                                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken()?>" />
                                            <button style="border: none; background: none; color:red" title="Usuń temat" onclick="return confirm('Czy napewno chcesz usunąć ten temat?')">
                                                <i class="glyphicon glyphicon-trash"></i>
                                            </button>
                                        </form>
                                    </li>
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

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Tematy, które moderujesz</h4></div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Lp</th>
                            <th>Autor</th>
                            <th>Nazwa</th>
                            <th>Całkowita ilość wpisów</th>
                            <th>Ilość wpisów oczekujących</th>
                            <th>Godzina wysyłania</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model->getModeratorThreads() as $i => $row) : ?>
                        <tr>
                            <td><?= $i+1; ?></td>
                            <td><?= Html::encode($row['login']); ?></td>
                            <td>
                                <?php if (strlen($row['name']) > 25) : ?>
                                    <?= Html::encode(substr($row['name'], 0, 25) . ' (...)'); ?>
                                <?php else: ?>
                                    <?= Html::encode($row['name']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['cnt_total']; ?></td>
                            <td><?= $row['cnt_wait']; ?></td>
                            <td><?= Html::encode($row['hour_send']); ?></td>
                            <td>
                                <?=
                                    ($row['status'] == 1)
                                        ? '<span style="color: green">Aktywny</span>'
                                        : '<span style="color: red">Nieaktywny</span>';
                                ?>
                            </td>
                            <td>
                                <ul class="list-inline">
                                    <li>
                                        <a href="/thread-row/list/<?= Html::encode($row['id']); ?>" title="Lista wpisów">
                                            <i class="glyphicon glyphicon-menu-hamburger"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/thread/view/<?= Html::encode($row['id']); ?>" title="Szczegóły tematu">
                                            <i class="glyphicon glyphicon-eye-open"></i>
                                        </a>
                                    </li>
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