<?php
/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/21
 * Time: 15:06
 */
//include MarketYGJY::class;
include_once("MarketYGJY.php");
include_once("MarketLianjia.php");
include_once("dao/LianjiaNewDao.php");
include_once "dao/YgjyInfosDao.php";
include_once "util/Utils.php";
include_once "util/Database.php";
function ygjy_infos()
{
    $market = new MarketYGJY(MarketYGJY::getTempPath());
    $datas = $market->getAllData();
//    $datas = $market->getAllData();
//    insertIntoDb($datas, new DaoManager(new YgjyInfosDao()));
//    saveDatasToDbForYgjy($datas);
//    var_export($datas);
    $market->printf($datas);
}

/**
 * @param $datas
 */
function saveDatasToDbForYgjy($datas)
{
    $db = new Database("localhost", "root", "a21703afbd7371df", "housing_resource");
    $db->select_table("ygjy_projects");
    $sCount = 0;
    $fCount = 0;
    foreach ($datas as $k => $value) {
        if ($db->data($value)->add() == 1) {
            $sCount++;
        } else {
            $fCount++;
        }

    }
    echo "插入成功:" . $sCount . "\n";
    echo "插入失败:" . $fCount . "\n";
}

function lianjia_new()
{
    $market = new MarketLianjia(MarketLianjia::getTempPath());
    $datas = $market->getAllData();
    insertIntoDb($datas, new DaoManager(new LianjiaNewDao()));
    $market->printf($datas);
}

function insertIntoDb($datas, $dao)
{
    $update_count = 0;
    $insert_count = 0;
    $index = 0;
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
        if ($index++ > 100) {
            break;
        }
    }
    echo "插入:" . $insert_count . "<br>";
    echo "更新:" . $update_count . "<br>";
}

function main()
{
    $type = $_GET['type'];
    switch ($type) {
        case 'ygjy_infos':
            ygjy_infos();
            break;
        case 'lianjia_new':
            lianjia_new();
            break;
        default:
//            echo "请选择要做的事啊baby";
//            ygjy_infos();
//            test();
            ygjy_infos();
            break;
    }
}

main();

function test()
{
    $market = new MarketYGJY(MarketYGJY::getTempPath());
//    $url = 'http://housing.gzcc.gov.cn/search/project/sellFormDetail.jsp?unitID=100001511865';
//    $url = 'http://housing.gzcc.gov.cn/search/project/sellForm_form.jsp';
    $url = 'http://housing.gzcc.gov.cn/search/project/sellForm.jsp?pjID=100000013209&presell=20120153&chnlname=fdcxmxx';
    $url = 'http://housing.gzcc.gov.cn/search/project/project.jsp?pjID=100000013209';

    $arr = array('buildingID' => 13539
    , 'modeID' => 1
    , 'hfID' => 0
    , 'unitType' => 0
    , 'houseStatusID' => 0
    , 'totalAreaID' => 0
    , 'inAreaID' => 0
    );
    $content = file_get_contents("/tmp/HouseMarket/tmp/test");
    $data = MarketYGJY::parse2($content);
    $data['pj_id'] = MarketYGJY::getPjIdFromUrl($url);
    $data['create_time']=date('Ymd',time());
    $data['update_time']=date('Ymd',time());

    $db = new Database("localhost", "root", "a21703afbd7371df", "housing_resource");
    $db->select_table("ygjy_projects");
    var_export($db->data($data)->add());
    echo "\n";
    var_export($data);
}


