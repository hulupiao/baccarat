<?php
/**
 * @author : hanjiafeng@gmail.com
 * @package : baccarat
 * @desc: baccarat框架分页类
 * @version : $Id$
 */
class baccarat_pager
{
    public $curPage = 1;
    //页数
    public $pageCount = 1;
    //每页条数
    public $pageNum = 10;
    //
    public $urlInfo = array();
    //总记录数
    public $count;
    //
    public $morePage = 5;
    //
    public $config = array('header' => '条记录', 'prev' => '上一页', 'prev_class' => 'page_prev', 'next' => '下一页', 'next_class' => 'page_next', 'first' => '第一页', 'first_class' => 'page_first', 'last' => '最后一页', 'last_class' => 'page_last', //'theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %prev% %next% %first% %last% %linkPage%'
    'theme' => ' %first% %prev% %linkPage% %next%  %last% ');

    /**
     * 分页类初始化
     * @param int $count
     * @param int $num
     */
    public function __construct($count, $num)
    {
        if(!$num)
        {
            return false;
        }
        $this->pageNum = $num;
        $this->pageCount = ceil($count / $this->pageNum);
        $this->count = $count;
        $this->getUrlInfo();
        $this->getCurPage();
    }

    public function getUrlInfo()
    {
        $requireUri = preg_replace('/[\/]+/', '/', $_SERVER['REQUEST_URI']);
        $urlInfo = parse_url($requireUri);
        $urlInfo['queryArr'] = array();
        if(!empty($urlInfo['query']))
        {
            parse_str($urlInfo['query'], $urlInfo['queryArr']);
            if(isset($urlInfo['queryArr']['p']))
            {
                unset($urlInfo['queryArr']['p']);
            }
        }
        $this->urlInfo = $urlInfo;
    }

    public function getPageUrl($page)
    {
        $queryArr = $this->urlInfo['queryArr'];
        $queryArr['p'] = $page;
        return $this->urlInfo['path'] . '?' . http_build_query($queryArr);
    }

    //
    public function getCurPage()
    {
        $this->curPage = isset($_GET['p']) ? intval($_GET['p']) : 1;
        if($this->curPage < 1)
        {
            $this->curPage = 1;
        }
        if($this->curPage > $this->pageCount)
        {
            $this->curPage = $this->pageCount;
        }
        return $this->curPage;
    }

    /**
     * 
     */
    public function getOffsetNum()
    {
        $offset = ($this->curPage - 1) * $this->pageNum;
        return $offset;
    }

    /**
     * 分页展现
     */
    public function getShowPage()
    {        
        $html = '';
        $startPage = $this->curPage - $this->morePage;
        if($startPage < 1)
        {
            $startPage = 1;
        }
        $endPage = $this->curPage + $this->morePage;
        if($endPage > $this->pageCount)
        {
            $endPage = $this->pageCount;
        }
        for($i = $startPage; $i <= $endPage; $i++)
        {
            if($i == $this->curPage)
            {
                $html .= '&nbsp;<a href="###" class="act">' . $i . '</a>&nbsp;';
            }
            else
            {
                $html .= '&nbsp;<a href="' . $this->getPageUrl($i) . '">' . $i . '</a>&nbsp;';
            }
        }
        ////上一页
        $prev_page = $this->curPage - 1;
        $prev = $prev_page > 0 ? '<a href="' . $this->getPageUrl($prev_page) . '" class="' . $this->config['prev_class'] . '">' . $this->config['prev'] . '</a>' : '';
        //下一页
        $next_page = $this->curPage + 1;
        $next = $next_page <= $this->pageCount ? '<a href="' . $this->getPageUrl($next_page) . '" class="' . $this->config['next_class'] . '">' . $this->config['next'] . '</a>' : '';
        //第一页
        $first = $this->pageCount > 10 ? '<a href="' . $this->getPageUrl(1) . '" class="' . $this->config['first_class'] . '">' . $this->config['first'] . '</a>' : '';
        //最后一页
        $last = $this->pageCount > 10 ? '<a href="' . $this->getPageUrl($this->pageCount) . '" class="' . $this->config['last_class'] . '">' . $this->config['last'] . '</a>' : '';
        
        $pageStr = str_replace(array('%totalRow%', '%header%', '%nowPage%', '%totalPage%', '%prev%', '%next%', '%first%', '%last%', '%linkPage%'), array($this->count, $this->config['header'], $this->curPage, $this->pageCount, $prev, $next, $first, $last, $html), $this->config['theme']);
        return $pageStr;
    }
}
?>