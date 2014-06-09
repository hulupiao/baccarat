<?php
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架初始化文件
 * @version : $Id$
 */
/**
 * 当前unix时间戳
 * @var unix time
 */
define("UNIX_TIME", time());
/**
 * 当前格式化时间
 */
define("TIME_STAMP", date("Y-m-d H:i:s"), UNIX_TIME);

defined("LIB_DIR") or define("LIB_DIR", __DIR__);

defined("REWRITE") or define("REWRITE", true);

defined("DEFAULT_RUN") or define("DEFAULT_RUN", 'index');

defined("SUB_PATH") or define("SUB_PATH", '');

defined("ROOT_PATH") or define("ROOT_PATH", substr(LIB_DIR, 0, -8));
// echo '<hr />';
// echo ROOT_PATH.'<br />';
// echo $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'<br />';

if(ROOT_PATH != $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR)
{
    define("DIFF_PATH", str_replace('\\', '/', substr(ROOT_PATH, strlen($_SERVER['DOCUMENT_ROOT']))));
}
else
{
    define("DIFF_PATH", "/");
}
//echo DIFF_PATH;exit;

defined("UPLOAD_PATH") or define("UPLOAD_PATH", ROOT_PATH.'/upload');
/**
 * 用户IP
 * @var ip address
 */
define("USER_IP", $_SERVER['REMOTE_ADDR']);
/**
 * 判断是否为调试模式
 */
//defined("DEBUG") or defined("DEBUG", false);
if(USER_IP == '127.0.0.1')
{
    $debug = true;
}
else
{
    $debug = false;
}
define('DEBUG', $debug);

if(DEBUG)
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
else
{
    ini_set('display_errors', 0);
}

/**
 * 自动加载规则
 * @param class name $class_name
 */
function __autoload($class_name)
{
    //var_dump(__namespace__);exit;
    //echo $class_name.'<br />';
	$tmp = explode('_', $class_name);
	if($tmp[0] == 'baccarat')
	{
		include_once LIB_DIR.DIRECTORY_SEPARATOR.$tmp[1].'.php';
	}	
	else
	{
	    //print_r($tmp);
        $dir = '';
        if(!empty(baccarat_router::$namespace))
        {
            $dir = baccarat_router::$namespace.DIRECTORY_SEPARATOR;
        }
        $include_file =  ROOT_PATH;
        foreach ($tmp as $value) 
        {
            $include_file .= DIRECTORY_SEPARATOR.$value;
        }
        $include_file .=  '.php';
        //echo $include_file;exit;
	    if(!file_exists($include_file))
	    {
            if(DEBUG)
            {
                //debug_print_backtrace();
                echo '<br />file not exists:'.$include_file;
                exit;
            }
            else
            {
                header('HTTP/1.1 404 Not Found');
                exit;
            }	        
	    }
	    include_once $include_file;
	}
}
/**
 * 框架初始化
 * @author hanjiafeng
 *
 */
class baccarat_init
{
    public static $mod;
    public static $act;
    private static function dispath()
    {
        global $router;
        self::$mod = !empty($_GET['mod']) && isset($router[$_GET['mod']]) ? $_GET['mod'] : 'index';
        self::$act = !empty($_GET['act']) && isset($router[self::$mod]['child'][$_GET['act']]) ? $_GET['act'] : 'index';
    }
    public static function run($_config)
    {
        //self::dispath();
        baccarat_db::$config = $_config['db'];
        baccarat_router::init($_config['router']);
        $class_name = baccarat_router::$app_name.'_controller_'.baccarat_router::$namespace.'_'.baccarat_router::$controller_name;
        $function_name = baccarat_router::$function_name.'_action';
        //echo baccarat_router::$namespace;echo '<hr />';exit;
        //echo $class_name;echo '<hr />';exit;
        $obj = new $class_name();
        $obj->parameter = baccarat_router::$parameter;
        $obj->$function_name();
    }
}
?>