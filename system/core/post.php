<?php


class post
{
    public static function uploadUpdate(){
        if (!is_dir("data")){
            if (file_exists("data")){
                unlink("data");
            }
            mkdir("data");
        }
        $upload = new uploader($_FILES['file']['tmp_name'],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
        $a = $upload->apiReturn();
        file_put_contents(DATA_PATH."upload.log",file_get_contents(DATA_PATH."upload.log").$a."\n");
    }
    public static function applyUpdate(){
        xcpak::decode("./data/update.xcpak",PATH);
//        unlink(PATH."run.bat");
        unlink(PATH."run.php");
    }
//    private static function unzip($name,$out){
//        $zip = new ZipArchive();
//        if (!$zip->open($name)){
//            return "解压失败\r\n";
//        }else{
//            $zip->extractTo($out);
//            $zip->close();
//            return "解压成功\r\n";
//        }
//    }
//    private static function xcopy()
}