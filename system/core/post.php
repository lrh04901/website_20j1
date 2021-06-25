<?php
include "redirect.php";

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
}