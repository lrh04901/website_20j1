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
            $result = ["status" => "fail", "reason" => $conn["reason"]];
        }
        return $result;
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

    public static function select(string $tableName,array $keys,array $where):array{
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
        $conn = dbTool::connectDB();
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
        return $result;
    }

    public static function deleteRow(string $tableName,array $where):array{
        $a = "DELETE FROM $tableName";
        $c = dbTool::connectDB();
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
        return $result;
    }

    public static function deleteTable(string $tableName):array{
        $a = "DROP TABLE $tableName";
        $conn = dbTool::connectDB();
        if ($conn["status"] === "success") {
            $r = mysqli_query($conn["conn"], $a);
            if (!$r) {
                return ["status" => "fail", "reason" => mysqli_error($conn["conn"])];
            }
            $result = ["status" => "success"];
        } else {
            $result = ["status" => "fail", "reason" => $conn["reason"]];
        }
        return $result;
    }

    public static function tableExist(string $tableName):bool{
        $conn = dbTool::connectDB();
        if ($conn["status"] === "success") {
            return (bool)mysqli_query($conn["conn"], "SELECT * FROM " . $tableName);
        } else {
            return false;
        }
    }

    public static function update(string $tableName,array $keys,array $values,array $where):array{
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
        $c = dbTool::connectDB();
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
        return $result;
    }
}