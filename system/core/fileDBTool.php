<?php


class fileDBTool
{
    public static function createTable(string $tableName, array $cols): array
    {
        file_put_contents(FILE_DB_PATH . $tableName . "." . FILE_DB_POSTFIX, encryptTool::encode(json_encode([0 => $cols]), SECRET, true));
        return ["status" => "success"];
    }

    public static function insert(string $tableName, array $keys, array $values):array
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
}