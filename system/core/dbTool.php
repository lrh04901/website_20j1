<?php


class dbTool
{
    function __construct()
    {//用于强制指定文件数据库
        global $force_file_db;
        $GLOBALS["force_file_db"]=true;
    }

    private static function getDataBaseType(): string
    {//获取数据库类型
        if (isset($_GET["debug"])||isset($_POST["debug"])) {
            if ($_GET["debug"] === "yes" || $_POST["debug"] === "yes") {
                return "file";
            }
        }
        return json_decode(file_get_contents(CONFIG_PATH . "db_config.json"), true)['type'];
    }

    public static function connectDB():array
    {//连接数据库(用于MySQL)
        $a = json_decode(file_get_contents(CONFIG_PATH . "db_config.json"), true);
        $conn = mysqli_connect($a['host'], $a['user'], $a['pwd'], $a['name'], $a['port']);
        if (!$conn){
            return ["status"=>"fail","reason"=>mysqli_connect_error()];
        }else{
            return ["status"=>"success","conn"=>$conn];
        }
    }

    private static function getTableHead(array $tableData): array
    {//获取表头行数据
        $b = array();
        for ($i = 0; $i < count($tableData[0]); $i++) {
            $b[$tableData[0][$i]] = $i;
        }
        return $b;
    }

    public static function createTable(string $tableName, array $cols): array
    {//创建数据表
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $result = fileDBTool::createTable($tableName,$cols);
                break;
            case "mysql":
                $result = mysqlTool::createTable($tableName,$cols);
                break;
        }
        return $result;
    }

    public static function insert(string $tableName, array $keys, array $values): array
    {//插入数据
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $result = fileDBTool::insert($tableName,$keys,$values);
                break;
            case "mysql":
                $result = mysqlTool::insert($tableName,$keys,$values);
                break;
        }
        return $result;
    }

    public static function select(string $tableName, array $keys, array $where): array
    {
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), XCGZS_SECRET, true), true);
                $data = array();
                $b = self::getTableHead($a);
                foreach ($a as $value) {
                    if ($value[$b[array_keys($where)[0]]] == $where[array_keys($where)[0]]) {
                        $t = array();
                        foreach ($b as $item) {
                            if ($keys[0] == "*" || in_array($item, $keys)) {
                                $t[array_keys($b)[$item]] = $value[$item];
                            }
                        }
                        $data[count($data)] = $t;
                    }
                }
                $data = arrayTool::removeFirst($data);
                $result = ["status" => "success", "data" => $data];
                break;
            case "mysql":
                $k = "";
                if ($keys[0] == "*") {
                    $k = $keys[0];
                } else {
                    foreach ($keys as $item) {
                        $k .= $item . ",";
                    }
                    $k = substr($k, 0, strlen($k) - 1);
                }
                $w = "";
                if (count($where)) {
                    $w = "WHERE ";
                    for ($i = 0; $i < count($where); $i++) {
                        $w .= array_keys($where)[$i] . "='" . $where[array_keys($where)[$i]] . "',";
                    }
                    $w = substr($w, 0, strlen($w) - 1);
                }
                $a = "SELECT $k FROM $tableName $w;";
                $conn = self::connectDB();
                if ($conn["status"]==="success") {
                    $r = mysqli_query($conn["conn"], $a);
                    if (!$r) {
                        $result = ["status" => "fail", "reason" => mysqli_error($conn["conn"])];
                    }
//                    print_r($r);
//                    die();
                    $data = array();
                    while ($row = mysqli_fetch_assoc($r)) {
                        unset($row['id']);
                        $data[count($data)] = $row;
                    }
                    $result = ["status" => "success", "data" => $data];
//                    print_r($result);
                }else{
                    $result = ["status"=>"fail","reason"=>$conn["reason"]];
                }
                break;
        }
        return $result;
    }

    public static function deleteRow(string $tableName, array $where)
    {
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                if (!count($where)) {
                    $result = ["status" => "fail", "reason" => "where参数不能为空数组，若要删除所有数据请指定where为[*]"];
                    break;
                }
                $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), XCGZS_SECRET, true), true);
                $h = self::getTableHead($a);
                $c = array();
                for ($i = 1; $i < count($a); $i++) {//遍历总数据的每一行
                    for ($j = 0; $j < count($where); $j++) {//遍历条件的数据
                        if ($a[$i][$h[array_keys($where)[$j]]] == $where[array_keys($where)[$j]]) {
                            $c[count($c)] = $a[$i];
                        }
                    }
                }
                foreach ($c as $item) {
                    while (in_array($item, $a)) {
                        $a = arrayTool::remove($a, $item);
                    }
                }
                file_put_contents(FILE_DB_PATH . $tableName . ".xcdb", encryptTool::encode(json_encode($a), XCGZS_SECRET, true));
                $result = ["status" => "success"];
                break;
            case "mysql":
                $a = "DELETE FROM $tableName";
                $c = self::connectDB();
                if ($c["status"]==="success") {
                    if (count($where)) {
                        $a .= " WHERE ";
                        for ($i = 0; $i < count($where); $i++) {
                            $a .= array_keys($where)[$i] . "='" . $where[array_keys($where)[$i]] . "',";
                        }
                        $a = substr($a, 0, strlen($a) - 1);
                    }
                    if (mysqli_query($c["conn"], $a)) {
                        $result = ["status" => "success"];
                    } else {
                        $result = ["status" => "fail", "reason" => mysqli_error($c["conn"])];
                    }
                }else{
                    $result = ["status"=>"fail","reason"=>$c["reason"]];
                }
                break;
        }
        return $result;
    }

    public static function deleteTable(string $tableName): array
    {
        $result = [];
        switch (self::getDataBaseType()) {
            case "file":
                if (file_exists(FILE_DB_PATH . $tableName . ".xcdb")) {
                    if (unlink(FILE_DB_PATH . $tableName . ".xcdb")) {
                        $result = ["status" => "success"];
                    } else {
                        $result = ["status" => "fail", "reason" => ""];
                    }
                } else {
                    $result = ["status" => "fail", "reason" => "数据表不存在"];
                }
                break;
            case "mysql":
                $a = "DROP TABLE $tableName";
                $conn = self::connectDB();
                if ($conn["status"]==="success") {
                    $r = mysqli_query($conn["conn"], $a);
                    if (!$r) {
                        $result = ["status" => "fail", "reason" => mysqli_error($conn["conn"])];
                        return $result;
                    }
                    $result = ["status" => "success"];
                }else{
                    $result = ["status"=>"fail","reason"=>$conn["reason"]];
                }
                break;
        }
        return $result;
    }

    public static function tableExist(string $tableName): bool
    {
        switch (self::getDataBaseType()) {
            case "file":
                return file_exists(FILE_DB_PATH . $tableName . ".xcdb");
            case "mysql":
                $conn = self::connectDB();
                if ($conn["status"]==="success") {
                    return mysqli_query($conn["conn"], "SELECT * FROM " . $tableName) ? true : false;
                }else{
                    return false;
                }
        }
    }

    public static function update(string $tableName, array $keys, array $values, array $where): array
    {
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), XCGZS_SECRET, true), true);
                $h = self::getTableHead($a);
                for ($i = 1; $i < count($a); $i++) {
                    if ($a[$i][$h[array_keys($where)[0]]] == $where[array_keys($where)[0]]) {
                        for ($j=0;$j<count($keys);$j++){
                            $a[$i][$h[$keys[$j]]] = $values[$j];
                        }
                    }
                }
                file_put_contents(FILE_DB_PATH . $tableName . ".xcdb", encryptTool::encode(json_encode($a), XCGZS_SECRET, true));
                $result = ["status" => "success"];
                break;
            case "mysql":
                $a = "UPDATE " . $tableName . " SET ";
                for ($i = 0; $i < count($keys); $i++) {
                    $a .= $keys[$i] . "='" . $values[$i] . "',";
                }
                $a = substr($a, 0, strlen($a) - 1);
                if (count($where)){
                    $a.=" WHERE ";
                    for ($i=0;$i<count($where);$i++){
                        $a.=array_keys($where)[$i]."='".$where[array_keys($where)[$i]]."',";
                    }
                    $a=substr($a,0,strlen($a)-1);
                }
                $c = self::connectDB();
                if ($c["status"]==="success") {
                    $r = mysqli_query($c["conn"], $a);
                    if (!$r) {
                        $result = ["status" => "fail", "reason" => mysqli_error($c["conn"])];
                    } else {
                        $result = ["status" => "success"];
                    }
                }else{
                    $result = ["status"=>"fail","reason"=>$c["reason"]];
                }
                break;
        }
        return $result;
    }

    public static function DBType(): string
    {
        return self::getDataBaseType() == "mysql" ? LANGUAGE_DATA['DATABASE_TYPE_MYSQL'] : (self::getDataBaseType() == "file" ? LANGUAGE_DATA['DATABASE_TYPE_FILE'] : LANGUAGE_DATA['DATABASE_TYPE_UNKNOWN']);
    }
}