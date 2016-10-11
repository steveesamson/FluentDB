<?php
/**
 * Created by PhpStorm->
 * User: steve
 * Date: 9/19/16
 * Time: 12:01 PM
 */

namespace slicks\db;

use slicks\db\Utils as _;


class Db
{

    const QUERY = 'Query';
    const PROC = 'Procedure';
    private $debug = false;
    private $dbhandle = null;
    private $offset = 0;
    private $lim = 0;
    private $isDistinct = false;
    private $froms;
    private $orderby;
    private $groupby;
    private $sets = null;
    private $mode = self::QUERY;
    private $lastQuery = null;
    private $whereConditions = null;
    private $selects = null;
    private $joins = null;
    private $havings = null;


    public function debug($trueOrFalse = true)
    {
        $this->debug = $trueOrFalse;
        return $this;
    }

    private function reset()
    {
        $this->offset = 0;
        $this->lim = 0;
        $this->mode = self::QUERY;
        $this->isDistinct = false;

        $this->froms = new StringBuilder();
        $this->groupby = new StringBuilder();
        $this->orderby = new StringBuilder();
        $this->sets = new SetBuilder();
        $this->whereConditions = new WhereBuilder();
        $this->selects = new StringBuilder();
        $this->joins = new StringBuilder();
        $this->havings = new HavingBuilder();
        return $this;
    }


    public function __construct($pdo)
    {
        $this->dbhandle = $pdo;
        $this->reset();
    }

    public function distinct()
    {
        $this->isDistinct = true;
        return $this;
    }


    public function query($q, $cb)
    {
        $this->setLastQuery($q);
        $this->reset();
        $stm = $this->dbhandle->prepare($this->lastQuery);
        $stm->execute();

        if (_::startsWith(strtolower($this->lastQuery), 'select')) {
            $rows = $stm->fetchAll(PDO::FETCH_OBJ);
            $cb(false, $rows);
        } else {
            if ($cb) {
                $cb(false, (object)['affectedRows' => $stm->rowCount()]);
            }
        }


    }

    public function join($table, $condition, $type)
    {
        switch (strtolower($type)) {
            case 'l':
            case 'left':
                $type = 'left';
                break;
            case 'r':
            case 'right':
                $type = 'right';
                break;
            case 'i':
            case 'inner':
                $type = 'inner';
                break;
            case 'o':
            case 'outer':
                $type = 'outer';
                break;

        }
        $type = strtoupper($type);
        $this->joins->append("$type JOIN $table ON $condition");
        return $this;
    }

    public function limit($lim, $offset = 0)
    {
        $this->lim = $lim;
        $this->offset = $offset;//!is_null($offset) ? $offset : 0;
        return $this;
    }

    public function select($select)
    {
        $this->selects->reInit($select);// = trim(explode(',', $select));
        return $this;
    }

    public function from($from)
    {
        $this->froms->reInit($from); //= trim(explode(",", $from));
        return $this;
    }

    public function set($column, $value)
    {
        $this->sets->set($column, $value);
        return $this;
    }

    public function where($column, $value)
    {

        $this->whereConditions->where($column, $value);

        return $this;
    }


    public function orWhere($column, $value)
    {

        $this->whereConditions->orWhere($column, $value);
        return $this;
    }

    public function whereIn($column, $value)
    {

        $this->whereConditions->whereIn($column, $value);
        return $this;
    }

    public function whereNotIn($column, $value)
    {
        $this->whereConditions->whereNotIn($column, $value);
        return $this;
    }

    public function orWhereIn($column, $value)
    {
        $this->whereConditions->orWhereIn($column, $value);
        return $this;
    }

    public function orWhereNotIn($column, $value)
    {
        $this->whereConditions->orWhereNotIn($column, $value);
        return $this;
    }


    public function like($column, $value, $position)
    {
        $this->whereConditions->like($column, $value, $position);
        return $this;
    }

    public function orLike($column, $value, $position)
    {
        $this->whereConditions->orLike($column, $value, $position);
        return $this;
    }

    public function orNotLike($column, $value, $position)
    {
        $this->whereConditions->orNotLike($column, $value, $position);
        return $this;
    }

    public function notLike($column, $value, $position)
    {
        $this->whereConditions->orNotLike($column, $value, $position);
        return $this;
    }


    public function groupBy($columns)
    {
        $this->groupby->reInit(_::trim(_::split($columns, ",")));
        return $this;
    }

    public function orderBy($columns, $direction = 'asc')
    {
        $this->orderby->reInit(_::trim(_::split($columns, ",")));
        $this->order = $direction;
        return $this;
    }

    public function having($column, $value)
    {
        $this->havings->having($column, $value);
        return $this;
    }

    public function orHaving($column, $value)
    {
        $this->havings->orHaving($column, $value);
        return $this;
    }


    public function insert($table, $options, $cb)
    {
        $sb = new StringBuilder('');
        $vsb = new StringBuilder('');

        $sb->append("INSERT INTO\n")
            ->append($table)
            ->append("\n(\n");
        $vsb->append("\nVALUES\n(\n");

        $firstEntry = true;
        $p = new Params();
        foreach ($options as $key => $val) {
            if (!$firstEntry) {
                $sb->append(",\n");
                $vsb->append(",\n");
            }
            $firstEntry = false;

            $sb->append(trim($key));


            $k = ':' . trim($key);
            $p->add($k, $val);

            $vsb->append($k);
        }

        $sb->append("\n)");
        $vsb->append("\n)\n");

        $sb = $sb->toString();
        $vsb = $vsb->toString();
        $this->setLastQuery("$sb$vsb");
        if ($this->debug) {
            echo($this->lastQuery);
        }
        $stm = $this->dbhandle->prepare($this->lastQuery);
        $p->bind($stm);
        $this->reset();
        $stm->execute();
        $cb(false, (object)['id' => (int)$this->dbhandle->lastInsertId()]);

    }

    public function compile()
    {
        $this->compileSelect();
        return $this->lastQuery;
    }


    public function fetch($tableOrcb, $cb = null)
    {

        if ($this->mode == 'QUERY') {
            if (is_callable($tableOrcb)) {
                if (is_null($this->froms) || $this->froms->isEmpty()) {
                    $e = "No table specified for select statement";
                    (!is_null($tableOrcb) && $tableOrcb($e));
                    return;
                }

                $this->compileSelect();
                if ($this->debug) {
                    echo($this->lastQuery);
                }

                $stm = $this->dbhandle->prepare($this->lastQuery);
                $this->whereConditions->bind($stm);
                $this->havings->bind($stm);
                $this->reset();
                $stm->execute();

                $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
//                print_r($stm->debugDumpParams());
                $tableOrcb(false, $rows);

            } else if (is_string($tableOrcb)) {

                $this->froms->reInit([$tableOrcb]);
                $this->fetch($cb);

            } else {
                $e = "No table or callback specified for select statement";
                throw new IllegalStateException($e);
            }

        }

    }

    public function update($table, $cb)
    {
        if (is_null($this->sets) || $this->sets->isEmpty()) {

            $e = "No fields to be set in update statement.";
            ($cb($e));
            return;
        }

        $this->compileUpdate($table);
        if ($this->debug) {
            echo($this->lastQuery);
        }

        $stm = $this->dbhandle->prepare($this->lastQuery);
        $this->sets->bind($stm);
        $this->whereConditions->bind($stm);
        $this->reset();
        $stm->execute();
//                print_r($stm->debugDumpParams());
        $cb(false, (object)['affectedRows' => $stm->rowCount()]);

    }

    public function delete($tableName, $options, $cb = null)
    {

        if (isset($options) && is_callable($options)) {
            $this->compileDelete($tableName);

            if ($this->debug) {
                echo($this->lastQuery);
            }

            $stm = $this->dbhandle->prepare($this->lastQuery);

//            print_r($this->whereConditions->params());

            $this->whereConditions->bind($stm);
            $this->reset();
            $stm->execute();
//            print_r($stm->debugDumpParams());
//
            $options(false, (object)['affectedRows' => $stm->rowCount()]);

        } else if (is_object($options)) {

            foreach ($options as $k => $v) {
                $this->whereConditions->where($k, $v);
            }
            $this->delete($tableName, $cb);
        }


    }


    //Private non-static functions
    private function setLastQuery($query)
    {
        $this->lastQuery = $query;
    }


    private function compileDelete($tableName)
    {
        $sb = new StringBuilder('');
        $sb->append("DELETE FROM\n")
            ->append($tableName);
        if (!$this->whereConditions->isEmpty()) {
            $sb->append("\nWHERE\n")
                ->append($this->whereConditions->join(" "))
                ->append("\n");
        }
        $this->setLastQuery($sb->toString());


    }

    private function compileUpdate($tableName)
    {
        $sb = new StringBuilder('');
        $sb->append("UPDATE \n")
            ->append($tableName)
            ->append("\n")
            ->append("SET\n")
            ->append($this->sets->join(','))
            ->append("\n");
        if (!$this->whereConditions->isEmpty()) {
            $sb->append("WHERE\n")
                ->append($this->whereConditions->join(" "))
                ->append("\n");
        }
        $this->setLastQuery($sb->toString());

    }

    private function compileSelect()
    {
        $sb = new StringBuilder('');
        $sb->append("SELECT \n")
            ->append($this->isDistinct ? "DISTINCT\n" : "")
            ->append((!is_null($this->selects) && !$this->selects->isEmpty()) ? $this->selects->join(',') : "*")
            ->append("\n")
            ->append("FROM\n")
            ->append($this->froms->join(','))
            ->append("\n");

        if (!$this->joins->isEmpty()) {
            $sb->append($this->joins->join(" "))
                ->append("\n");
        }

        if (!$this->whereConditions->isEmpty()) {
            $sb->append("WHERE\n")
                ->append($this->whereConditions->join(" "))
                ->append("\n");
        }

        if (!is_null($this->groupby) && !$this->groupby->isEmpty()) { //====
            $sb->append("GROUP BY\n")
                ->append($this->groupby->join(','))
                ->append("\n");
        }

        if (!$this->havings->isEmpty()) {
            $sb->append("HAVING (\n")
                ->append($this->havings->join(" "))
                ->append("\n)\n");
        }

        if (!is_null($this->orderby) && !$this->orderby->isEmpty()) { //====
            $sb->append("ORDER BY\n")
                ->append($this->orderby->join(','))
                ->append("\n")
                ->append($this->order)
                ->append("\n");

        }

        if ($this->lim > 0) {
            $sb->append("LIMIT\n")
                ->append($this->lim)
                ->append("\n");
            if ($this->offset > 0) {
                $sb->append("OFFSET\n")
                    ->append($this->offset)
                    ->append("\n");
            }

        }

        $this->setLastQuery($sb->toString());
    }

}