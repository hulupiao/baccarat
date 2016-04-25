<?php
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架数据层基类
 * @version : $Id$
 $__db__ = array(
        0 => array(
                0 => array(
                        'user' => 'root',
                        'password' => '',
                        'host' => 'localhost',
                        'port' => '3306',
                        'database' => 'zhujianbu'
                ),
                1 => array(
                        'user' => 'root',
                        'password' => '',
                        'host' => 'localhost',
                        'port' => '3306',
                        'database' => 'zhujianbu'
                )
        )
);
 */
namespace hulupiao\baccarat;

use PDO;

class db
{
    /**
     * 是否开启DEBUG
     * @var $debug boolean
     */
    private $debug = true;
    /**
     * 是否开启memcache cache
     * @var unknown_type
     */
    private $cache = false;
    /**
     * PDO操作对象
     * @var PDO
     */
    public $db;
    /**
     * 当前操作数据库名称
     * @var unknown_type
     */
    public $database;
    /**
     * 数据库配置
     * @var unknown_type
     */
    public static $config;
    /**
     * 当前执行的$sql
     *
     */
    private $sql_sign = '';
    /**
     * PDO 查询名柄对象
     * @var PDOStatement
     */
    protected $smf;
    /**
     * 数据库连接集合
     * @var array
     */
    public static $db_link;
    /**
     * memcache 对象
     * @var unknown_type
     */
    public $mc;

    public function __construct($database = '')
    {
        //global $__db__;
        if(empty(self::$config))
        {
            echo 'no config exit';
            exit;
        }
        if(!empty($database) && isset(self::$config[$database]))
        {
            $this->database = $database;
        }
        else
        {
            $this->database = key(self::$config);
        }
        //$this->mc = baccarat_mc::getInstance();
        $this->init();
    }

    public function init()
    {
    
    }

    private function getDb($i = 0)
    {
        //
        if(isset(self::$db_link[$this->database][$i]))
        {
            $status = self::$db_link[$this->database][$i]->getAttribute(PDO::ATTR_SERVER_INFO);
            if($status == 'MySQL server has gone away')
            {
                self::$db_link[$this->database][$i] = null;
            }
        }
        //
        if(!isset(self::$db_link[$this->database][$i]))
        {
            extract(self::$config[$this->database][$i]);
            $dsn = "mysql:host=$host;port=$port;dbname=$database";
            try
            {
                self::$db_link[$this->database][$i] = new PDO($dsn, $user, $password);
            }
            catch(PDOException $e)
            {
                $err_str = 'Connection failed: ' . $e->getMessage();
                if($this->debug)
                {
                    echo $err_str;
                }
                if($this->exception)
                {
                    throw new Exception($err_str);
                }
                return false;
            }
        }
        $this->db = self::$db_link[$this->database][$i];
        $this->setDb();
    }

    public function setDb($charset = 'UTF8')
    {
        $this->db->exec("SET NAMES  UTF8");
    }

    /**
     *
     * 数据库查询
     * @param sql $sql
     * @param 主从库 $db_master 0,1
     */
    public function query($sql, $params = array(), $db_master = false)
    {
        //强制作用主库
        if($db_master)
        {
            $db_router = 0;
        }
        //主从库判断
        else
        {
            $db_router = ('select' == strtolower(substr(trim($sql), 0, 6)) || 'desc' == strtolower(substr(trim($sql), 0, 4))) ? 1 : 0;
        }
        if($db_router)
        {
            $this->sql_sign = count($params) ? md5($sql) : md5($sql.implode('', $params));
        }
        $this->getDb($db_router);
        $this->smf = $this->db->prepare($sql);
        $rs = $this->smf->execute($params);
        if(!$rs)
        {
            $this->errorInfo($this->smf->queryString, $params);
            return false;
        }
        return true;
    }

    private function errorInfo($sql, $params = array())
    {
        $error_info = $this->smf->errorInfo();
        if($this->debug)
        {
            print_r($this->smf);
            echo '<hr />';
            if(!empty($params))
            {
            	foreach ($params as $key => $value)
            	{
            		$sql = str_replace($key, "'".$value."'", $sql);
            	}
            }
            echo "sql:" . $sql . "<hr />";
            print_r($params);
            echo '<hr />';
            print_r($error_info);
            exit();
        }
    }

    /**
     * 数据库插入操作
     *
     * @param array $info
     * @return int
     */
    public function insert($info, $table, $level = 1)
    {        
        $tmp = '`:' . implode('`,`:', array_keys($info)) . '`';
        $keys = str_replace(':', '', $tmp);
        $values = str_replace('`', '', $tmp);
        $sql = "insert into `$table` ($keys) values ($values)";
        $this->query($sql, $this->change_array_key($info));
        return $this->db->lastInsertId('id');
    }

    public function update($id, $info, $table)
    {
        foreach($info as $key => $value)
        {
            $tmp[] = '`'.$key.'`=:'.$key;
        }
        $sql = "update `$table` set " . implode(',', $tmp) . " where id = :id";
        $info['id'] = $id;
        return $this->query($sql, $this->change_array_key($info));
    }
	private function change_array_key($info)
	{
	    $params = array();
	    foreach($info as $key => $value)
	    {
	        $params[':'.$key] = $value;
	    }
		return $params;
	}
    /**
     * 取符合条件的一条记录
     * 未转义参数,有可能造成漏洞
     * @param sql $sql
     * @return array
     */
    public function fetch($sql, $params = array(), $styles = PDO::FETCH_ASSOC)
    {
        if($this->cache && !empty($this->sql_sign))
        {
            $result = $this->mc->get($this->sql_sign);
            if($result === false)
            {
                $this->query($sql, $params);
                $result = $this->smf->fetch($styles);
                $this->mc->set($this->sql_sign, $result);
            }
        }
        else
        {
            $this->query($sql, $params);
            $result = $this->smf->fetch($styles);
        }
        return $result;
    }

    public function fetchCol($sql, $params = array(), $n = 0)
    {
        $this->query($sql, $params);
        return $this->smf->fetchAll(PDO::FETCH_COLUMN, $n);
    }

    /**
     * 取出所有满足条件的数据
     * @param string $sql
     * @return array
     */
    public function fetchAll($sql, $params = array(), $styles = PDO::FETCH_ASSOC)
    {
        if($this->cache && !empty($this->sql_sign))
        {
            $result = $this->mc->get($this->sql_sign);
            if($result === false)
            {
                $this->query($sql, $params);
                $result = $this->smf->fetchAll($styles);
                $this->mc->set($this->sql_sign, $result);
            }
        }
        else
        {
            $this->query($sql, $params);
            $result = $this->smf->fetchAll($styles);
        }
        return $result;
    }

    /**
     * 扩展取单一字段
     * @param string $sql
     * @return str
     */
    public function fetchOne($sql, $params = array())
    {
        if($this->cache && !empty($this->sql_sign))
        {
            $result = $this->mc->get($this->sql_sign);
            if($result === false)
            {
                $this->query($sql, $params);
                $result = $this->smf->fetch();
                $result = $result[0];
                $this->mc->set($this->sql_sign, $result);
            }
        }
        else
        {
            $this->query($sql, $params);
            $result = $this->smf->fetch();
            $result = $result[0];
        }
        return $result;
    }

    /**
     * 事物封装处理
     * 没有经过测试
     * @param array $sql_arr
     */
    public function run($sql_arr)
    {
        $this->getDb(0);
        $this->db->beginTransaction();
        foreach($sql_arr as $sql)
        {
            echo $sql . '<br />';
            $rs = $this->db->exec($sql);
            echo $rs . ':' . $sql . '<br />';
        }
        if(!$this->db->commit())
        {
            $this->db->rollBack();
            return false;
        }
        return true;
    }
}
?>