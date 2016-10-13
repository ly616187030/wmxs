<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/5
 * Time: 16:47
 */

if(!function_exists('my_page')){
    function my_page($model,$p,$where,$pagenum){
        $User = M($model); // 实例化User对象

        $list=$User->where($where)->page($p.",{$pagenum}")->select();
        $count=$User->where($where)->count();

        $Page= new \Think\Page($count,$pagenum);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = true;//分页最后的总页数不显示
        $show=$Page->show();

        return array('list'=>$list,'page'=>$show);
    }
}