<?php
class configTool
{
    public static function getConfig(string $configName): array
    {
        if(substr($configName,strlen($configName)-5)==".json"){
            $configName = substr($configName,0,strlen($configName)-5);
        }
//        echo $configName;
        if (file_exists(CONFIG_PATH.$configName.".json")){
            $a = json_decode(file_get_contents(CONFIG_PATH.$configName.".json"),true);
            if (@$a["encrypted"]=="yes"){
                return ["success","data"=>encryptTool::decode($a["data"],SECRET,true)];
            }else{
                return ["success","data"=>$a];
            }
        }else{
            return ["config_not_exist"];
        }
    }
}
