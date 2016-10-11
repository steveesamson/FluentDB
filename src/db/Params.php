<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/21/16
 * Time: 2:45 PM
 */

namespace slicks\db;


class Params
{

    private $map = [];

    const INT = \PDO::PARAM_INT;
    const STR = \PDO::PARAM_STR;
    const BOOL = \PDO::PARAM_BOOL;
    const LOB = \PDO::PARAM_LOB;
    const NULL = \PDO::PARAM_NULL;
    const INOUT = \PDO::PARAM_INPUT_OUTPUT;


    public function add(&$key, $val, $type = null)
    {

        if (is_null($type)) {
            $type = is_int($val) ? self::INT : is_bool($val) ? self::BOOL : self::STR;
        }


        if(isset($this->map[trim($key)])){
            $key = trim($key) . '1';
        }


        $this->map[trim($key)] = (object)['val' => $val, 'key' => trim($key), 'type' => $type];
    }


    public function bind(&$statement)
    {
        foreach ($this->map as $param) {
            $statement->bindValue($param->key, $param->val, $param->type);
        }
    }

    public function map(){
        return $this->map;
    }
}