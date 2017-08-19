<?php
use yii\helpers\Html;

$this->title = 'WpisCodzienny - Lista moderatorów';
$statuses = [
    0 => '<span style="color: red">Nieprzypisany</span>',
    1 => '<span style="color: green">Przypisany</span>',
];
?>

<div class="row">
    <div class="col-lg-7">
        <form action="/moderator/add/<?= $id; ?>" method="post" class="form-inline">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken()?>" />
            <div class="form-group">
                <input type="text" id="name" class="form-control" name="name" autofocus="" placeholder="Nazwa użytkownika">
            </div>
            <input type="submit" name="submit" class="btn btn-primary" value="Dodaj moderatora">
        </form>
    </div>
</div>
<br />
<div class="alert alert-info" role="alert">
    W celu dodania moderatorów, wyślij im link zamieszczony w poniższej tabelce.
    Jeżeli zalogują się do aplikacji z wysłanego do nich linku, pole status zmieni się na "Przypisany"
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Moderatorzy</h4></div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Lp</th>
                            <th>Użytkownik</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model as $i => $row) : ?>
                        <tr>
                            <td><?= $i+1; ?></td>
                            <td><?= Html::encode($row->moderator_name); ?></td>
                            <td>
                                <?=
                                    implode('/', [
                                        Yii::$app->getRequest()->hostInfo,
                                        'm',
                                        $row->moderator_hash
                                    ]);
                                ?>
                            </td>
                            <td><?= $statuses[$row->status]; ?></td>
                            <td>
                                <ul class="list-inline">
                                    <li>
                                        <form action="/moderator/delete/<?= $id; ?>" method="post">
                                            <input type="hidden" name="id" value="<?= $row->id; ?>" />
                                            <input type="hidden" name="hash" value="<?= Html::encode($row->moderator_hash); ?>" />
                                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken()?>" />
                                            <button style="border: none; background: none; color:red" title="Usuń moderatora" onclick="return confirm('Czy napewno chcesz usunąć moderatora?')">
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