<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/22
 * Time: 12:01
 */
include('MarketData.php');
class MarketYGJY extends MarketData
{

    function parseToList($str)
    {
        $str = parent::cut($str, '住宅未售套数<', '<!-- main end -->');
        $str = str_replace('<\/a>', '', $str);
        $str = str_replace('<\br>', '', $str);
        //根据$tag切割成数组
        $list = explode('</tr>', $str);
        $data = array();

        foreach ($list as $k => $item) {
            if (strpos($item, 'box_tab_style02_td') === false) {
                continue;
            }
            $item = parent::cut($item, '<tr', '');
            foreach (array('id', 'name', 'developer', 'no', 'addr', 'sold_num', 'remain_num') as $type) {
                $tm = parent::cut($item, 'class="box_tab_style02_td', '</td>');
                if ($type == 'id') {
                    $item = parent::cut($item, '</td>', '');
                    continue;
                }
                if (parent::contains($tm, '<img src=') || parent::contains($tm, 'padding_left10px')) {
                    //要parse to num
                    $data[$k][$type] = self::parseNum($tm);
                } else {
                    if ($type == 'name' && parent::contains($tm, '<a href="')) {
                        $data[$k]['url'] = parent::cut($tm, '<a href="', '"');
                    }
                    $tt = str_replace('">', '', strip_tags($tm));
                    $data[$k][$type] = $tt;
                }
                $item = parent::cut($item, '</td>', '');
            }
        }
        return $data;
    }

    function convertToUtf8($content)
    {
        return iconv('GBK', 'UTF-8', $content);
    }

    function parseNum($str)
    {
        $result = '';

        $list = explode('<img src=', $str);

        foreach ($list as $k => $item) {
            if (!parent::contains($item, '.gif')) {
                continue;
            }
            $tmp = parent::rcut($item, '.gif', 'images/');

            switch ($tmp) {
                case '6a1d935323':
                    $result .= '1';
                    break;
                case 'eeb60828ae':
                    $result .= '2';
                    break;
                case 'bfea3b40f6':
                    $result .= '3';
                    break;
                case '676d25b24f':
                    $result .= '4';
                    break;
                case '16b9962acc':
                    $result .= '5';
                    break;
                case 'ccfcfc3238':
                    $result .= '6';
                    break;
                case 'd759e0da63':
                    $result .= '7';
                    break;
                case 'bea57ddbff':
                    $result .= '8';
                    break;
                case '853f634d1e':
                    $result .= '9';
                    break;
                case 'cea7249519':
                    $result .= '0';
                    break;
                default :
                    break;
            }
        }
        if (empty($result)) {
            $result = '0';
        }

        return intval($result, 10);
    }

    public static function getTempPath()
    {
        return parent::getTempPath() . __CLASS__ . '/';
    }

    public static function getDataPath()
    {
        return parent::getDataPath() . __CLASS__;
    }

    function getAllData()
    {
        $url_host = 'http://housing.gzcc.gov.cn';
        $url_tag = $url_host . '/fyxx/fdcxmxx/index';
        $url_index = $url_tag . '.shtml';

        $data = array();
        $test = true;
//        if (!file_exists(parent::getDataPath())){
//            parent::createDir(parent::getDataPath());
//        }

        if ($test && file_exists(self::getDataPath())) {
            echo unlink(self::getDataPath());
        }

        if (file_exists(self::getDataPath())) {
            return unserialize(file_get_contents(self::getDataPath()));
        } else {
            $loop = true;
            $i = 0;
            do {
                $u = $url_tag . '_' . $i . '.shtml';
                if ($i++ == 0) {
                    $u = $url_index;
                }
                echo $u . "\n";
                $content = self::getContent($u);
                if (!$this->contains($content, 'box_tab_style02_td')) {
                    $loop = false;
                } else {
                    $d = self::parseToList($content);
                    $data = array_merge($data, $d);
                }
                if ($test && $i > 50){
                    $loop = false;
                }
            } while ($loop);
            file_put_contents(self::getDataPath(), serialize($data));
            return $data;
        }
    }

    function test()
    {
//        $c = '87d43e65385260509a073ec269c2d17d.cache';
//        $content = file_get_contents('/tmp/HouseMarket/20170322pm/MarketYGJY/' . $c);
//        return self::parseToList($content);
        $content = $this->getContent('http://housing.gzcc.gov.cn/search/project/project_detail.jsp?changeproInfoTag=1&changeSellFormtag=1&pjID=100000013209&name=fdcxmxx');
        var_export($content);
    }
}