<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use Wkop\Helpers;

AppAsset::register($this);

$config = Yii::$app->params['wykop_api'];
$loginUrl = Helpers::getConnectUrl($config['redirect_url'], $config['app_key'], $config['secret_key']);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'WpisCodzienny',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => 'Pomoc', 'url' => ['/help']],
                    ['label' => 'Kontakt', 'url' => ['/contact']],
                    Yii::$app->user->isGuest ? (
                        ['label' => 'Login', 'url' => $loginUrl]
                        ) : (
                        '<li>'
                        . Html::beginForm(['/logout'], 'post', ['class' => 'navbar-form'])
                        . Html::submitButton(
                            'Wyloguj (' . Yii::$app->user->identity->login . ')', ['class' => 'btn btn-link']
                        )
                        . Html::endForm()
                        . '</li>'
                        )
                ],
            ]);
            NavBar::end();
            ?>

            <div class="container">

            <?php
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);

                if (!empty(Yii::$app->session->getAllFlashes())) {
                    echo '<div class="row">';
                        echo '<div class="col-lg-12">';
                        foreach (Yii::$app->session->getAllFlashes() as $type => $row) {
                            echo '<div class="alert alert-'.$type.'">' . $row . '</div>';
                        }
                        echo '</div>';
                    echo '</div>';
                }

                echo $content;
            ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; WpisCodzienny <?= date('Y') ?></p>
                <p class="pull-right">Autor <a href="http://www.wykop.pl/ludzie/AlvarezCasarez/">AlvarezCasarez</a></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
