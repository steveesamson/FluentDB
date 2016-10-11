<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/25/16
 * Time: 4:46 PM
 */

namespace slicks\db;

use slicks\db\Utils as _;

class SetBuilder extends StringBuilder
{


    protected $params = null;

    public function __construct($initial='')
    {
        parent::__construct($initial);
        $this->params = new Params();
    }

    public function set($column, $value){

        $column = _::hasOperator($column) ? $column : "$column =";
        $bindPlaceHolder = _::placeHolder(_::trimColumn($column));
        $this->params->add($bindPlaceHolder, $value);
        $this->append("$column $bindPlaceHolder");
    }

    public function bind(&$statement)
    {
        $this->params->bind($statement);
    }
}