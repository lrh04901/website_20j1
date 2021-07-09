<?php

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
    public static function getTableHead(array $tableData): array
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
     * @param array $where 查找条件，全部数据使用["*"]
     * @return array 返回数组，详情请阅读对应数据库模块的文档
     */
    public static function select(string $tableName, array $keys, array $where): array
    {
        $result = "";
        switch (self::getDataBaseType()) {
            case "file":
                $result = fileDBTool::select($tableName,$keys,$where);
                break;
            case "mysql":
                $result = mysqlTool::select($tableName,$keys,$where);
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
                $result = fileDBTool::deleteRow($tableName,$where);
                break;
            case "mysql":
                $result = mysqlTool::deleteRow($tableName,$where);
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
                $result = fileDBTool::deleteTable($tableName);
                break;
            case "mysql":
                $result = mysqlTool::deleteTable($tableName);
                break;
        }
        return $result;
    }

    /**
     * 判断数据表是否存在
     * @param string $tableName 数据表名
     * @return string 返回一个字符串，存在为yes，不存在为no
     */
    public static function tableExist(string $tableName): string
    {
        $result = [];
        switch (self::getDataBaseType()) {
            case "file":
                $result = fileDBTool::tableExist($tableName);
                break;
            case "mysql":
                $result = mysqlTool::tableExist($tableName);
                break;
        }
        return $result;
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
                $result = fileDBTool::update($tableName,$keys,$values,$where);
                break;
            case "mysql":
                $result = mysqlTool::update($tableName,$keys,$values,$where);
                break;
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function getTables():array
    {
        $result = [];
        switch (self::getDataBaseType()){
            case "file":
                $result = fileDBTool::getTables();
                break;
            case "mysql":
                $result = mysqlTool::getTables();
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
        return self::getDataBaseType() == "mysql" ? L['DATABASE_TYPE_MYSQL'] : (self::getDataBaseType() == "file" ? L['DATABASE_TYPE_FILE'] : L['DATABASE_TYPE_UNKNOWN']);
    }
}