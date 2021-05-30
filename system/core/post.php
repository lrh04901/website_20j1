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
        $log = "临时目录：".sys_get_temp_dir() . "\r\n";
        $log.="POST变量：".json_encode($_POST)."\r\n";
        $log.="FILE变量：".json_encode($_FILES)."\r\n";
        $log.="REQUEST变量：".json_encode($_REQUEST)."\r\n";
        $log.="TEMP目录下的文件：".json_encode(scandir(sys_get_temp_dir()))."\r\n";
        $log.=file_exists($_FILES["file"]["tmp_name"])?"文件存在\r\n":"文件不存在\r\n";
        $log.=file_exists($_FILES["file"]["tmp_name"]."1")?"分片1存在\r\n":"分片1不存在\r\n";
        if (move_uploaded_file($_FILES["file"]["tmp_name"],"data/update.xcpak")){
            $log.="上传成功\r\n";
            echo "successful";
        }else{
            $log.="上传失败\r\n";
            echo "unsuccessful";
        }
        file_put_contents("data/upload.log",$log);
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