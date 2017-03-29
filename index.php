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
include("dao/NewHouseDaoManager.php");
function main()
{
    $market = new MarketYGJY(MarketYGJY::getTempPath());
    $dates = $market->getAllData();
    var_export($dates);
//    inser2db_ygjy($dates);
}



//main();
function test()
{
    $market = new MarketLianjia(MarketLianjia::getTempPath());
    $datas = $market->getAllData();
    echo '=======共获取数据:' . count($datas) . "条==========\n";
    var_export($datas);
//    inser2db($datas);
}

function inser2db($datas)
{
    $dao = new DaoManager(new NewHouseDaoManager());
    $update_count = 0;
    $insert_count = 0;
    foreach ($datas as $k => $item) {
        //$sql = insert_sql($datas[$k]);
        //查是否有name=name的这条信息,如果有,看看数据是否有更新,有更新就update,没有就不做
        //如果没有这条信息,则Insert
        $name = $datas[$k]['name'];
        $result = $dao->countItems($datas[$k]);
        if ($result > 1) {
            die('有问题, 小区=' . $name . ',有' . $result . '行数据');
        } else if ($result == 1) {
            //update
            //不update了
            //判断是否要update
          $r = $dao->updateItem($name, $datas[$k]);
          if ($r === 1) {
              var_export($datas[$k]);
              $update_count++;
          }
        } else {
            $insert_count += $dao->insertItem($datas[$k]);
            var_export($datas[$k]);
        }
    }
    echo "=========共更新数据:" . $update_count . "条=======\n";
    echo "=========共插入数据:" . $insert_count . "条=======\n";
}

test();