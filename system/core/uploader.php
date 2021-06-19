<?php
include "redirect.php";

class uploader
{
    private $filepath = './data';
    private $tempPath;
    private $blobNum;
    private $totalBlobNum;
    private $fileName;

    /**
     * uploader constructor.
     * @param $tempPath
     * @param $blobNum
     * @param $totalBlobNum
     * @param $fileName
     */
    public function __construct($tempPath, $blobNum, $totalBlobNum, $fileName)
    {
        $this->tempPath = $tempPath;
        $this->blobNum = $blobNum;
        $this->totalBlobNum = $totalBlobNum;
        $this->fileName = $fileName;

        $this->moveFile();
        $this->fileMerge();
    }

    private function fileMerge()
    {
        if ($this->blobNum == $this->totalBlobNum) {
            $blob = '';
            for ($i = 1; $i <= $this->totalBlobNum; $i++) {
                $blob .= file_get_contents($this->filepath . '/' . $this->fileName . '__' . $i);
            }
            file_put_contents($this->filepath . '/' . $this->fileName, $blob);
            $this->deleteFileBlob();
        }
    }

    private function deleteFileBlob()
    {
        for ($i = 1; $i <= $this->totalBlobNum; $i++) {
            @unlink($this->filepath . '/' . $this->fileName . '__' . $i);
        }
    }

    private function moveFile()
    {
        $this->touchDir();
        $filename = $this->filepath . '/' . $this->fileName . '__' . $this->blobNum;
        move_uploaded_file($this->tempPath, $filename);
    }

    public function apiReturn()
    {
        if ($this->blobNum == $this->totalBlobNum) {
            if (file_exists($this->filepath . '/' . $this->fileName)) {
                $data['code'] = 2;
                $data['msg'] = 'success';
                $data['file_path'] = DATA_PATH . $this->fileName;
            }
        } else {
            if (file_exists($this->filepath . '/' . $this->fileName . '__' . $this->blobNum)) {
                $data['code'] = 1;
                $data['msg'] = 'waiting for all';
                $data['file_path'] = '';
            }
        }
        header('Content-type: application/json');
        return json_encode($data);
    }

    private function touchDir()
    {
        if (!file_exists($this->filepath)) {
            return mkdir($this->filepath);
        }
    }
}