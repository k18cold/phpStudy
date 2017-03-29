<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/27
 * Time: 10:21
 */

include("DaoManager.php");
include "DaoInterface.php";
class NewHouseDao implements DaoInterface
{
    public function countItemsByName($con,$name)
    {
        $query_result = DaoManager::handle_sql($con, "SELECT * FROM ".$this->getTableName()." WHERE url = '$name'");
        return mysqli_num_rows($query_result);
    }

    public function updateItemByName($con,$name, $item)
    {
        $query_result =  DaoManager::handle_sql($con, "SELECT * FROM ".$this->getTableName()." WHERE url = '$name'");
        $rd = mysqli_fetch_row($query_result);
        if ($rd[2] == $item['addr'] && $rd[3] == $item['area'] && $rd[4] == $item['other'] && $rd[5] == $item['type'] && $rd[6] == $item['average'] && $rd[7] == $item['url']) {
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

    public function insertItem($con,$item){
        $sql =$this->insert_sql($item);
        if ( DaoManager::handle_sql($con, $sql)) {
            return 1;
        }else {
            return 0;
        }
    }

    function insert_sql($arr)
    {
        $create_time = time();
        $update_time = time();
        $result = "INSERT INTO ".$this->getTableName()."(name, addr, area, tag, type, average, url, create_time, update_time) VALUES('$arr[name]','$arr[addr]','$arr[area]','$arr[other]','$arr[type]','$arr[average]','$arr[url]', '$create_time', '$update_time')";
//    echo $result;
        return $result;
    }

    function update_sql($arr)
    {
        $update_time = time();
        $result = "UPDATE ".$this->getTableName()." set update_time=$update_time where name='$arr[name]'";
        return $result;
    }

    function select_sql_name($name)
    {
        $result = "Select * from ".$this->getTableName()." Where name = '$name'";
    }

    function getTableName()
    {
       return  "lianjia_new";
    }
}