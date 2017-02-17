<?php

namespace App\Controllers;

use App\Lib\Controller as Controller;

class App_Controller extends Controller {
    public static function indexAction() {
        $result = self::$renderer->render('index.html', null);
        echo $result;
    }
}
