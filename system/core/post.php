<?php


class post
{
    public static function update_uploader(){
        $log="";
        $log.=sys_get_temp_dir()."\r\n";
        $log.=json_encode($_POST)."\r\n";
        $log.=json_encode($_FILES)."\r\n";
        $log.=file_exists($_FILES["file"]["tmp_name"])?"file exist\r\n":"file not exist\r\n";
        if (move_uploaded_file($_FILES["file"]["tmp_name"],"data/update.zip")){
            $log.="上传成功\r\n";
            $log.=self::unzip("data/update.zip","data/update/");
            copy("data/update/system","data/system");
        }else{
            $log.="上传失败\r\n";
        }

        file_put_contents("data/upload.log",$log);
    }
    private static function unzip($name,$out){
        $zip = new ZipArchive();
        if (!$zip->open($name)){
            return "解压失败\r\n";
        }else{
            $zip->extractTo($out);
            $zip->close();
            return "解压成功\r\n";
        }
    }
}