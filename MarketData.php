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

    public function url_request($url, $fields)
    {
        $con = curl_init();
        if (empty($fields)) {
            curl_setopt($con, CURLOPT_URL, $url);
            curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($con, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36');
        }else {
            curl_setopt($con, CURLOPT_POSTFIELDS, $fields);
        }
            $tem = curl_exec($con);
            $code = curl_getinfo($con,CURLINFO_HTTP_CODE);
            curl_close($con);
            return array('code'=>$code, 'content'=>$tem);
    }

    function send_post($url, $post_data) {

        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
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

    public static function rcut($str, $end, $start)
    {
        $index_end = strpos($str, $end);
        $index_start = strrpos(substr($str, 0, $index_end), $start) + strlen($start);
        return substr($str, $index_start, $index_end - $index_start);
    }

    public static function contains($str, $t)
    {
        if (!strpos($str, $t) === false) {
            return true;
        } else {
            return false;
        }
    }

    function getContent($url, $fields)
    {
        if (!file_exists($this->m_path)){
            self::createDir($this->m_path);
        }
        $file_temp = $this->getFileTemp($url, $fields);
        if (file_exists($file_temp)) {
            $result = file_get_contents($file_temp);
        } else {
            echo 'downloading:'.$url."\n";
            $rResult = self::url_request($url, $fields);
            if ($rResult['code'] != 400){
                $result = "";
            }else{
                $result = $rResult['content'];
                file_put_contents($file_temp, $this->convertToUtf8($result));
            }
        }
        return $result;
    }

    function getFileTemp($url, $fields){
        $f = empty($fields) ? '': array_keys($fields);
//        echo $this->m_path.md5($url.$f).'.cache';
//        echo "\n";
        return $this->m_path.md5($url.$f).'.cache';
    }

    abstract function getAllData();

    abstract function convertToUtf8($content);

    abstract function parseToList($content);

    abstract function printf($datas);
}