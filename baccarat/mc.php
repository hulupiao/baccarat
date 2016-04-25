<?php 
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架内存缓存操作类
 * @version : $Id$
 */
namespace hulupiao\baccarat;

final class mc
{
	
    public function __construct()
    {
        Memcached::addServer('127.0.0.1');
    }
    public function getInstance()
    {
    	
    }
    
}
?>