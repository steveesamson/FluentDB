<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/19/16
 * Time: 3:07 PM
 */

namespace slicks\db;


class DbConnectionFactory
{
    private static $settings = [];
    private static $pdo = null;
    private static $debug = false;


    private static function create()
    {

        $options = [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        if(isset(self::$settings['debug'])){
            $deb = self::$settings['debug'];
            self::$debug = (is_bool($deb) && $deb == true);
        }
        self::$pdo = new \PDO(
            sprintf(
                'mysql:host=%s;dbname=%s;port=%s;charset=utf8',
                self::$settings['host'],
                self::$settings['database'],
                self::$settings['port']
//                self::$settings['charset']
            ),
            self::$settings['username'],
            self::$settings['password'],
            $options
        );
    }

    public static function init($config)
    {
        if (!is_array($config)) {
            throw new \Exception("DbConnectionFactory init requires and array, which represents DB DNS configuration");
        }
        self::$settings = $config;
        self::create();
    }

    public static function setDatabase($db)
    {
        self::$settings['database'] = $db;
    }

    public static function setHost($host)
    {
        self::$settings['host'] = $host;
    }

    public static function setPort($port)
    {
        self::$settings['port'] = $port;
    }

    public static function setUsername($username)
    {
            self::$settings['username'] = $username;
    }

    public static function setPassword($password)
    {
            self::$settings['password'] = $password;
    }

    public static function debug($trueOrFalse){
        self::$debug = $trueOrFalse;
    }

    public static function getDb()
    {
        if (is_null(self::$pdo)) {
            self::create();
        }

        $db =  new Db(self::$pdo);
        return $db->debug(self::$debug);

    }

}