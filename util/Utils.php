<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/30
 * Time: 16:55
 */
class Utils
{
    public static function cut($str, $start, $end)
    {
        $temp = $str;
        if (empty($str)){
            return "";
        }
        if (strpos($str, $start) !== false) {
            $temp = substr($str, strpos($str, $start) + strlen($start));
        }
        if (!empty($end) && strpos($temp, $end) !== false) {
            $temp = substr($temp, 0, strpos($temp, $end));
        }
        return $temp;
    }

    public static function getFeildFromUrl($url, $k){
        return self::convertUrlQuery(parse_url($url)['query'])[$k];
    }

    static function getUrlQuery($array_query)
    {
        $tmp = array();
        foreach($array_query as $k=>$param)
        {
            $tmp[] = $k.'='.$param;
        }
        $params = implode('&',$tmp);
        return $params;
    }
    static function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    public static function trim($content)
    {
        $tmp = strip_tags(str_replace('&nbsp;','',str_replace("\n", '', str_replace('	', '', str_replace(' ', '', $content)))));
        return chop($tmp, ",");
    }

    public static function contains($str, $t)
    {
        if (!strpos($str, $t) === false) {
            return true;
        } else {
            return false;
        }
    }
}