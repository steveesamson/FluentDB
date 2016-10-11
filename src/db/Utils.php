<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 9/21/16
 * Time: 11:02 AM
 */

namespace slicks\db;


class Utils
{

    public static function startsWith($haystack, $with)
    {

        $exists = strpos($haystack, $with);
        return ($exists === false) ? false : ($exists === 0);
    }

    public static function startsWithIgnoreCase($haystack, $with)
    {

        return self::startswith(strtolower($haystack), strtolower($with));
    }

    public static function endsWith($haystack, $with)
    {

        $exists = strpos($haystack, $with);
        return ($exists === false) ? false : $with === substr($haystack, $exists);
    }

    public static function endsWithIgnoreCase($haystack, $with)
    {

        return self::endswith(strtolower($haystack), strtolower($with));
    }

    public static function split($s, $delimiter)
    {

        return preg_split("/$delimiter/", $s);
    }

    public static function trim($arr)
    {

        if (is_string($arr)) {
            return trim($arr);
        }

        return array_map(function ($e) {
            return trim($e);
        }, $arr);
    }


    public static function join($arr, $delimiter)
    {
        return array_reduce($arr, function ($a, $item) use ($delimiter) {
            $a .= empty($a) ? $item : $delimiter . $item;
            return $a;
        });
    }

    public static function toString($o)
    {
        return '' . (string)$o;
    }

    public static function stringWrap($o)
    {
        $raw = self::toString($o);

        return (strpos($raw, "'") !== false) ? $raw : "'$raw'";
    }

    public static function placeHolder($column)
    {
        $column = self::trimColumn($column);
        $bindPlaceHolder = ':' . $column;
        $hasDot = strpos($column, '.');
        if ($hasDot !== false) {
            $bindPlaceHolder = ":" . substr($column, $hasDot + 1);
        }
        return $bindPlaceHolder;
    }

    public static function trimColumn($column)
    {
        $trims = ['<','>','=', '>=', '<=', '<>', '!='];
        foreach ($trims as $t) {
            $column = str_replace($t, "", $column);
        }
        return $column;
    }

    public static function hasOperator($s)
    {
        $has = false;
        $s = trim($s);
        if (self::endsWith($s, ">") || self::endsWith($s, ">=") || self::endsWith($s, "<=") || self::endsWith($s, "<") || self::endsWith($s, "<>") || self::endsWith($s, "!=")) {
            $has = true;
        }
        return $has;
    }


    public static function quote($string)
    {
        $simplified = "";

        if (is_null($string) || empty($string)) {
            $simplified = "''";
        }
        if (is_array($string)) {
//            $arr = explode(',', $string);
            for ($i = 0; $i < count($string); ++$i) {
                $string[$i] = self::stringWrap($string[$i]);
            }
            $simplified = self::join($string, ',');
        } else {
            $simplified = self::stringWrap($string);
        }
        return $simplified;
    }

    public static function quoteIf($s)
    {
        if (is_array($s) && !empty($s)) {
            return is_string($s[0]) ? self::quote($s) : self::join($s,',');
        } else {
            return is_string($s) ? self::quote($s) : $s;
        }
    }


}