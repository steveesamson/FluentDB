<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/22/16
 * Time: 3:45 PM
 */

namespace slicks\db;
use slicks\db\Utils as _;

class HavingBuilder extends StringBuilder
{
    protected $params = null;


    public function __construct($initial='')
    {
        parent::__construct($initial);
        $this->params = new Params();
    }

    public function having($column, $value){

        $column = _::hasOperator($column) ? $column : "$column=";
        $bindPlaceHolder = _::placeHolder($column);
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "AND $column $bindPlaceHolder" : "$column $bindPlaceHolder");
    }

    public function orHaving($column, $value){
        $column = _::hasOperator($column) ? $column : "$column=";
        $bindPlaceHolder = _::placeHolder($column);
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "OR $column $bindPlaceHolder" : "$column $bindPlaceHolder");
    }

    public function bind(&$statement)
    {
        $this->params->bind($statement);
    }
}