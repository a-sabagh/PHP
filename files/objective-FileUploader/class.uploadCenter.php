<?php

/**
 * upload center for uploading multiple and singular file
 * set type and size of file and get all messages about uploading proccess
 * Author: gnutec.ir
 * Author link: http://gnutec.ir
 * Link: https://github.com/a-sabagh/PHP/tree/master/files/objective-FileUploader
 */
class uploadCenter {

    protected $destination;
    protected $messages;
    protected $uploadOk = TRUE;
    protected $fileName = NULL;
    protected $fileSize = 1024 * 1024;
    protected $fileType = array("image/jpeg", "image/png", "image/webp", "image/x-icon", "application/zip", "application/pdf", "application/x-rar-compressed");
    protected $blacklistExt = array("js", "py", "exe", "php", "dmg", "php3", "php4", "phtml", "pl", "jsp", "asp", "htm", "shtml", "sh", "cgi");
    protected $suffix = ".txt";

    /**
     * create destination with this template '$folder/year/month/'
     * @param type $destination
     * @throws Exception
     */
    function __construct($destination) {
        if (is_dir($destination) and is_writable($destination)) {
            $perma_path = "";
            $year = date("Y");
            $month = date("m");
            if ($destination[strlen($destination) - 1] !== "/") {
                $perma_path = "{$destination}/{$year}/{$month}/";
            } else {
                $perma_path = "{$destination}{$year}/{$month}/";
            }
            if (!is_dir($perma_path)) {
                mkdir($perma_path, 0755, TRUE);
            }
            $this->destination = $perma_path;
        } else {
            throw new Exception("{$destination} must be real directory and be writable");
        }
    }

    /**
     * uploading proccess for multiple and singular uploading file
     * @param type $file
     */
    public function upload($file) {
        if (is_array($file['name'])) {
            $current_file = array();
            $count = count(current($file));
            for ($i = 0; $i < $count; $i++) {
                $current['name'] = $file['name'][$i];
                $current['type'] = $file['type'][$i];
                $current['tmp_name'] = $file['tmp_name'][$i];
                $current['error'] = $file['error'][$i];
                $current['size'] = $file['size'][$i];
                $this->checkName($current['name']);
                $this->uploadOk = TRUE;
                $this->checkFile($current);
                if ($this->uploadOk) {
                    $this->moveFileUpload($current);
                }
            }
        } else {
            $this->checkName($file['name']);
            $this->checkFile($file);
            if ($this->uploadOk) {
                $this->moveFileUpload($file);
            }
        }
    }

    /**
     * neutralize blacklist extension file
     * @param type $filename
     */
    protected function neutralizeBlacklistExt($filename) {
        $pathinfo = pathinfo($filename);
        $extension = $pathinfo['extension'];

        if (in_array($extension, $this->blacklistExt)) {
            $this->fileName = $this->fileName . $this->suffix;
        }
    }

    /**
     * checking size and type and error of the uploading file
     * @param type $file
     */
    protected function checkFile($file) {
        if (!$this->checkSize($file['size'])) {
            $this->uploadOk = FALSE;
        }
        if (!$this->checkType($file['type'])) {
            $this->uploadOk = FALSE;
        }
        if (!$this->checkError($file['error'])) {
            $this->uploadOk = FALSE;
        }
    }

    /**
     * convert string value to byte for example 1Mb = 1024 byte
     * @param type $string
     * @return boolean|int
     */
    protected static function convertToByte($string) {
        $output = (int) $string;
        $unit = strtolower($string[strlen($string) - 1]);
        $computer_units = array("k", "m", "g");
        if (in_array($unit, $computer_units)) {
            switch ($unit) {
                case "g":
                    $output *= 1024;
                case "m":
                    $output *= 1024;
                case "k":
                    $output *= 1024;
            }
            return $output;
        } else {
            return FALSE;
        }
    }

    /**
     * seting max size for uploading file and check it with ini server max size
     * @param type $size
     * @throws Exception
     */
    public function setMaxSize($size) {
        $serverMaxSize = self::convertToByte(ini_get("upload_max_filesize"));
        $size = (int) $size;
        $serverMaxSize = (int) $serverMaxSize;
        if ($size > $serverMaxSize) {
            throw new Exception("{$size} is greater than server maximum file size");
        }
        if (is_numeric($size) and ! empty($size) and $size !== 0) {
            $this->fileSize = $size;
        }
    }

    /**
     * checking size of uploading file 
     * @param type $size
     * @return boolean
     */
    protected function checkSize($size) {
        if ($size > $this->fileSize) {
            $this->messages[] = $this->fileName . " is to big";
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * set type for uploading file
     * @param type $array_type
     * @throws Exception
     */
    public function setType($array_type) {
        if (is_array($array_type)) {
            $this->fileType = $array_type;
        } else {
            throw new Exception("uploadCenter::setType parameter must be array");
        }
    }

    /**
     * checking type of uploading file 
     * @param type $type
     * @return boolean
     */
    protected function checkType($type) {
        if (in_array($type, $this->fileType)) {
            return TRUE;
        } else {
            $this->messages[] = "type of {$type} file is illegal";
            return FALSE;
        }
    }

    /**
     * checking error of uploading file
     * @param type $error
     * @return boolean
     */
    protected function checkError($error) {
        switch ($error) {
            case 0:
                return TRUE;
            case 1;
                $this->messages[] = 'UPLOAD_ERR_INI_SIZE';
                return FALSE;
            case 2;
                $this->messages[] = 'UPLOAD_ERR_FORM_SIZE';
                return FALSE;
            case 3;
                $this->messages[] = 'UPLOAD_ERR_PARTIAL';
                return FALSE;
            case 4;
                $this->messages[] = 'UPLOAD_ERR_NO_FILE';
                return FALSE;
            case 6;
                $this->messages[] = 'UPLOAD_ERR_NO_TMP_DIR';
                return FALSE;
            case 7;
                $this->messages[] = 'UPLOAD_ERR_CANT_WRITE';
                return FALSE;
            case 8;
                $this->messages[] = 'UPLOAD_ERR_EXTENSION';
                return FALSE;
        }
    }

    /**
     * checking name of uploading file for rename file that exist in folder and replace space with  underscore
     * and neutrilize blacklist extencion
     * @param type $name
     * @return boolean
     */
    protected function checkName($name) {
        if (strpos($name, " ")) {
            $this->fileName = str_replace(" ", "_", $name);
        } else {
            $this->fileName = $name;
        }
        $this->neutralizeBlacklistExt($name);
        $existing_files = scandir($this->destination);
        $pathinfo = pathinfo($this->fileName);
        $extension = $pathinfo['extension'];
        $filename = $pathinfo['filename'];
        $i = 1;
        while (in_array($this->fileName, $existing_files)) {
            $this->fileName = $filename . "_{$i}." . $extension;
            $i++;
        }
        return TRUE;
    }

    /**
     * moving uploaded file from temp directory to permanenet path
     * finally showing message if file rename 
     * @param type $file
     */
    protected function moveFileUpload($file) {

        $temp_path = $file['tmp_name'];
        $destination = $this->destination;
        $destination = $destination . $this->fileName;
        $result = move_uploaded_file($temp_path, $destination);
        if ($result) {
            if ($file['name'] !== $this->fileName) {
                $this->messages[] = "{$file['name']} is rename to " . $this->fileName;
            }
            $this->messages[] = $this->fileName . " was uploaded successfully";
        } else {
            $this->messages[] = "uploading " . $this->fileName . " fail";
        }
    }

    /**
     * show array messages
     * @return type
     */
    public function getMessage() {
        return $this->messages;
    }

}
