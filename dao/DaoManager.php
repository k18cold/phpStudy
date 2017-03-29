<?php

/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2017/3/29
 * Time: 11:22
 */
class DaoManager
{
    /**
     * @param $name
     */
    protected $mCon;
    protected $mDao;

    function __construct($Dao)
    {
        $this->mDao = $Dao;
        $this->mCon = mysqli_connect("localhost", "root", "a21703afbd7371df");
        if (!mysqli_query($this->mCon, "set names utf8")) {
            die('Error: ' . mysqli_error($this->mCon));
        }
        if (!$this->mCon) {
            die('Could not connect: ' . mysqli_error($this->mCon));
        } else {
            mysqli_select_db($this->mCon, 'housing_resource');
        }
    }
    public static function handle_sql($con, $sql)
    {
//    echo $sql;
        $result = mysqli_query($con, $sql);
        if (!$result) {
            die('Error: ' . mysqli_error($con));
        }
        return $result;
    }

    public function __destruct()
    {
        mysqli_close($this->mCon);
    }

    function getTableName()
    {
        return $this->mDao->getTableName();
    }

    function countItems($name)
    {
        return $this->mDao->countItemsByName($this->mCon, $name);
    }

    function updateItem($name, $k)
    {
        return $this->mDao->updateItemByName($this->mCon, $name, $k);
    }

    function insertItem($k)
    {
        return $this->mDao->insertItem($this->mCon, $k);
    }
}