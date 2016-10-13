<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-05-11
 * Time: 14:40
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;
class HelpController extends AdminController{

    public function index(){
        $m = M('Article_base');

        $where['cid'] = 7;

        $count = $m->where($where)->count();
        $Page = new \Think\Page($count,10);
        //设置分页显示方式

        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示

        $show = $Page->show();

        $list = $m->alias('a')
            ->join('LEFT JOIN __ARTICLE_ARTICLE__ AS b ON a.id = b.id')
            ->field('a.*,b.title')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->where($where)->order('sort desc')->select();

        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }

    public function show(){
        $m = M('Article_base');

        $id = I('id');

        $where['a.id'] = $id;

        $all = $m->where('cid = 7')->order('sort desc')->getField('id',true);

        $k = array_search($id,$all);

        $list = $m->alias('a')
            ->join('LEFT JOIN __ARTICLE_ARTICLE__ AS b ON a.id = b.id')
            ->field('a.*,b.title,b.content')
            ->where($where)->find();

        $prev_where['a.id'] = $all[$k-1];
        $prev = $m->alias('a')
            ->join('LEFT JOIN __ARTICLE_ARTICLE__ AS b ON a.id = b.id')
            ->field('a.*,b.title')
            ->where($prev_where)->find();

        $next_where['a.id'] = $all[$k+1];
        $next = $m->alias('a')
            ->join('LEFT JOIN __ARTICLE_ARTICLE__ AS b ON a.id = b.id')
            ->field('a.*,b.title')
            ->where($next_where)->find();

        $this->assign('list',$list);
        $this->assign('prev',$prev);
        $this->assign('next',$next);
        $this->display();
    }
}