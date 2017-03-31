<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/22
 * Time: 12:01
 */
include_once('MarketData.php');
include_once "util/Utils.php";
include_once "util/Database.php";

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

    public static function parseNum($str)
    {
        $result = '';

        $list = explode('<img src=', $str);
//        return $list;
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
            $result .= Utils::cut($item, '>', '');
        }
        if (empty($result)) {
            $result = '0';
        }

        return Utils::trim($result);
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
        $firstData = self::getAllDataFirst();
        $result = array();
        foreach ($firstData as $k => $item) {
            $pj_id = self::getPjIdFromUrl($item['url']);
            $url = 'http://housing.gzcc.gov.cn/search/project/project.jsp?pjID=' . $pj_id;
            $arr = array('buildingID' => 13539
            , 'modeID' => 1
            , 'hfID' => 0
            , 'unitType' => 0
            , 'houseStatusID' => 0
            , 'totalAreaID' => 0
            , 'inAreaID' => 0
            );

            $content = $this->getContent($url, "");
            $data = self::parse2($content);
            $data['pj_id'] = $pj_id;
            $data['create_time'] = date('Ymd', time());
            $data['update_time'] = date('Ymd', time());
            $data['sold_num'] = $item['sold_num'];
            $data['remain_num'] = $item['remain_num'];
            $data['no'] = $item['no'];
            $result[$k] = $data;
        }
        return $result;
    }

    function insert2Db($table, $datas)
    {
        $db = new Database("localhost", "root", "a21703afbd7371df", "housing_resource");
        $db->select_table($table);
        var_export($db->data($datas)->add());
    }

    public static function parse2($content)
    {
        $data = array();
        $item = Utils::cut($content, '<table', ' </table>');
//        $item = Utils::cut($item, 'tab_style01_th">', '');
        foreach (array('name', 'no', 'addr', 'developer', 'district', 'floor_area', 'building_area', 'cer_num', 'type', 'total_num', 'total_area', 'sold_num', 'remain_num', 'sold_area', 'remain_area') as $type) {
            $tm = Utils::cut($item, 'tab_style01_td">', '</td>');
            if (Utils::contains($tm, '<img src=') || Utils::contains($tm, 'gif')) {
                //要parse to num
//            if ($type == 'floor_area' || $type == 'building_area' || $type == 'total_area' || $type == 'sold_area'){
//                $data[$type] = doubleval(MarketYGJY::parseNum($tm));
//            }elseif ($type == 'total_num' || $type == 'sold_num'){
//                $data[$type] = intval(MarketYGJY::parseNum($tm));
//            }else{
                $data[$type] = MarketYGJY::parseNum($tm);
//            }
            } else {
                $data[$type] = $tm;
            }
            $item = Utils::cut($item, 'tab_style01_td">', '');
        }
        return $data;
    }

    function getAllDataFirst()
    {
        $url_host = 'http://housing.gzcc.gov.cn';
        $url_tag = $url_host . '/fyxx/fdcxmxx/index';
        $url_index = $url_tag . '.shtml';
        $data = array();
        $test = true;

        if ($test && file_exists(self::getDataPath())) {
            echo unlink(self::getDataPath());
        }

        if (!test && file_exists(self::getDataPath())) {
            die;
            return unserialize(file_get_contents(self::getDataPath()));
        } else {
            $loop = true;
            $i = 0;
            do {
                $u = $url_tag . '_' . $i . '.shtml';
                if ($i++ == 0) {
                    $u = $url_index;
                }
//                echo $u . "\n";
                $content = self::getContent($u, "");
                if (!$this->contains($content, 'box_tab_style02_td')) {
                    $loop = false;
                } else {
                    $d = self::parseToList($content);
                    $data = array_merge($data, $d);
                }
//                if ($test && $i > 2){
//                    break;
//                }
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
//        $content = $this->getContent('http://housing.gzcc.gov.cn/search/project/project_detail.jsp?changeproInfoTag=1&changeSellFormtag=1&pjID=100000013209&name=fdcxmxx',"");
        $content = $this->getContent('http://housing.gzcc.gov.cn/search/project/project.jsp?pjID=-18326', "");
        var_export($data = self::parse2($content));
    }

    function printf($arr)
    {
        //使用array()语句结构将联系人列表中所有数据声明为一个二维数组,默认下标是顺序数字索引
        //以HTML表格的形式输出二维数组中的每个元素
        echo '<table border="1" width="1440" align="center" >';
        echo '<caption><h1>阳光家缘-项目表</h1></caption>';
        echo '<tr bgcolor="#dddddd">';
        echo '<th>id</th><th>项目</th><th width="100">区域</th><th width="100">未售</th><th width="100">已售</th>';
        echo '</tr>';
        //使用双层for语句嵌套二维数组$contact1,以HTML表格的形式输出
        //使用外层循环遍历数组$contact1中的行
        $keys = array('name', 'district', 'remain_num', 'sold_num');
        $id = 0;
        foreach ($arr as $k => $item) {
            echo '<tr>';
            $id += 1;
            if ($id == 184 || $id == 3552 || $id == 3055){
                continue;
            }
            //使用内层循环遍历数组$contact1 中 子数组的每个元素,使用count()函数控制循环次数
            foreach ($item as $kk => $row) {
                if (!in_array($kk, $keys)){
                    continue;
                }
                if ($kk == 'name') {
                    echo '<td>' . $id . '</td>';
                    echo '<td>' . $row . '</td>';
                } else {
                    echo '<td>' . $row . '</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table>';
    }

public static function getPjIdFromUrl($url)
{
    return Utils::getFeildFromUrl($url, "pjID");
}
}