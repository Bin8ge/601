<?php
/**
 * Created by PhpStorm.
 * User: Lbb
 * Date: 2018/7/5 0005
 * Time: 21:52
 */

namespace Think;

class AjaxPage {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 默认列表每页显示行数
    public $listRows = 50;
    // 起始行数
    public $firstRow ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
   // protected $config  = array('header'=>'<li class="hidden-xs hidden-sm">共<strong>%TOTAL_ROW%</strong>条记录</li>','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>'%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 分页显示定制
    protected $config  = array(
        'header' => '<li style="line-height:2.8rem">共<strong>%TOTAL_ROW%</strong>条记录 第<strong>%NOW_PAGE%</strong>页/共<strong>%TOTAL_PAGE%</strong>页</li>',
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '第一页',
        'last'   => '最后一页',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%',
    );

    // 默认分页变量名
    protected $varPage;


    public function __construct($totalRows,$listRows='',$ajax_func,$parameter='') {
        $this->totalRows = $totalRows;
        $this->ajax_func = $ajax_func;
        $this->parameter = $parameter;
        $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

     public function nowpage($totalRows,$listRows='',$ajax_func,$parameter='') {
         $this->totalRows = $totalRows;
         $this->ajax_func = $ajax_func;
         $this->parameter = $parameter;
         $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
         if(!empty($listRows)) {
             $this->listRows = intval($listRows);
         }
         $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
         $this->coolPages  = ceil($this->totalPages/$this->rollPage);
         $this->nowPage  = !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
         if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
             $this->nowPage = $this->totalPages;
         }
         $this->firstRow = $this->listRows*($this->nowPage-1);

         return $this->nowPage;
     }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }


    public function show() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="<li><a class='ajaxify' id='big' href='JavaScript:".$this->ajax_func."(".$upRow.")'>".$this->config['prev']."</a></li>";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="<li><a class='ajaxify' id='big' href='javascript:".$this->ajax_func."(".$downRow.")'>".$this->config['next']."</a></li>";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<li><a class='ajaxify' id='big' href='javascript:".$this->ajax_func."(".$preRow.")'>上".$this->rollPage."页</a></li>";
            $theFirst = "<li><a class='ajaxify' id='big' href='javascript:".$this->ajax_func."(1)' >".$this->config['first']."</a></li>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<li><a class='ajaxify' id='big' href='javascript:".$this->ajax_func."(".$nextRow.")' >下".$this->rollPage."页</a></li>";
            $theEnd = "<li><a class='ajaxify' id='big' href='javascript:".$this->ajax_func."(".$theEndRow.")' >".$this->config['last']."</a></li>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= " <li><a class='ajaxify' id='big' href='javascript:".$this->ajax_func."(".$page.")'> ".$page." </a></li>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= " <li><a class='active'>".$page."</a></li>";
                }
            }
        }
        if ($this->totalPages>1)
        {
            $theEnd .= " <input style='width: 62px;height: 31px' type='number' min='1' max='$this->totalPages' id='jump' ><a href='javascript:void(0)' onclick=".$this->ajax_func."($('#jump').val()) class='active'>跳转</a>";
        }

        $pageStr = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'),
            array($this->config['header'], $this->nowPage, $upPage, $downPage, $theFirst, $linkPage, $theEnd, $this->totalRows, $this->totalPages),
            $this->config['theme']);
       /* $pageStr  =  str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd), $this->config['theme']);
      */  return $pageStr;
    }
}

