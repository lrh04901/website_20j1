<?php
include "redirect.php";

/**
 * 文件数据库模块
 */
class fileDBTool
{
    public static function createTable(string $tableName, array $cols): array
    {
        file_put_contents(FILE_DB_PATH . $tableName . "." . FILE_DB_POSTFIX, encryptTool::encode(json_encode([0 => $cols]), SECRET, true));
        return ["status" => "success"];
    }

    public static function insert(string $tableName, array $keys, array $values): array
    {
        $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . "." . FILE_DB_POSTFIX), SECRET, true), true);
        $b = array();
        for ($i = 0; $i < count($a[0]); $i++) {
            $b[$a[0][$i]] = $i;
        }
        $c = array();
        for ($i = 0; $i < count($values); $i++) {
            $c[$b[$keys[$i]]] = $values[$i];
        }
        $a[count($a)] = $c;
        file_put_contents(FILE_DB_PATH . $tableName . "." . FILE_DB_POSTFIX, encryptTool::encode(json_encode($a), SECRET, true));
        return ["status" => "success"];
    }

    public static function select(string $tableName, array $keys, array $where): array
    {
        $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), SECRET, true), true);
        $data = array();
        $b = dbTool::getTableHead($a);
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
        return ["status" => "success", "data" => $data];
    }

    public static function deleteRow(string $tableName, array $where): array
    {
        if (!count($where)) {
            return ["status" => "fail", "reason" => "where参数不能为空数组，若要删除所有数据请指定where为[*]"];
        }
        $a = json_decode(encryptTool::decode(file_get_contents(FILE_DB_PATH . $tableName . ".xcdb"), SECRET, true), true);
        $h = dbTool::getTableHead($a);
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
        return ["status" => "success"];
    }

    public static function deleteTable(string $tableName): array
    {
        if (file_exists(FILE_DB_PATH . $tableName . ".xcdb")) {
            if (unlink(FILE_DB_PATH . $tableName . ".xcdb")) {
                $result = ["status" => "success"];
            } else {
                $result = ["status" => "fail", "reason" => ""];
            }
        } else {
            $result = ["status" => "fail", "reason" => "数据表不存在"];
        }
        return $result;
    }

    public static function tableExist(string $tableName): bool
    {
        return file_exists(FILE_DB_PATH . $tableName . "." . FILE_DB_POSTFIX);
    }

    public static function update(string $tableName, array $keys, array $values, array $where): array
    {
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
        return ["status" => "success"];
    }
}