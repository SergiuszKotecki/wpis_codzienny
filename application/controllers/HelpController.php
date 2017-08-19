<?php

namespace app\controllers;
use yii\web\Controller;

class HelpController extends Controller
{
    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        return $this->render('contact');
    }

    /**
     * Displays help page.
     *
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('help');
    }
}
