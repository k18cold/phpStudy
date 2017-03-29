<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/22
 * Time: 11:43
 */
abstract class MarketData
{
    //
    private $m_path;

    function __construct($path)
    {
        $this->m_path = $path;
    }

    public static function getTempPath(){
        return '/tmp/HouseMarket/'.date('Ymda').'/';
    }

    public static function getDataPath(){
        return '/tmp/HouseMarket/datas/'.date('Ymda').'-';
    }

    public function createDir($aimUrl) {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                $result = mkdir($aimDir);
            }
        }
        return $result;
    }

    public function url_get($url)
    {
        $con = curl_init();
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36');
        $tem = curl_exec($con);
        curl_close($con);
        return $tem;
    }
    public function cut($str, $start, $end)
    {
        $temp = $str;
        if (strpos($str, $start) !== false) {
            $temp = substr($str, strpos($str, $start) + strlen($start));
        }
        if (!empty($end) && strpos($temp, $end) > 0) {
            $temp = substr($temp, 0, strpos($temp, $end));
        }
        return $temp;
    }

    public function rcut($str, $end, $start)
    {
        $index_end = strpos($str, $end);
        $index_start = strrpos(substr($str, 0, $index_end), $start) + strlen($start);
        return substr($str, $index_start, $index_end - $index_start);
    }

    public function contains($str, $t)
    {
        if (!strpos($str, $t) === false) {
            return true;
        } else {
            return false;
        }
    }

    function getContent($url)
    {
        if (!file_exists($this->m_path)){
            self::createDir($this->m_path);
        }
        $file_temp = $this->getFileTemp($url);
        if (file_exists($file_temp)) {
            $result = file_get_contents($file_temp);
        } else {
            echo 'downloading:'.$url."\n";
            $result = self::url_get($url);
            file_put_contents($file_temp, $this->convertToUtf8($result));
        }
        return $result;
    }

    function getFileTemp($url){
        return $this->m_path.'/'.md5($url).'.cache';
    }

    abstract function getAllData();

    abstract function convertToUtf8($content);

    abstract function parseToList($content);
}