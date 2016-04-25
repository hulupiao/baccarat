<?php 
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架模板类，基于smarty2.6
 * @version : $Id$
 */
namespace hulupiao\baccarat;


class viewer extends Smarty
{	
    public $smarty_obj;
    public function __construct()
    {
        $this->set_smarty();        
    }
    public function set_smarty()
    {
        $base_dir = CACHE_DIR . baccarat_router::$app_name;
        $compile_dir = $base_dir.'/templates_c/';
        $cache_dir = $base_dir.'/templates_cache/';
        if (!is_dir($base_dir))
        {
            mkdir($base_dir, 0777);
        }
        if (!is_dir($compile_dir))
        {
            mkdir($compile_dir, 0777);
        }
        if (!is_dir($cache_dir))
        {
            mkdir($cache_dir, 0777);
        }
        $this->smarty_obj = new Smarty();
        $this->smarty_obj->template_dir = ROOT_PATH;
        $this->smarty_obj->compile_dir = $compile_dir;
        $this->smarty_obj->cache_dir = $cache_dir;
        $this->smarty_obj->left_delimiter = '<!--{';
        $this->smarty_obj->right_delimiter = '}-->';
        $this->assign("controller_name", baccarat_router::$controller_name);
        $this->assign("function_name", baccarat_router::$function_name);
    }
    public function assign($key, $value)
    {
        $this->smarty_obj->assign($key, $value);
    }
    public function display($tpl)
    {
        header("Content-Type: text/html;charset=utf-8");
        $this->smarty_obj->display(baccarat_router::$app_name.'/view/'.$tpl);
    }
    /**
     * 提交post请求，curl方法
     *
     * @param string $url         请求url地址
     * @param array  $post_fields 变量数组
     * @return string             请求结果
     */
    function post_url($url, $post_fields, $timeout = 10) {
        $post_data = curl_build_query($post_fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    /**
     * 获取url返回值，curl方法
     */
    function get_url($url, $timeout = 20) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    
    /**
     * 验证方法
     * @param $params 所要验证的值
     * @param $type 验证的类型
     */
    function validate($params, $type = 'string') {
        $validate = array(
                'string' => '//',
                'require' => '/.+/',
                'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
                'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
                'currency' => '/^\d+(\.\d+)?$/',
                'number' => '/^\d+$/',
                'zip' => '/^[1-9]\d{5}$/',
                'integer' => '/^[-\+]?\d+$/',
                'double' => '/^[-\+]?\d+(\.\d+)?$/',
                'english' => '/^[A-Za-z]+$/',
                'qq' => '/^[1-9]\d{1,10}$/',
                'telephone' => '/^[0-9-()]+$/',
                'phonenum' => '/1[3-8][0-9]{9}+$/'
        );
        // 检查是否有内置的正则表达式
        if (isset($validate[strtolower($type)]))
        {
            $type = $validate[strtolower($type)];
        }
        return preg_match($type, $params) === 1;
    }
}
?>