<?php
/**
 * GD库 图片水印程序
 * @author hulupiao@sina.cn
 */
class baccarat_watermark
{
	public $type = 0;//0图片水印,1文字水印

	public $bgImg = 'water.png';//水印背景图片

	public $text = '';//水印背景文字

	public $font = '';//水印文字字体

	public $fontSize = 12;//水印文字字体大小

	public $fontColor = '#cccccc';//水印文字颜色

	public $image = '';//水印图片
	
	//0随机,1左上,2左下,3右上,4右下,5居中
	public $position = 0;//水印位置

	public $angle = 0;//水印角度

	public $newFile = '';
	
	public $debug = true;

	public function __construct($conf = array())
	{	
		if(!extension_loaded('gd'))
		{
			$this->errorReporting('请先安装GD库', __METHOD__); 
		}
		if(is_array($conf))
		{
			foreach($conf as $key => $value)
			{
				if(isset($this->$key))
				{
					$this->$key = $value;
				}
			}
		}
	}
	public function render()
	{		
		if(!file_exists($this->image))
		{
			$this->errorReporting('图片不存在', __METHOD__); 
		}

		$this->getImageInfo();

		if($this->type == 0)
		{			
			$this->imgRender();
		}
		else
		{			
			$this->fontRender();
		}
		switch($this->imageInfo[2]) 
		{     
			case 1:
				imagegif($this->imageIm, $this->newFile);
				break;
			case 2;
				imagejpeg($this->imageIm, $this->newFile);
				break;    
			case 3:
				imagepng($this->imageIm, $this->newFile);
				break;
			default:
				$this->errorReporting('不支持的图片格式', __METHOD__);  
		} 
		imagedestroy($this->imageIm);

	}
	//取图片信息
	public function getImageInfo()
	{
		$this->imageInfo = getimagesize($this->image);
		//取得水印图片的格式
		switch($this->imageInfo[2])
		{
            case 1:
				$this->imageIm = imagecreatefromgif($this->image);
				break;
            case 2:
				$this->imageIm = imagecreatefromjpeg($this->image);
				break;
            case 3:
				$this->imageIm = imagecreatefrompng($this->image);
				break;
            default:
				$this->errorReporting('不支持的图片格式', __METHOD__);
        }
	}
	public function getCoordinate($w, $h)
	{
		$imgW = $this->imageInfo[0];
		$imgH = $this->imageInfo[1];
		//
		$coordinate = array(
			'x' => rand(0,($imgW - $w)),
			'y' => rand(0,($imgH - $h)),
		);
		switch($this->position) 
		{
			case 1://左上
				$coordinate['x'] = 0;
				$coordinate['y'] = 0;
				break;
			case 2://左下
				$coordinate['x'] = 0;
				$coordinate['y'] = $imgH - $h;
				break;
			case 3://右上
				$coordinate['x'] = $imgW - $w;
				$coordinate['y'] = 0;
				break;
			case 4://右下
				$coordinate['x'] = $imgW - $w;
				$coordinate['y'] = $imgH - $h;
				break;
			case 5://居中
				$coordinate['x'] = ($imgW - $w) / 2;
				$coordinate['y'] = ($imgH - $h) / 2;
				break;  
		}
		return $coordinate;
	}
	//图片水印
	public function imgRender()
	{
		if(!file_exists($this->bgImg))
		{
			$this->errorReporting('不存在水印图片', __METHOD__);
		}
		//水印坐标位置
		$bgInfo = getimagesize($this->bgImg);
		$w = $bgInfo[0];
		$h = $bgInfo[1];
		$coordinate = $this->getCoordinate($w, $h);

		//取得背景图片的格式
		switch($bgInfo[2]) 
		{
            case 1:
				$bgIm = imagecreatefromgif($this->bgImg);
				break;
            case 2:
				$bgIm = imagecreatefromjpeg($this->bgImg);
				break;
            case 3:
				$bgIm = imagecreatefrompng($this->bgImg);
				break;
            default:
				$this->errorReporting('不支持的水印图片格式', __METHOD__);
        }
		//设定图像的混色模式
		imagealphablending($this->imageIm, true);
		//生成水印图片

		$rs = imagecopy($this->imageIm, $bgIm, $coordinate['x'], $coordinate['y'], 0, 0, $w, $h);
		//var_dump($rs);
		imagedestroy($bgIm);
	}
	//文字水印
	public function fontRender()
	{
		if(empty($this->text))
		{
			$this->errorReporting('未定义水印文字', __METHOD__);
		}
		if(!file_exists($this->font))
		{
			$this->errorReporting('未指定水印文字字体', __METHOD__);
		}
		//原图信息
		$this->getImageInfo();
		//颜色
		$R = hexdec(substr($this->fontColor,1,2));
		$G = hexdec(substr($this->fontColor,3,2));
		$B = hexdec(substr($this->fontColor,5));
		$color = imagecolorallocate($this->imageIm, $R, $G, $B);
		//水印坐标位置
		$box = imagettfbbox($this->fontSize, $this->angle, $this->font, $this->text);
		$w = $box[2] - $box[6];
		$h = $box[3] - $box[7];
		$coordinate = $this->getCoordinate($w, $h);
		//
		imagettftext ($this->imageIm, $this->fontSize, $this->angle, $coordinate['x'], $coordinate['y'], $color, $this->font, $this->text);	
	}
	public function errorReporting($msg, $method)
	{
		if($this->debug)
		{
			trigger_error($method.'--'.$msg, E_USER_ERROR);
			exit;
		}
		else
		{
			$this->error = $msg;
		}		
	}
}
/*
//Example
ini_set('display_errors', 1);
error_reporting(E_ALL);

$imageWater_obj = new imageWater();
$imageWater_obj->image = 'sfz2.jpg';
$imageWater_obj->type = 0;
$imageWater_obj->position = 5;
$imageWater_obj->text = '仅限于购买房屋使用';//仅限于域名实名使用
$imageWater_obj->font = 'c:\windows\fonts\simhei.ttf';
$imageWater_obj->fontSize = 50;
$imageWater_obj->newFile = 'test.jpg';
$imageWater_obj->render();
*/