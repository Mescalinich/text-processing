<?php

namespace App\Classes;

class FileFormatTxt implements IFileFormat {
    protected $charsCount = 0;
    protected $wordsCount = 0;

    protected $filepath;

    function __construct($filepath) {
        if (file_exists($filepath)) {
            $this->filepath = $filepath;

            $handle = fopen($this->filepath, "r");
            while(!feof($handle)){
                $line = fgets($handle);
                $this->charsCount += strlen($line);
                $this->wordsCount += str_word_count($line);
            }

            fclose($handle);

        }
    }

    // Interface implementation
    public static function getFileExtension() {
        return 'txt';
    }

    public function getCharactersCount() {
        return $this->charsCount;
    }
    public function getWordsCount() {
        return $this->wordsCount;
    }
}
