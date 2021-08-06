<?php

/**
 * post处理模块
 */
class post
{
    /**
     * 上传更新包
     * @return bool 返回true，防止执行错误
     */
    public static function uploadUpdate():bool
    {
        if (!is_dir("data")) {
            if (file_exists("data")) {
                unlink("data");
            }
            mkdir("data");
        }
        $upload = new uploader($_FILES['file']['tmp_name'], $_POST['blob_num'], $_POST['total_blob_num'], $_POST['file_name']);
        $a = $upload->apiReturn();
        file_put_contents(DATA_PATH . "upload.log", file_get_contents(DATA_PATH . "upload.log") . $a . "\n");
        return true;
    }

    /**
     * 应用更新
     * @return bool 返回true，防止执行错误
     */
    public static function applyUpdate():bool
    {
        xcpak::decode("./data/update.xcpak", PATH);
//        unlink(PATH."run.bat");
        unlink(PATH . "run.php");
        return true;
    }

    public static function login():bool{
        $user = argsTool::post("user");
        $pass = hash("sha256",argsTool::post("pass"));
//        echo $pass;
        $result = dbTool::select("users",["*"],["username"=>$user]);
//        echo $result["data"]["password"];
//        print_r($result);
        if ($result["status"]=="success"){
            if ($result["data"]){
                if ($result["data"][0]["password"]==$pass){
                    if ($result["data"][0]["ban"]=="yes"){
                        echo json_encode(["status"=>"fail","reason"=>"ban","banReason"=>$result["data"]["banReason"]]);
                    }else{
                        echo json_encode(["status"=>"success"]);
                    }
                }else{
                    echo json_encode(["status"=>"fail","reason"=>"password-wrong"]);
                }
            }else{
                echo json_encode(["status"=>"fail","reason"=>"user-not-exist"]);
            }
        }else{
            echo json_encode(["status"=>"fail","reason"=>"database-error"]);
        }
//        echo json_encode($result);
        return true;
    }
    public static function signup():bool{
        $user = argsTool::post("user");
        $pass = hash("sha256",argsTool::post("pass"));
        $mail = argsTool::post("mail");
        $r_a = dbTool::select("users",["*"],["username"=>$user]);
        if ($r_a["status"]=="success"){
            if ($r_a["data"]){
                echo json_encode(["status"=>"fail","reason"=>"user-exist"]);
            }else{
                $r_b = dbTool::select("users",["*"],["email"=>$mail]);
                if ($r_b["status"]=="success"){
                    if ($r_b["data"]){
                        echo json_encode(["status"=>"fail","reason"=>"mail-exist"]);
                    }else{
                        $r_b = dbTool::insert("users", ["username", "name", "password", "email", "ban", "banReason", "admin"], [$user, $user, $pass, $mail, "", "", ""]);
                        if ($r_b["status"] == "success") {
                            echo json_encode(["status" => "success"]);
                        } else {
                            echo json_encode(["status" => "fail", "reason" => "database-error"]);
                        }
                    }
                }else{
                    echo json_encode(["status"=>"fail","reason"=>"database-error"]);
                }
            }
        }else{
            echo json_encode(["status"=>"fail","reason"=>"database-error"]);
        }
        return true;
    }

    public static function sendCaptchaCode():bool
    {
        require "phar://mail.phar/PHPMailerAutoload.php";
        $code = rand(1000,9999);
        $address = argsTool::post("mail");
        $config = json_decode(file_get_contents(CONFIG_PATH . "mail_config.json"),true);
        $mail = new PHPMailer(false);
        $mail->isSMTP();
        $mail->Host = $config["mail_smtp"];
        $mail->SMTPAuth = true;
        $mail->Username = $config["mail_username"];
        $mail->Password = $config["mail_password"];
        try {
            $mail->setFrom($config["mail_username"],L["INDEX_TITLE"]);
        }catch (phpmailerException $e){}
        $mail->addAddress($address);
        $mail->CharSet = "UTF-8";
        $mail->Subject = "验证码邮件";
        $mail->Body = '<div style="width: 85%;height: 50%;padding: 15px;background-color: #3280fc;color: #FFFFFF;box-shadow: 10px 10px 5px grey;font-size: 25px">感谢您在20J1官网注册账号，您的验证码是<b><strong><i>'.$code.'</i></strong></b>，请立即注册，重新发送邮件验证码时该验证码将会失效。</div>';
        $mail->AltBody = "验证码邮件";
        try {
            if ($mail->send()){
                echo json_encode(["status"=>"success","code"=>$code]);
            }else{
                echo json_encode(["status"=>"fail","reason"=>$mail->ErrorInfo]);
            }
        }catch (phpmailerException $e){}
        return true;
    }

    public static function dbAdmin(): bool
    {
        dbAdmin::run();
        return true;
    }

    /**
     * 生成座位表的Excel文档
     * @return bool
     */
    public static function arrangeSeats():bool
    {
        if (!is_dir(DATA_PATH)){
            if (is_file(DATA_PATH)){
                unlink(DATA_PATH);
            }
            mkdir(DATA_PATH);
        }
//        print_r($_POST);
        require "phar://phpExcel.phar/PHPExcel.php";
        $width = 12;
        $subWidth = 8;
        $height = 20;
        $phpExcel = new PHPExcel();
        $phpExcel->getProperties()
            ->setCreator("20J1 arrange seats")
            ->setLastModifiedBy("20J1")
            ->setTitle("20J1 seats table")
            ->setSubject("20J1 seats table")
            ->setDescription("20J1 seats table")
            ->setKeywords("seats table")
            ->setCategory("table");
        $phpExcel->setActiveSheetIndex()
            ->setTitle("座位表");
        foreach ([ 'B', 'C', 'E', 'F', 'H', 'I', 'K', 'L'] as $letter) {
            $phpExcel->setActiveSheetIndex()->getColumnDimension($letter)->setWidth($width);
        }
        foreach (['A', 'D', 'G', 'J'] as $letter) {
            $phpExcel->setActiveSheetIndex()->getColumnDimension($letter)->setWidth($subWidth);
        }
        for ($i = 1;$i<=7;$i++){
            $phpExcel->setActiveSheetIndex()->getRowDimension($i)->setRowHeight($height);
        }
        $phpExcel->setActiveSheetIndex()->getStyle('A1:L7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $phpExcel->setActiveSheetIndex()->getStyle('A1:A6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        $phpExcel->setActiveSheetIndex()->getStyle('B6:L6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        foreach (['D','G','J'] as $letter){
            $phpExcel->setActiveSheetIndex()->getStyle($letter.'1:'.$letter.'5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
        }
        $phpExcel->setActiveSheetIndex()->setCellValue('G7',"讲台");
        $index = 5;
        foreach (['A1', 'A2', 'A3', 'A4', 'A5'] as $cell) {
            @$phpExcel->setActiveSheetIndex()->setCellValue($cell,$index--);
        }
        $index = 1;
        foreach (['B', 'C', 'E', 'F', 'H', 'I', 'K', 'L'] as $letter) {
            @$phpExcel->setActiveSheetIndex()->setCellValue($letter.'6',$index++);
        }
        for ($i = 0;$i<count($_POST);$i++){
            $key = array_keys($_POST)[$i];
            $value = $_POST[$key];
//            echo "$key $value ";
//            echo self::arrangeSeatsPosition($key)."\n";
            $phpExcel->setActiveSheetIndex()->setCellValue(self::arrangeSeatsPosition($key),$value);
            if ($value=="X"){
//                echo $key."\n";
                $phpExcel->setActiveSheetIndex()->getStyle(self::arrangeSeatsPosition($key))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFFF0000");
            }
        }
        $writer = PHPExcel_IOFactory::createWriter($phpExcel,'Excel2007');
        $time = date("yymdhis");
        $writer->save(DATA_PATH."座位表$time.xlsx");
        echo json_encode(["a"=>$_SERVER["HTTP_HOST"].strstr($_SERVER["REQUEST_URI"],"?",true),"b"=>$time,"c"=>".xlsx"]);
        return true;
    }

    /**
     * 根据座位坐标定位单元格位置
     * @param string $position 座位坐标
     * @return string 单元格位置
     */
    private static function arrangeSeatsPosition(string $position):string
    {
        $a = $position;
        if (strlen($a)==3){
            $a = substr($a,1);
        }
        $x = (int)$a[0]-1;
        $y = (int)$a[1]-1;
        $x_array = ['B','C','E','F','H','I','K','L'];
        $y_array = ['5','4','3','2','1'];
        return $x_array[$x].$y_array[$y];
    }

    public static function scj():bool
    {
        if (!file_exists(CACHE_PATH."scj.cache")){
            touch(CACHE_PATH."scj.cache");
            file_put_contents(CACHE_PATH."scj.cache",encryptTool::encode(json_encode([]),SECRET,true));
        }
        $text = argsTool::post("text");
        $cache = json_decode(encryptTool::decode(file_get_contents(CACHE_PATH."scj.cache"),SECRET,true),true);
        if (isset($cache[$text])){
            die($cache[$text]);
        }
        $lang = ["slo","fin","rom","zh"];
        $new_text = $text;
        foreach ($lang as $item) {
            $new_text = self::scj_a($new_text,$item);
        }
        echo $new_text;
        @$a = explode("：",$new_text);
        if ($a[0]=="翻译失败"){
            die();
        }
        $cache[$text] = $new_text;
        file_put_contents(CACHE_PATH."scj.cache",encryptTool::encode(json_encode($cache),SECRET,true));
        return true;
    }

    private static function scj_a(string $text,string $to):string
    {
        sleep(1);
        $url = "https://fanyi-api.baidu.com/api/trans/vip/translate";
        $q = $text;
        $config = json_decode(encryptTool::decode(json_decode(file_get_contents(CONFIG_PATH."bdfy_config.json"),true)["data"],SECRET,true),true);
        $appid = $config["appid"];
        $secret = $config["secret"];
        $salt = "sch20040925";
        $sign = hash("md5","$appid$q${salt}$secret");
        $data = file_get_contents(str_replace(" ","%20","$url?q=$q&from=auto&to=$to&appid=$appid&salt=$salt&sign=$sign"));
        $json = json_decode($data,true);
        if (isset($json["trans_result"])){
            return $json["trans_result"][0]["dst"];
        }else{
            echo $data;
            return "翻译失败：".$json["error_code"]."<br>";
        }
    }
}
