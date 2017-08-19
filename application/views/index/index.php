<?php

/* @var $this yii\web\View */

$this->title = 'WpisCodzienny - Main';
?>
<div class="site-index">
    <div class="jumbotron">
        <h2>Czym jest "WpisCodzienny"?</h2>
        <p class="lead">Jest to prosta aplikacja, służąca do automatycznego dodawania wpisów na Mikroblogu, w serwisie Wykop.pl</p>
        <p><a class="btn btn-sm btn-success" href="<?= $url; ?>">Dołącz do nas</a></p>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4>Popularne</h4></div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Lp</th>
                                    <th>Autor</th>
                                    <th>Nazwa</th>
                                    <th>Wpisów</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->getAllPopular(5) as $i => $row) : ?>
                                <tr>
                                    <td><?= $i+1; ?></td>
                                    <td><?= $row['login']; ?></td>
                                    <td><?= $row['name']; ?></td>
                                    <td><?= $row['cnt']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><h4>Ostatnio dodane</h4></div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Lp</th>
                                    <th>Autor</th>
                                    <th>Nazwa</th>
                                    <th>Wpisów</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->getNewest(5) as $i => $row) : ?>
                                <tr>
                                    <td><?= $i+1; ?></td>
                                    <td><?= $row['login']; ?></td>
                                    <td><?= $row['name']; ?></td>
                                    <td><?= $row['cnt']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
