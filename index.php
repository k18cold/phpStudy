<?php
/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/21
 * Time: 15:06
 */
//include MarketYGJY::class;
include("MarketYGJY.php");
include("MarketLianjia.php");
include("dao/NewHouseDao.php");



function ygjy_infos()
{
    $market = new MarketYGJY(MarketYGJY::getTempPath());
    $dates = $market->getAllData();
    var_export($dates);
}

//main();
function lianjia_new()
{
    $market = new MarketLianjia(MarketLianjia::getTempPath());
    $datas = $market->getAllData();
    inser2db_lianjia_new($datas);
    $market->printf($datas);
}

function inser2db_lianjia_new($datas)
{
    $dao = new DaoManager(new NewHouseDao());
    $update_count = 0;
    $insert_count = 0;
    foreach ($datas as $k => $item) {
        //$sql = insert_sql($datas[$k]);
        //查是否有name=name的这条信息,如果有,看看数据是否有更新,有更新就update,没有就不做
        //如果没有这条信息,则Insert
        $name = $datas[$k]['name'];
        $url = $datas[$k]['url'];
        $result = $dao->countItems($url);
        if ($result > 1) {
            die('有问题, 小区=' . $name . ',有' . $result . '行数据');
        } else if ($result == 1) {
            //update
            //不update了
            //判断是否要update
          $r = $dao->updateItem($url, $datas[$k]);
          if ($r === 1) {
              $update_count++;
          }
        } else {
            $insert_count += $dao->insertItem($datas[$k]);
        }
    }
    echo "插入:" . $insert_count . "<br>";
    echo "更新:" . $update_count . "<br>";
}


function main(){
    $type = $_GET['type'];
    switch ($type){
        case 'ygjy_infos':
            ygjy_infos();
            break;
        case 'lianjia_new':
            lianjia_new();
            break;
        default:
            echo "请选择要做的事啊baby";
            break;
    }
}
main();
