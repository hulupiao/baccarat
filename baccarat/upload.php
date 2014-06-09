<?php
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架文件上传
 * @version : $Id$
 */

class baccarat_upload
{
    protected $file_type = array('.jpg', '.png');
    protected $max_size = 2048;
    protected $min_size = 0;
    private $error_info = array(
        UPLOAD_ERR_OK => '',
	    UPLOAD_ERR_INI_SIZE => '文件大小不符',
	    UPLOAD_ERR_FORM_SIZE => '文件大小不符',
	    UPLOAD_ERR_PARTIAL => '',
	    UPLOAD_ERR_NO_FILE => '无上传文件',
	    UPLOAD_ERR_NO_TMP_DIR => '系统没有临时目录',
	    UPLOAD_ERR_CANT_WRITE => '',
	    UPLOAD_ERR_EXTENSION => '文件类型不符',
    );
    private $ext = '';
    public $dir = 'upload';
    public function __construct()
    {
    	
    }
	public function check($file)
	{
		$this->file = $file;
		$this->ext = '.'.pathinfo($this->file['name'], PATHINFO_EXTENSION);
		$error_no = UPLOAD_ERR_OK;
		if(!in_array($this->ext, $this->file_type))
		{
		    $error_no = UPLOAD_ERR_EXTENSION;
		}
		if($this->file['error'])
		{
			$error_no = $this->file['error'];
		}
		return $this->error_info[$error_no];
	}
	public function save()
	{
	    if(!is_dir(UPLOAD_PATH.$this->dir))
	    {
	    	mkdir(UPLOAD_PATH.$this->dir);
	    }
	    $file_name = md5_file($this->file['tmp_name']);
	    
	    $save_dir = '';
		if(is_uploaded_file($this->file['tmp_name']))
		{
		    $save_dir = $this->dir.'/'.$file_name.$this->ext;
		    move_uploaded_file($this->file['tmp_name'], UPLOAD_PATH.$save_dir);
		}
		$rs['name'] = str_replace($this->ext, '', $this->file['name']);
		$rs['url'] = '/upload/'.$save_dir;
		return $rs;
	}
	//二级目录
	public function get_dir()
	{
		
	}
}
?>