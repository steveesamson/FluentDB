<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/23/16
 * Time: 2:00 PM
 */
require_once __DIR__ . "/../../vendor/autoload.php";

use slicks\db\DbConnectionFactory as DbFac;

describe('#SlicksPHP-Db', function () {

    beforeEach(function () {
        $cfg = [
            'host' => 'devmac',
            'port' => '3306',
            'username' => 'tester',
            'database' => 'todo_db',
            'password' => 'tester',
            'debug_db' => true
        ];
        DbFac::init($cfg);
        $this->db = DbFac::getDb();
//        echo 'setup ok';
    });
    context('#Insert', function () {

        describe('#Insert with "insert" ', function () {
            it('Should insert into "task_owners" table without error and return insert id that equals 1', function () {
                $this->db->insert('task_owners', ['name' => 'Test owner'], function ($e, $res) {
                    if ($e) {
                        throw new Exception($e);
                    }
                    expect($res->id)->to->be->equal(1);
                });


            });
        });

        describe('#Insert with "insert" ', function () {
            it('Should insert into "todo" table without error and return insert id that equals 1', function () {
                $this->db->insert('todo', ['task' => 'Do dishes', 'task_owner' => 1, 'earnings'=>10.99], function ($e, $res) {
                    if ($e) {
                        throw new Exception($e);
                    }
                    expect($res->id)->to->be->equal(1);
                });
            });
        });

        describe('#Insert multiple with "query"', function () {
            it('Should insert multiple records into "todo" table with "query", affected rows should be 2', function () {
                $q = "insert into todo (task, task_owner) values ('Vacuum the floor',1),('Iron my shirt', 1)";
                $this->db->query($q, function ($e, $res) {
                    if ($e) {
                        throw new Exception($e);
                    }
                    expect($res->affectedRows)->to->be->equal(2);
                });
            });
        });
    });


    context('#Fetch', function () {
        describe('#All records', function () {
            it('Should retrieve all records in "todo"  table with "fetch" without error, records length should be 3', function () {
                $this->db->fetch('todo', function ($e, $rows) {
                    if ($e) {
                        throw new Exception($e);
                    }
                    expect($rows)->to->have->length(3);
                });
            });
        });

        describe('#GreaterThan clause', function () {
            it('Should retrieve all records with id greater than 1 in "todo"  table with "where >" without error, records length should be 2', function () {
                $this->db->where('id >', 1)
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(2);
                    });
            });
        });

        describe('#GreaterThanOrEquals clause', function () {
            it('Should retrieve all records with id greater than  or equals 1 in "todo"  table with "where >=" without error, records length should be 3', function () {
                $this->db->where('id >=', 1)
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(3);
                    });
            });
        });

        describe('#LessThan clause', function () {
            it('Should retrieve all records with id less than 2 in "todo"  table with "where <" without error, records length should be 1', function () {
                $this->db->where('id <', 2)
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(1);
                    });
            });
        });

        describe('#LessThanOrEquals clause', function () {
            it('Should retrieve all records with id less than  or equals 2 in "todo"  table with "where <=" without error, records length should be 2', function () {
                $this->db->where('id <=', 2)
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(2);
                    });
            });
        });

        describe('#Limit clause', function () {
            it('Should retrieve ONLY 2 records in "todo"  table with "limit" of 2 without error, records length should be 2', function () {
                $this->db->limit(2)
                ->fetch('todo', function ($e, $rows) {
                    if ($e) {
                        throw new Exception($e);
                    }
                    expect(count($rows))->to->be->equal(2);
                });
            });
        });


        describe('#OrderBy DESC clause', function () {
            it('Should retrieve ALL records in "todo"  table with "orderby" of "desc" without $eor, records length should be 3, first record id should be 3', function () {
                $this->db->orderBy('id', 'desc')
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(3);
                        expect((int)$rows[0]->id)->to->be->equal(3);

                    });
            });
        });

        describe('#OrderBy ASC clause', function () {
            it('Should retrieve ALL records in "todo"  table with "orderby" of "asc"  without $eor, records length should be 3, first record id should be 1', function () {
                $this->db->orderBy('id', 'asc')
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(3);
                        expect((int)$rows[0]->id)->to->be->equal(1);

                    });
            });
        });

        describe('#Just OrderBy clause, same as orderBy ASC', function () {
            it('Should retrieve ALL records in "todo"  table with just "orderby"  without $eor, records length should be 3, first record id should be 1', function () {
                $this->db->orderBy('id')
                    ->fetch('todo', function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect(count($rows))->to->be->equal(3);
                        expect((int)$rows[0]->id)->to->be->equal(1);

                    });
            });
        });

    });

    context('#Select', function(){

        describe('#Select fields', function () {
            it('Should retrieve ONLY "id" and "task" from all records in "todo"  table with "select" without $eor, field isset("task_owner") should be false in any records', function () {
                $this->db->select('id, task')
                    ->from('todo')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        $a_rec = $rows[0];
                        expect(isset($a_rec->task_owner))->to->be->equal(false);
                    });
            });
        });

        describe('#Where clause', function () {
            it('Should retrieve ONLY ONE record, from  "todo"  table, record id should equal 2', function () {
                $this->db->select('id, task')
                    ->from('todo')
                    ->where('id', 2)
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(1);
                        expect((int)$rows[0]->id)->to->be->equal(2);
                    });
            });
        });

        describe('#WhereIn clause', function () {
            it('Should retrieve all records with ids 1 and 3 from  "todo"  table, record length should equal 2', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->whereIn('id', [1,3])
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(2);
                    });
            });
        });

        describe('#OrWhereIn clause', function () {
            it('Should retrieve all records with ids 1, 2 and 3 from  "todo"  table, record length should equal 3', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->where('id', 2)
                    ->orWhereIn('id', [1,3])
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(3);
                    });
            });
        });

        describe('#WhereNotIn clause', function () {
            it('Should retrieve all records with ids not amongst 1, 2 and 3 from  "todo"  table, record length should equal 0', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->whereNotIn('id', [1,2,3])
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(0);


                    });
            });
        });

        describe('#OrWhereNotIn clause', function () {
            it('Should retrieve all records with ids 2 or ids not amongst 1 and 3 from  "todo"  table, record length should equal 1', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->where('id', 2)
                    ->orWhereNotIn('id', [1,3])
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(1);


                    });
            });
        });

        describe('#Like clause', function () {
            it('Should retrieve all records with task like "Vacuum" from  "todo"  table, record length should equal 1', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->like('task', 'vacuum', 'b')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(1);
                    });
            });
        });

        describe('#OrLike clause', function () {
            it('Should retrieve all records with task like "Vacuum"  or with task like "iron" from  "todo"  table, record length should equal 2', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->like('task', 'vacuum', 'b')
                    ->orLike('task', 'iron', 'b')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(2);


                    });
            });
        });
//
        describe('#NotLike clause', function () {
            it('Should retrieve all records with task not like "Vacuum" from  "todo"  table, record length should equal 2', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->notLike('task', 'vacuum', 'b')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(2);


                    });
            });
        });

        describe('#OrNotLike clause', function () {
            it('Should retrieve all records with task not like "Vacuum" or task not like "Vacuum" from  "todo"  table, record length should equal 2', function () {
                $this->db->select('todo.*')
                    ->from('todo')
                    ->where('id', 2)
                    ->orNotLike('task', 'dishes', 'b')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(2);
                    });
            });
        });

        describe('#Join clause', function () {

            it('Should retrieve all records(with ALL fields from todo and the "name" field from task_owners tables) by using "join", record length should equal 3 and field "name" should be defined', function () {

                $this->db->select('t.*, o.name')
                    ->from('todo t')
                    ->join('task_owners o', 't.task_owner = o.id', 'left')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(3);
                        $a_rec = $rows[0];
                        expect($a_rec->name)->to->not->be->null;
                    });

            });
        });


        describe('#GroupBy clause(Aggregate)', function () {

            it('Should retrieve a single record containing the "name" from "task_owners" table and "tasks" as "todo" counts  from "todo" table for the specific task_owner"  by using "groupby", record length should equal 1 and fields "name"  and "tasks" should be defined', function () {

                $this->db->select('o.name, count(*) tasks')
                    ->from('task_owners o')
                    ->join('todo t', 't.task_owner = o.id', 'left')
                    ->groupBy('o.name')
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(1);
                        $a_rec = $rows[0];
                        expect($a_rec->tasks)->to->not->be->null;
                        expect($a_rec->name)->to->not->be->null;


                    });

            });
        });

        describe('#Having clause( with Aggregate)', function () {

            it('Should retrieve a single record containing the "name" from "task_owners" table and "tasks" as "todo" counts  from "todo" table with record having "task" greater than 2, record length should equal 1 and fields "name"  and "tasks" should be defined', function () {

                $this->db->select('o.name, count(*) tasks')
                    ->from('task_owners o')
                    ->join('todo t', 't.task_owner = o.id', 'left')
                    ->groupBy('o.name')
                    ->having('tasks >', 2)
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }

                        expect($rows)->to->have->length(1);
                        $a_rec = $rows[0];
                        expect($a_rec->tasks)->to->not->be->null;
                        expect($a_rec->name)->to->not->be->null;


                    });

            });
        });

        describe('#OrHaving clause( with Aggregate)', function () {

            it('Should retrieve a single record containing the "name" from "task_owners" table and "tasks" as "todo" counts  from "todo" table with record having "tasks" greater than 2 or having "tasks" equals 3, record length should equal 1 and fields "name"  and "tasks" should be defined', function () {

                $this->db->select('o.name, count(*) tasks')
                    ->from('task_owners o')
                    ->join('todo t', 't.task_owner = o.id', 'left')
                    ->groupBy('o.name')
                    ->having('tasks >', 2)
                    ->orHaving('tasks', 3)
                    ->fetch(function ($e, $rows) {
                        if ($e) {
                            throw new Exception($e);
                        }
                        expect($rows)->to->have->length(1);
                        $a_rec = $rows[0];
                        expect($a_rec->tasks)->to->not->be->null;
                        expect($a_rec->name)->to->not->be->null;


                    });

            });
        });


    });

    describe('#Update ', function () {
        it('Should update "todo" table. Should return 2 as number of affectedRows', function () {

            $this->db->set('task', 'Updated Todo')
                ->set('earnings', 0.99)
                ->whereIn('id', [1,3])
                ->update('todo', function ($e, $res) {
                    if ($e) {
                        throw new Exception($e);
                    }
                    expect($res->affectedRows)->to->be->equal(2);
                });
        });
    });


    describe('#Delete ', function () {
        it('Should delete from "todo" table and return 1 as number of affectedRows', function () {

            $this->db->where('id', 2)
                ->delete('todo', function ($e, $res) {
                    if ($e) {
                        throw new Exception($e);
                    }

                    expect($res->affectedRows)->to->be->equal(1);


                });

        });
    });



});

