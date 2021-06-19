<?php
include "redirect.php";
class mysqlTool
{
    public static function createTable(string $tableName, array $cols): array
    {
        $a = "CREATE TABLE IF NOT EXISTS `$tableName` (`id` int NOT NULL AUTO_INCREMENT,";
        foreach ($cols as $value) {
            $a .= '`' . $value . '` longtext NOT NULL,';
        }
        $a .= "PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $conn = dbTool::connectDB();
        if ($conn["status"] === "success") {
            if (!mysqli_query($conn["conn"], $a)) $result = ["status" => "fail", "reason" => mysqli_error($conn["conn"])]; else $result = ["status" => "success"];
        } else {
            return ["status" => "fail", "reason" => $conn["reason"]];
        }
    }

    public static function insert(string $tableName, array $keys, array $values): array
    {
        $a = "INSERT INTO `$tableName` (";
        foreach ($keys as $key) {
            $a .= "`" . $key . "`,";
        }
        $a = substr($a, 0, strlen($a) - 1);
        $a .= ") VALUES (";
        foreach ($values as $value) {
            $a .= "'" . $value . "',";
        }
        $a = substr($a, 0, strlen($a) - 1);
        $a .= ");";
        $conn = dbTool::connectDB();
        if ($conn["status"] === "success") {
            if (!mysqli_query($conn["conn"], $a))
                return ["status" => "fail", "reason" => mysqli_error($conn["conn"])];
            else
                return ["status" => "success"];
        } else {
            return ["status" => "fail", "reason" => $conn["reason"]];
        }
    }
}