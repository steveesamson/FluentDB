<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/19/16
 * Time: 4:02 PM
 */

namespace slicks\db;
use slicks\db\Utils as _;


class StringBuilder
{

    protected $buffer = [];
    protected $delimiter = '';

    public function __construct($initial = ''){
        if($initial){
            $this->buffer[] = $initial;
        }
    }
    public function append($o){
        $this->buffer[] = $o;
        return $this;
    }

    public function length(){
        return count($this->buffer);
    }

    public function join($delimiter){
        return array_reduce($this->buffer,function($a, $item) use ($delimiter){
            $a .=  empty($a)? $item : $delimiter .$item;
            return $a;
        });
    }

    public function toString(){

        return array_reduce($this->buffer,function($a, $item){
            $a .=  empty($a)? $item : $this->delimiter .$item;
            return $a;
        });
    }

    public function reInit($stringOrArray){
        if(is_string($stringOrArray)){
            $arr = _::trim(_::split($stringOrArray,","));
            $this->buffer = $arr;
        }elseif(is_array($stringOrArray)){
            $this->buffer = $stringOrArray;
        }
    }
    public function isEmpty(){

        return empty($this->buffer);
    }


}