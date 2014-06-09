<?php
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架路由控制类
 * @version : $Id$
 */
final class baccarat_router
{
	public static $app_name;
	public static $controller_name;
	public static $function_name;
  public static $parameter;
  public static $namespace;
    public static function init($router_config)
    {
        //the same as apache rewrite.
        if(!empty($router_config['rule']))
        {
          foreach($router_config['rule'] as $key => $value)
          {
            if(preg_match("|$key|is", $_SERVER['REQUEST_URI'], $tmp))
            {         
              self::$app_name = 'app';
              self::$controller_name = $tmp[1];
              self::$function_name = $tmp[2];
              self::$namespace = $value['namespace'];
              /**
               * 参数匹配
               * @var [type]
               */
              foreach ($value['parameter'] as $k => $v) {
                self::$parameter[$k] = $tmp[$v];
              }
              return NULL;
            }
          }
        }

        //print_r(self::$parameter);
        //echo $_SERVER['REQUEST_URI'].'<br />';//exit;
        if(DIFF_PATH)
        {
          $request_url = str_replace(DIFF_PATH, '', $_SERVER['REQUEST_URI']);
        }

        $router_tmp = explode('/', $request_url);

        $default_app = current($router_config['app_name']);
        !empty($router_tmp[0]) or $router_tmp[0] = $default_app;
        !empty($router_tmp[1]) or $router_tmp[1] = DEFAULT_RUN;
        !empty($router_tmp[2]) or $router_tmp[2] = DEFAULT_RUN;
        
       	if(in_array($router_tmp[0], $router_config['app_name']))
       	{
       	    self::$app_name = $router_tmp[0];
       	    self::$controller_name = $router_tmp[1];
       	    self::$function_name = $router_tmp[2];
       	}
       	else 
       	{
       	    self::$app_name = $default_app;
       	    self::$controller_name = $router_tmp[0];
       	    self::$function_name = $router_tmp[1];
       	}
    }
    public function getUrl($name_space, $controller_name, $function_name, array $parameter = NULL)
    {


    }
    /*
    public function __get($name)
    {
    	if(isset(self::$name))
    	{
    		return self::$name;
    	}
    	return NULL;
    }
    */
}
?>