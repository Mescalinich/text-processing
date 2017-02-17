<?php

namespace App\Classes;

class File_Processor {
    protected static $instance = null;

    protected $filepath;
    protected $fileformats;

    protected $charsCount = -1;
    protected $wordsCount = -1;

    public static function getInstance($filepath = null) {
        if (!self::$instance) {
            self::$instance = new File_Processor($filepath);
        }
        return self::$instance;
    }

    private function __construct($filepath = null) {
        $this->filepath = $filepath;

        $formatsDir = __DIR__ . DIRECTORY_SEPARATOR . 'fileformats';

        foreach (glob("$formatsDir/*.php") as $filename)
        {
            include $filename;
        }

        $classes = get_declared_classes();

        $this->fileformats = array();

        foreach($classes as $class) {
            $reflect = new \ReflectionClass($class);
            if($reflect->implementsInterface('App\\Classes\\IFileFormat')) {
                $this->fileformats[] = $class;
            }
        }

        if ($filepath) {
            $this->processFile();
        }
    }

    private function processFile() {
        // Try to process file if allowed
        if ($this->isFileAllowed()) {
            $path_parts = pathinfo($this->filepath);
            $extention = $path_parts['extension'];

            $classes = get_declared_classes();

            foreach($classes as $class) {
                $reflect = new \ReflectionClass($class);
                if($reflect->implementsInterface('App\\Classes\\IFileFormat')) {
                    if ($extention === $class::getFileExtension()) {
                        $fileFormat = new $class($this->filepath);
                        $this->charsCount = $fileFormat->getCharactersCount();
                        $this->wordsCount = $fileFormat->getWordsCount();
                    }
                }
            }
        }
    }

    public function getAvaibleFileExtentions() {
        $result = array();
        foreach($this->fileformats as $format) {
            $result[] = $format::getFileExtension();
        }
        return $result;
    }


    public function isFileAllowed() {
        $path_parts = pathinfo($this->filepath);
        $extention = $path_parts['extension'];

        if (in_array($extention, $this->getAvaibleFileExtentions())) {
            return true;
        } else {
            return false;
        }
    }

    public function getCharactersCount() {
        return $this->charsCount;
    }

    public function getWordsCount() {
        return $this->wordsCount;
    }
}