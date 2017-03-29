<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/22
 * Time: 15:50
 */
class MarketLianjia extends MarketData
{


    function convertToUtf8($content)
    {
        return $content;
    }

    function parseToList($content)
    {
        $content = $this->cut($content, '<ul id="house-lst" class="house-lst">', '<div class="footer">');
        $list = explode('<li data-index="0" data-id="">', $content);
        $data = array();
        foreach ($list as $k => $item) {
            if (!$this->contains($item, ' <h2>')) {
                continue;
            }
            $name = strip_tags($this->cut($item, '<h2>', '</h2>'));
            $url = $this->cut($item, '<h2>', '</h2>');
            $url = $this->cut($url, 'href="', '"');
            $addr = strip_tags($this->cut($item, '<div class="where">', '</div>'));
            $area = strip_tags($this->cut($item, '<div class="area">', '</div>'));
            $other = $this->cut($item, '<div class="other">', '</div>');
            $other = str_replace('<span>', '', $other);
            $other = str_replace('</span>', ',', $other);
            $type = $this->cut($item, '<div class="type">', '</div>');
            $type = strip_tags(str_replace('</span>', ',', $type));
            $average = strip_tags($this->cut($item, ' <div class="average">', '</div>'));
            $data[$k] = array(
                'url' => self::handle($url),
                'name' => self::handle($name),
                'addr' => self::handle($addr),
                'area' => self::handle($area),
                'other' => self::handle($other),
                'type' => self::handle($type),
                'average' => self::handle($average),
            );
        }
        return $data;
    }

    function handle($content)
    {
        $tmp = str_replace("\n", '', str_replace('	', '', str_replace(' ', '', $content)));
        return chop($tmp, ",");
    }

    function test()
    {
        $url = 'http://gz.fang.lianjia.com/loupan/';
        $content = $this->getContent($url);
        $content = $this->cut($content, '<div class="page-box house-lst-page-box"', '</div>');
        $regex = $this->cut($content, 'page-url="', '"');
        $total = $this->cut($content, '"totalPage":', ',"curPage');
        $curPage = $this->cut($content, '"curPage":', '}');
        var_export($regex . $total . $curPage);

//        $content = $this->cut($content, '<ul id="house-lst" class="house-lst">', '<div class="footer">');
//        var_export($this->parseToList($content));
    }

    static function getTempPath()
    {
        return parent::getTempPath() . __CLASS__.'/';
    }

    public static function getDataPath(){
        return parent::getDataPath().__CLASS__;
    }

    /**
     * @return array|mixed
     */
    function getAllData()
    {
        $url_host = 'http://gz.fang.lianjia.com';
        $page = '/loupan/';
        $data = array();
        $curIndex = 1;
        $pageRegex = '';
//        if (!file_exists(parent::getDataPath())) {
//            parent::createDir(parent::getDataPath());
//        }
        if (file_exists(self::getDataPath())) {
//            unlink(self::getDataPath());
            return unserialize(file_get_contents(self::getDataPath()));
        } else {
            do {
                $u = $url_host . $page;
                echo $u . "\n";
                $content = self::getContent($u);
                $d = self::parseToList($content);
                $data = array_merge($data, $d);

                if (empty($pageRegex)){
                    $pageRegex = $this->cut($content, 'page-url="', '"');
                }
                $totalIndex = intval($this->cut($content, '"totalPage":', ',"curPage'));
                $curPage = intval($this->cut($content, '"curPage":', '}'));
                $page = str_replace('{page}', ++$curPage ,$pageRegex);
            } while ($curIndex < $totalIndex);
            file_put_contents(self::getDataPath(), serialize($data));
            return $data;
        }
    }

    public function printf($datas)
    {
        $this->arrayToform($datas);
    }

    private function arrayToform($arr){
        //使用array()语句结构将联系人列表中所有数据声明为一个二维数组,默认下标是顺序数字索引
        //以HTML表格的形式输出二维数组中的每个元素
        echo '<table border="1" width="1440" align="center" >';
        echo '<caption><h1>链家新房列表</h1></caption>';
        echo '<tr bgcolor="#dddddd">';
        echo '<th>id</th><th>name</th><th>addr</th><th>area</th><th>other</th><th>type</th><th>average</th><th>url</th>';
        echo '</tr>';
        //使用双层for语句嵌套二维数组$contact1,以HTML表格的形式输出
        //使用外层循环遍历数组$contact1中的行
        $id = 0;
        foreach ($arr as $k => $item){
            echo '<tr>';
            $host = "http://gz.fang.lianjia.com";
            //使用内层循环遍历数组$contact1 中 子数组的每个元素,使用count()函数控制循环次数
            foreach ($item as $kk => $row)
            {
                if ($kk == 'url') {
                    $t = '<a href="'.$host.$row.'" target="_blank">跳转</a>';
                    echo '<td>' . ++$id . '</td>';
                }elseif ($kk == 'average'){
                    echo '<td>'.$row.'</td>';
                    echo '<td>'.$t.'</td>';
                }else{
                    echo '<td>'.$row.'</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table>';
    }
}