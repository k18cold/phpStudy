<?php
include_once "DaoManager.php";
/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/29
 * Time: 19:49
 */
class YgjyInfosDao implements DaoInterface
{

    function getTableName()
    {
        return "ygjy_infos";
    }

    function countItemsByName($con, $name)
    {
        $query_result = DaoManager::handle_sql($con, "SELECT * FROM ".$this->getTableName()." WHERE url = '$name'");
        return mysqli_num_rows($query_result);
    }

    function updateItemByName($con, $name, $item)
    {
        $query_result =  DaoManager::handle_sql($con, "SELECT * FROM ".$this->getTableName()." WHERE url = '$name'");
        $rd = mysqli_fetch_row($query_result);
        if ($rd[1] == $item['name'] && $rd[2] == $item['developer'] && $rd[3] == $item['no'] && $rd[4] == $item['addr'] && $rd[5] == $item['sold_num'] && $rd[6] == $item['remain_num'] && $rd[7] == $item['url']) {
            //不做任何处理
            return 0;
        } else {
            if ( DaoManager::handle_sql($con,$this->update_sql($item) )) {
                return 1;
            } else {
                return -1;
            }
        }
    }

    function insertItem($con, $item)
    {
        $sql =$this->insert_sql($item, "");
        if ( DaoManager::handle_sql($con, $sql)) {
            return 1;
        }else {
            return 0;
        }
    }

    private function update_sql($item)
    {
        $update_time = time();
        $result = "UPDATE ".$this->getTableName()." set 
        name='".$item['name']
            ."',developer='".$item['developer']
            ."',no='".$item['no']
            ."',addr='".$item['addr']
            ."',sold_num='".$item['sold_num']
            ."',remain_num='".$item['remain_num']
            ."',update_time='".$update_time
            ."' where url='$item[url]'";
        return $result;
    }

    private function insert_sql($arr, $time){
        $create_time = empty($time) ? time() : $time;
        $update_time = time();
        $result = "INSERT INTO ".$this->getTableName()."(name, developer, no, addr, sold_num, remain_num, url, create_time, update_time) VALUES('$arr[name]','$arr[developer]','$arr[no]','$arr[addr]','$arr[sold_num]','$arr[remain_num]','$arr[url]', '$create_time', '$update_time')";
        return $result;
    }
}