<?php

namespace App\Classes;

interface IFileFormat {
    public static function getFileExtension();
    public function getCharactersCount();
    public function getWordsCount();
}