<?php

namespace App\Viewmodels;

class Viewmodel {
    var $flash_type;
    var $flash_msg;

    var $errors;

    public function toArray() {
        return get_object_vars($this);
    }
}
