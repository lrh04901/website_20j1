<?php
include "redirect.php";

/**
 * 数据库模块
 */
class dbTool
{
    /**
     * 获取当前数据库类型
     * @return string 返回数据库类型：mysql/file
     */
    private static function getDataBaseType(): string
    {//获取数据库类型
        if (isset($_GET["debug"]) || isset($_POST["debug"])) {
            if ($_GET["debug"] === "yes" || $_POST["debug"] === "yes") {
                return "file";
            }
        }
        return json_decode(file_get_contents(CONFIG_PATH . "db_config.json"), true)['type'];
    }

    /**
     * 连接数据库（MySQL）
     * @return array 返回数组，array[0]为当前状态，fail为失败（array[1]为失败原因），success为成功(array[1]为MySQL对象)
     */
    public static function connectDB(): array
    {//连接数据库(用于MySQL)
        $a = json_decode(file_get_contents(CONFIG_PATH . "db_config.json"), true);
        $conn = mysqli_connect($a['host'], $a['user'], $a['pwd'], $a['name'], $a['port']);
        if (!$conn) {
            return ["status" => "fail", "reason" => mysqli_connect_error()];
        } else {
            return ["status" => "success", "conn" => $conn];
        }
    }

    /**
     * 获取数据表的表头数据
     * @param array $tableData 数据表
     * @return array 返回标题数组
     */
    private static function getTableHead(array $tableData): array
    {//获取表头行数据
        $b = array();
        for ($i = 0; $i < count($tableData[0]); $i++) {
            $b[$tableData[0][$i]] = $i;
        }
        return $b;
    }

    /**
     * 在数据库中创建数据表
     * @param string $tableName 数据表名
     * @param array $cols 数据表的列
     * @return array 返回数组，详情请阅读对应数据库模块的文档
     */
    public static function createTable(string $tableName, array $cols): array
    {//创建数据表
        $result = [];
        switch (self::getDataBaseType()) {
            case "file":
                $result = fileDBTool::createTable($tableName, $cols);
                break;
            case "mysql":
                $result = mysqlTool::createTable($tableName, $cols);
                break;
        }
        return $result;
    }

    /**
     * 在数据表中插入一行数据
     * @param string $tableName 数据表名
     * @param array $keys 插入数据的列名
     * @param array $values 插入的数据
     * @return array|string 返回数组，详情请阅读对应数据库模块的文档
     */
    public static function insert(string $tableName, array $keys, array $values): array
    {//插入数据
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $result = fileDBTool::insert($tableName, $keys, $values);
                break;
            case "mysql":
                $result = mysqlTool::insert($tableName, $keys, $values);
                break;
        }
        return $result;
    }

    /**
     * 从数据库中读取数据
     * @param string $tableName 数据表名
     * @param array $keys 需要读取的数据的键，全部数据使用["*"]
     * @param array $where 查找条件
     * @return array 返回数组，详情请阅读对应数据库模块的文档
     */
    public static function select(string $tableName, array $keys, array $where): array
    {
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), SECRET, true), true);
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
                if ($conn["status"] === "success") {
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
                } else {
                    $result = ["status" => "fail", "reason" => $conn["reason"]];
                }
                break;
        }
        return $result;
    }


    /**
     * 删除数据库中的一行
     * @param string $tableName 数据表名
     * @param array $where 删除条件
     * @return array 返回数组，详情请阅读对应数据库模块的文档
     */
    public static function deleteRow(string $tableName, array $where):array
    {
        $result = [];
        switch (self::getDataBaseType()) {
            case "file":
                if (!count($where)) {
                    $result = ["status" => "fail", "reason" => "where参数不能为空数组，若要删除所有数据请指定where为[*]"];
                    break;
                }
                $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), SECRET, true), true);
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
                file_put_contents(FILE_DB_PATH . $tableName . ".xcdb", encryptTool::encode(json_encode($a), SECRET, true));
                $result = ["status" => "success"];
                break;
            case "mysql":
                $a = "DELETE FROM $tableName";
                $c = self::connectDB();
                if ($c["status"] === "success") {
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
                } else {
                    $result = ["status" => "fail", "reason" => $c["reason"]];
                }
                break;
        }
        return $result;
    }

    /**
     * 删除某个数据表
     * @param string $tableName 数据表名
     * @return array 返回数组，详情请阅读对应数据库模块的文档
     */
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
                if ($conn["status"] === "success") {
                    $r = mysqli_query($conn["conn"], $a);
                    if (!$r) {
                        $result = ["status" => "fail", "reason" => mysqli_error($conn["conn"])];
                        return $result;
                    }
                    $result = ["status" => "success"];
                } else {
                    $result = ["status" => "fail", "reason" => $conn["reason"]];
                }
                break;
        }
        return $result;
    }

    /**
     * 判断数据表是否存在
     * @param string $tableName 数据表名
     * @return bool 返回一个布尔值，存在为true，不存在为false
     */
    public static function tableExist(string $tableName): bool
    {
        switch (self::getDataBaseType()) {
            case "file":
                return file_exists(FILE_DB_PATH . $tableName . ".xcdb");
            case "mysql":
                $conn = self::connectDB();
                if ($conn["status"] === "success") {
                    return mysqli_query($conn["conn"], "SELECT * FROM " . $tableName) ? true : false;
                } else {
                    return false;
                }
        }
    }

    /**
     * 更新数据表中的某一行数据
     * @param string $tableName 数据表名
     * @param array $keys 数据列名
     * @param array $values 数据内容
     * @param array $where 查找条件
     * @return array 返回数组，详情请阅读对应数据库模块的文档
     */
    public static function update(string $tableName, array $keys, array $values, array $where): array
    {
        $result = [];
        switch (self::getDataBaseType()) {
            case "file":
                $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), SECRET, true), true);
                $h = self::getTableHead($a);
                for ($i = 1; $i < count($a); $i++) {
                    if ($a[$i][$h[array_keys($where)[0]]] == $where[array_keys($where)[0]]) {
                        for ($j = 0; $j < count($keys); $j++) {
                            $a[$i][$h[$keys[$j]]] = $values[$j];
                        }
                    }
                }
                file_put_contents(FILE_DB_PATH . $tableName . ".xcdb", encryptTool::encode(json_encode($a), SECRET, true));
                $result = ["status" => "success"];
                break;
            case "mysql":
                $a = "UPDATE " . $tableName . " SET ";
                for ($i = 0; $i < count($keys); $i++) {
                    $a .= $keys[$i] . "='" . $values[$i] . "',";
                }
                $a = substr($a, 0, strlen($a) - 1);
                if (count($where)) {
                    $a .= " WHERE ";
                    for ($i = 0; $i < count($where); $i++) {
                        $a .= array_keys($where)[$i] . "='" . $where[array_keys($where)[$i]] . "',";
                    }
                    $a = substr($a, 0, strlen($a) - 1);
                }
                $c = self::connectDB();
                if ($c["status"] === "success") {
                    $r = mysqli_query($c["conn"], $a);
                    if (!$r) {
                        $result = ["status" => "fail", "reason" => mysqli_error($c["conn"])];
                    } else {
                        $result = ["status" => "success"];
                    }
                } else {
                    $result = ["status" => "fail", "reason" => $c["reason"]];
                }
                break;
        }
        return $result;
    }

    /**
     * 返回数据库类型的名称（显示内容与当前语言有关）
     * @return string 当前数据库类型
     */
    public static function DBType(): string
    {
        return self::getDataBaseType() == "mysql" ? LANGUAGE_DATA['DATABASE_TYPE_MYSQL'] : (self::getDataBaseType() == "file" ? LANGUAGE_DATA['DATABASE_TYPE_FILE'] : LANGUAGE_DATA['DATABASE_TYPE_UNKNOWN']);
    }
}