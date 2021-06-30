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
}