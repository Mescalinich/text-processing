<?php

namespace App\Lib;

class Controller {
    //put your code here
    protected $view;
    protected static $renderer;
    protected static $errors;

    function __construct($renderer) {
        self::$renderer = $renderer;
        self::$errors = [];
    }
}
