<?php 
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架控制层基类
 * @version : $Id$
 */
class baccarat_controller
{    
    private static $baccarat_viewer;    
    public function __construct()
    {
     	   //header('Content-Type: text/html; charset=utf-8');
    }    
    public function request($key = '', $filter = FILTER_DEFAULT, $input = INPUT_POST, $default = '')
    {        
        $data = $input == INPUT_POST ? $_POST : $_GET;
        if(!isset($data[$key]))
        {
            return $default;
        }
        if($key && filter_has_var($input, $key))
        {
        	//处理数组貌似有bug，总是返回false
            //return filter_var($data[$key], $filter);
            if( filter_var($data[$key], $filter) ){
            	return filter_var($data[$key], $filter);
            }elseif ( !empty($data[$key]) ){
            	return $data[$key];
            }
        }        
        return $default;
    }
    public function get_viewer()
    {
        if(!isset(self::$baccarat_viewer))
        {
            self::$baccarat_viewer = new baccarat_viewer();
        }
        return self::$baccarat_viewer;    	
    }
    public function ajax_response($rs = array())
    {
        if(!isset($rs['error']))
        {
            $rs['error'] = '';
        }
    	echo json_encode($rs);
    	exit;
    }
    public function top_class()
    {
        $top_class = array(
                0 => 'one',
                1 => 'two',
                2 => 'thr',
                3 => 'for',
                4 => 'fiv',
                5 => 'six',
                6 => 'sev',
                7 => 'eig',
                8 => 'nin',
                9 => 'ten'
        );
        $this->get_viewer()->assign("top_class", $top_class);
    }
    function truncate_utf8_string($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
        {
        if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
        {
        if ($length < 1.0)
            {
            break;
            }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            }
            else
            {
            $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
            }
            $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
            if ($i < $strlen)
            {
            	$result .= $etc;
        	}
            return $result;
	}
    /*
    public function __call($function_name, $arguments)
    {
        
    	if(!isset($function_name))
    	{
    	    echo 'test';
    	}    	 
    }
    */
	
	/**
	 * 获取用户的IP
	 * @return string
	 */
	function getIp(){
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ips[0])
			{
				$ip = trim($ips[0]);
				return $ip;
			}
		}
		
		return $_SERVER['REMOTE_ADDR'];
	}
}
?>