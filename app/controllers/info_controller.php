<?php

namespace App\Controllers;

use App\Lib\Controller as Controller;

class Info_Controller extends Controller {
    public static function aboutAction() {
        $result = self::$renderer->render('about.html', null);
        echo $result;
    }

    public static function contactAction() {
        $result = self::$renderer->render('contact.html', null);
        echo $result;
    }
}