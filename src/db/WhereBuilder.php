<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/22/16
 * Time: 2:30 PM
 */

namespace slicks\db;

use slicks\db\Utils as _;

class WhereBuilder extends StringBuilder
{
    protected $params = null;

    public function __construct($initial='')
    {
        parent::__construct($initial);
        $this->params = new Params();
    }

    public function where($column, $value)
    {

        $column = _::hasOperator($column) ? $column : "$column =";
        $bindPlaceHolder = _::placeHolder(_::trimColumn($column));
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "AND $column $bindPlaceHolder" : "$column $bindPlaceHolder");
    }

    public function orWhere($column, $value)
    {
        $column = _::hasOperator($column) ? $column : "$column =";
        $bindPlaceHolder = _::placeHolder(_::trimColumn($column));
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "OR $column $bindPlaceHolder" : "$column $bindPlaceHolder");
    }

    public function whereIn($column, $value)
    {
        $value = _::quoteIf($value);
        $this->append(!$this->isEmpty() ? "AND $column IN ($value)" : "$column IN ($value)");
    }

    public function orWhereIn($column, $value)
    {
        $value = _::quoteIf($value);
        $this->append(!$this->isEmpty() ? "OR $column IN ($value)" : "$column IN ($value)");
    }

    public function whereNotIn($column, $value)
    {
        $value = _::quoteIf($value);
        $this->append(!$this->isEmpty() ? "AND $column NOT IN ($value)" : "$column NOT IN ($value)");
    }

    public function orwhereNotIn($column, $value)
    {
        $value = _::quoteIf($value);
        $this->append(!$this->isEmpty() ? "OR $column NOT IN ($value)" : "$column NOT IN ($value)");
    }

    public function like($column, $value, $position)
    {

        if ($position == 'left' || $position == 'l') {
            $value = "%$value";
        }

        if ($position == 'right' || $position == 'r') {
            $value = "$value%";
        }
        if ($position == 'both' || $position == 'b') {
            $value = "%$value%";
        }
        $bindPlaceHolder = _::placeHolder($column);
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "AND $column LIKE $bindPlaceHolder" : "$column LIKE $bindPlaceHolder");
    }

    public function notLike($column, $value, $position)
    {

        if ($position == 'left' || $position == 'l') {
            $value = "%$value";
        }

        if ($position == 'right' || $position == 'r') {
            $value = "$value%";
        }
        if ($position == 'both' || $position == 'b') {
            $value = "%$value%";
        }
        $bindPlaceHolder = _::placeHolder($column);
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "AND $column NOT LIKE $bindPlaceHolder" : "$column NOT LIKE $bindPlaceHolder");
    }

    public function orLike($column, $value, $position)
    {

        if ($position == 'left' || $position == 'l') {
            $value = "%$value";
        }

        if ($position == 'right' || $position == 'r') {
            $value = "$value%";
        }
        if ($position == 'both' || $position == 'b') {
            $value = "%$value%";
        }
        $bindPlaceHolder = _::placeHolder($column);
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "OR $column LIKE $bindPlaceHolder" : "$column LIKE $bindPlaceHolder");
    }

    public function orNotLike($column, $value, $position)
    {

        if ($position == 'left' || $position == 'l') {
            $value = "%$value";
        }

        if ($position == 'right' || $position == 'r') {
            $value = "$value%";
        }
        if ($position == 'both' || $position == 'b') {
            $value = "%$value%";
        }
        $bindPlaceHolder = _::placeHolder($column);
        $this->params->add($bindPlaceHolder, $value);
        $this->append(!$this->isEmpty() ? "OR $column NOT LIKE $bindPlaceHolder" : "$column NOT LIKE $bindPlaceHolder");
    }

    public function params(){
        return $this->params->map();
    }

    public function bind(&$statement)
    {
        $this->params->bind($statement);
    }

}