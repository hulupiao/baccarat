<?php
/**
 * Created by PhpStorm.
 * User: hanjiafeng
 * Date: 16/4/25
 * Time: 上午11:48
 */

namespace index\model;


class test extends base
{
    public function get() {
        $sql = "select * from user limit 1";
        return $this->fetch($sql);
    }
}