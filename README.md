# FluentDB
FluentDB allows the expressive writing of database queries and routines. FluentDB is fluent, which is intuitive as you can nearly guess what should come next even if you are just getting started with FluentDB. FluentDB is not an ORM. It was developed to allow folks coming from relational databases background write expressive queries with object interactions in mind.

##FluentDB options
FluentDB takes all the options/config allowed by `PDO`. Please see http://php.net/manual/en/book.pdo.php for details. It also has, in addition, `debug_db` option which could be `true/false`. `debug_db` enables the logging of the raw queries to the console when it is set to *true*, useful while developing.


## Installation

```cli
  composer require Slicks/FluentDB
```

## Usage

Using FluentDB is pure joy:

```php

       use slicks\db\DbConnectionFactory as DbFac;
       $options = [
                      'host' => 'devmac',
                      'port' => '3306',
                      'username' => 'tester',
                      'database' => 'todo_db',
                      'password' => 'tester'
                  ];
                  
       //Init Db factory;                  
       DbFac::init($options);
       //Let us now connect and get a db object
       $db = DbFac::getDb();
       //Do db stuffs here
          
```

##FluentDB in action

Now that we have a valid `db` object, how do we use it? Well, see the following:


##`fetch`ing records

```php
   
    $db->fetch('todo', function ($e, $rows) {
        if ($e) {
            throw new Exception($e);
        }
        print_r($rows);
    });
    
```

The above is used when all record fields are needed. However, if a subset of the fields are of interest, **`select`** with **`from`** and **`fetch`** is the way to go.

##`select`ing records

```php
         
         $db->select('id, task')
             ->from('todo')
             ->fetch(function ($e, $rows) {
                 if ($e) {
                     throw new Exception($e);
                 }
                 print_r($rows);
             });
         
         
         
```

##`query`ing records with `query`

```php

    $q = "insert into todo (task, task_owner) values ('Vacuum the floor',1),('Iron my shirt', 1)";
    $this->db->query($q, function ($e, $res) {
        if ($e) {
            throw new Exception($e);
        }
       print_r($res);
    });
    
```

**Note:** The use of ONLY **`fetch`** or in conjunction with **`select`** and **`from`** does not change the outcome. I think it just depends on what flavour you like or the need at hand. That being said, all the examples are written in one or other flavour but what was done in one flavour can equally be done in the other flavour.

###`where`
```php

     $db->where('id', 1)
        ->fetch('todo', function ($err, $rows) {
             if ($err) {
                 throw new Exception($err);
             }
              print_r($rows);
         });
```

```php

     $db->where('id >', 1)
       ->fetch('todo', function ($err, $rows) {
             if ($err) {
                  throw new Exception($err);
              }
               print_r($rows);
         });
```

```php

     $db->where('id <', 10)
        ->fetch('todo', function ($err, $rows) {
             if ($err) {
                  throw new Exception($err);
              }
               print_r($rows);
         });
```

```php

     $db->where('id >=', 1)
        ->fetch('todo', function ($err, $rows) {
             if ($err) {
                   throw new Exception($err);
               }
                print_r($rows);
         });
```

```php

     $db->where('id <=', 10)
        ->fetch('todo', function ($err, $rows) {
             if ($err) {
                   throw new Exception($err);
               }
                print_r($rows);
         });
```

###`where`, `orWhere`, `whereIn`, `orWhereIn`, `whereNotIn`, `orWhereNotIn` conditions
Please, note that all the variations that apply to **`where`** also apply to the following: `orWhere`, `whereIn`, `orWhereIn`, `whereNotIn`, `orWhereNotIn`.

###`orWhere`
```php

     $db->where('id', 10)
       ->orWhere('task_owner', 1)
       ->fetch('todo', function ($err, $rows) {
            if ($err) {
                  throw new Exception($err);
              }
            print_r($rows);
         });
```

###`whereIn`

```php

    $db->select('todo.*') //I could have used fetch directly here too
       ->from('todo')
       ->whereIn('id', [1,3])
       ->fetch(function ($err, $rows) {
            if ($err) {
                  throw new Exception($err);
            }
            print_r($rows);
    });
```

###`orWhereIn`

```php

    $db->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->where('id', 2)
      ->orWhereIn('id', [1,3])
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`whereNotIn`

```php

    $db->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->whereNotIn('id', "1,2,3")
      ->fetch(function ($err, $rows) {
            if ($err) {
                  throw new Exception($err);
            }
            print_r($rows);
    });
```

###`orWhereNotIn`

```php

    $db->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->where('id', 2)
      ->orWhereNotIn('id', "1,3")
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`like`

Generates `task like %vacuum%` , **`b`** or **`both`**  for both ends are allowed.

```php

    $db->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->like('task', 'vacuum', 'b')
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
        });
```

###`orLike`

Generates `task like '%vacuum' or task like 'iron%'` , **`l`** or **`left`**  for left end are allowed, while **`r`** or **`right`**  for right end are allowed.

```php

    $d->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->like('task', 'vacuum', 'l')
      ->orLike('task', 'iron', 'r')
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`notLike`

Generates `task NOT like '%vacuum%'` , **`b`** or **`both`**  for both ends are allowed.

```php

    $db->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->notLike('task', 'vacuum', 'b')
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
          });
```

###`orNotLike`

Generates `OR task NOT like '%dishes'` , **`l`** or **`left`**  for left end are allowed.

```php

    $db->select('todo.*') //I could have used fetch directly here too
      ->from('todo')
      ->where('id', 2)
      ->orNotLike('task', 'dishes', 'l')
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`limit`

```php

    $db->limit(2) //I could have used select, from + fetch here too
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`limit` with `offset`

```php

    $db->limit(2, 0) //I could have used select, from + fetch here too
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`orderBy (desc)`

```php

    $db>orderBy('id', 'desc') //I could have used select, from + fetch here too
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`orderBy ([asc])` the direction is optional if ascending order is desired

```php

    $db->orderBy('id', 'asc') //I could have used select, from + fetch here too
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

Same as below:

```php

    $db->orderBy('id') //I could have used select, from + fetch here too
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
       });
```

###`join`ing tables

```php

    $db->select('t.*, o.name')
      ->from('todo t')
      //'left', for left join, also 'right', 'outer' etc are allowed
      ->join('task_owners o', 't.task_owner = o.id', 'left')
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`groupBy` for aggregates

```php

    $db->select('o.name, count(*) tasks')
      ->from('task_owners o')
      ->join('todo t', 't.task_owner = o.id', 'left')
      ->groupBy('o.name')
     ->fetch(function ($err, $rows) {
             if ($err) {
                   throw new Exception($err);
             }
             print_r($rows);
       });
```

###`having` for aggregates

```php

    $db->select('o.name, count(*) tasks')
      ->from('task_owners o')
      ->join('todo t', 't.task_owner = o.id', 'left')
      ->groupBy('o.name')
      ->having('tasks >', 2)
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

###`orHaving` for aggregates

```php

    $db->select('o.name, count(*) tasks')
      ->from('task_owners o')
      ->join('todo t', 't.task_owner = o.id', 'left')
      ->groupBy('o.name')
      ->having('tasks >', 2)
      ->orHaving('tasks', 3)
      ->fetch(function ($err, $rows) {
              if ($err) {
                    throw new Exception($err);
              }
              print_r($rows);
      });
```

##`insert`ing records

###`insert` - single record per insert

```php

    $db->insert('task_owners', ['name' => 'Test owner'], function ($e, $res) {
        if ($e) {
            throw new Exception($e);
        }
        echo($res->id);
    });
```

###inserting multiple records with `query`

```php

        $q = "insert into todo (task, task_owner) values ('Vacuum the floor',1),('Iron my shirt', 1)";
        $db->query($q, function ($e, $res) {
            if ($e) {
                throw new Exception($e);
            }
            echo($res->affectedRows);
        });
```



###`update`ing records

```php

       $db->set('task', 'Updated Todo')
           ->set('earnings', 0.99)
           ->whereIn('id', [1,3])
           ->update('todo', function ($e, $res) {
               if ($e) {
                   throw new Exception($e);
               }
               echo($res->affectedRows);
           });
```

###`delete`ing records

```php

       $db->where('id', 2)
           ->delete('todo', function ($e, $res) {
               if ($e) {
                   throw new Exception($e);
               }

               echo($res->affectedRows);
           });
```


##Test
Before running the tests, load the included script **test_scripts.sql** onto your mysql database. Ensure to load the script as 'root' for you need to grant privileges. Thereafter, run;

```cli
    vendor/bin/peridot tests/specs
```
