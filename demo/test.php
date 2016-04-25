<?php
/**
 * Created by PhpStorm.
 * User: hanjiafeng
 * Date: 16/4/25
 * Time: 上午10:24
 */

spl_autoload_register(function($class_name){
    var_dump($class_name);
});



class a{
    static function fun1(){
        $class_name = 'test';
        new $class_name;
    }
}

a::fun1();