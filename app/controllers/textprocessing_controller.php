<?php

namespace App\Controllers;

use App\Lib\Controller as Controller;
use App\Classes\File_Processor as File_Processor;
use App\Viewmodels\Textprocessing_Inputresult_Viewmodel as InputresultViewmodel;

class Textprocessing_Controller extends Controller {
    public static function inputAction() {
        $result = self::$renderer->render('textprocessing.input.html', null);
        echo $result;
    }

    public static function uploadAction() {
        $fp = File_Processor::getInstance();
        $allowedExt = implode(',', $fp->getAvaibleFileExtentions());
        $maxUploadFileSize = self::file_upload_max_size();

        $showErrors = "hidden";
        if (self::$errors) {
            $showErrors = "";
        }

        $viewModelData = array(
            "file_formats" => $allowedExt,
            "max_upload_filesize" => $maxUploadFileSize,
            "errors" => implode("<br />", self::$errors),
            "show_errors" => $showErrors,
        );

        $result = self::$renderer->render('textprocessing.upload.html', $viewModelData);
        echo $result;
    }

    public static function fileuploadAction() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['inputfile'])) {
                $error = $_FILES['inputfile']['error'];
                switch ((int)$error) {
                    case UPLOAD_ERR_OK:
                        $uploadfile = APP_UPLOADS_PATH . basename($_FILES['inputfile']['name']);
                        if (move_uploaded_file($_FILES['inputfile']['tmp_name'], $uploadfile)) {

                            $fp = File_Processor::getInstance($uploadfile);
                            if ($fp->isFileAllowed()) {

                                $vm = new InputresultViewmodel();
                                $vm->charsCount = $fp->getCharactersCount();
                                $vm->wordsCount = $fp->getWordsCount();

                                $result = self::$renderer->render('textprocessing.uploadresult.html', $vm->toArray());
                                echo $result;
                                return;
                            }
                            else {
                                self::$errors[] = 'File format not allowed';
                            }

                        } else {
                            self::$errors[] = "Ошибка при копировании загруженного файла";
                        }

                        // File uploaded. Lets check it

                        break;
                    case UPLOAD_ERR_NO_FILE:
                        self::$errors[] = 'No file sent.';
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        self::$errors[] = 'Exceeded filesize limit.';
                    default:
                        self::$errors[] = 'Unknown errors.';
                }
            }
        }
        else {
            self::$errors[] = "Method not alloed";

        }

        self::uploadAction();
    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    static function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = self::parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    static function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }

}
