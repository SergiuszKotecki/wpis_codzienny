<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use Wkop\Factory;

class TestController extends Controller
{
    private $_config;

    public function beforeAction($action)
    {
        $this->_config = Yii::$app->params['wykop_api'];

        return parent::beforeAction($action);
    }

    public function actionEntry()
    {
        $client = Factory::get($this->_config['app_key'], $this->_config['secret_key']);
        $client->setUserCredentials('AlvarezCasares', 'rfyryQuZdr7dsoGWRu1p');

        if ($client->logIn()) {
            $apiParams = [
                'appkey' => $this->_config['app_key'],
                'userkey' => 'GtLoB0W2H7'
            ];
            $body = [
                'body' => '#test test',
                'embed' => 'http://xj.cdn02.imgwykop.pl/c3397992/AlvarezCasarez_JmwUM4ZMA4,q40.jpg',
            ];
            var_dump($client->post('entries', ['add'], $apiParams, $body));
        }
    }
}
