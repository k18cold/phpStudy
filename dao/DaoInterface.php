<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/29
 * Time: 14:23
 */
interface DaoInterface
{
    function getTableName();

    function countItemsByName($con, $name);

    function updateItemByName($con,$name, $k);

    function insertItem($con,$k);
}