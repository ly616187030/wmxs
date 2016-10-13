<?php
/**
 * Created by PhpStorm.
 * Auther: <zhangpf41@163.com>
 * Date: 2015/6/17
 * Time: 16:28
 */

namespace Platform\Controller;


use Admin\Controller\AdminController;
use Platform\Controller\MenuPctController;


class StorageController extends AdminController
{
    /**
     * 首页显示、数据查询
     * @author changkai <job_ck@126.com>
     */
    public function index()
    {

        if ($_GET['nickname']) {
            $storage = M('storage');
            //实例化storage对象
            $data = I('get.');
            $map['storage_id'] = array('like', "%" . $data['nickname'] . "%");
            $map['storage_name'] = array('like', "%" . $data['nickname'] . "%");
            $map['_logic'] = 'OR';
            $count = $storage->where($map)->count();
            // 查询满足要求的总记录数
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $list = $storage->alias('s')->join('LEFT JOIN wm_address_province as p ON s.province_code=p.code')->join('LEFT JOIN wm_address_city as c ON s.city_code=c.code')->join('LEFT JOIN wm_address_town as q ON s.county_code=q.code')->field('s.storage_id,s.storage_name,s.storage_desc,s.address,p.name sheng,c.name shi,q.name qu')->order('s.storage_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->where($map)->select();
            $show = $Page->show();
            // 分页显示输出
            $this->assign('page', $show);
            // 赋值分页输出
            $this->assign('list', $list);
            $this->display();
        } else {
            $storage = M('storage');
            //实例化storage对象
            $count = $storage->count();
            // 查询满足要求的总记录数
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $list = $storage->alias('s')->join('LEFT JOIN wm_address_province as p ON s.province_code=p.code')->join('LEFT JOIN wm_address_city as c ON s.city_code=c.code')->join('LEFT JOIN wm_address_town as q ON s.county_code=q.code')->field('s.storage_id,s.storage_name,s.storage_desc,s.address,p.name sheng,c.name shi,q.name qu')->order('s.storage_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $show = $Page->show();
            // 分页显示输出
            $this->assign('page', $show);
            // 赋值分页输出
            $this->assign('list', $list);
            //赋值数据集
            $this->meta_title ='仓库管理';
            $this->display();
        }
    }

    /**
     * 新增后台菜单
     * @author changkai <job_ck@126.com>
     */
    public function addStorage()
    {
        if (IS_POST) {
            //创建新增仓库的动态验证
            $rules = array(array('storage_name', 'require', '仓库名称不能为空！'), array('storage_name', '', '仓库名称已经存在！', 0, 'unique', 1), array('province_code', 'require', '省市不能为空！'), array('address', 'require', '仓库详细地址不能为空！'), array('storage_desc', 'require', '仓库简介不能为空！'),);
            $storages = M('storage');

            if ($storages->validate($rules)->create()) {
                $re = $storages->add();
                if ($re) {
                    $this->success('添加仓库成功!', U('index'));
                } else {
                    $this->error('添加仓库失败!');
                }

            } else {
                $this->error($storages->getError());
            }
        } else {
            //城市三级联动
            $province = A('MenuPct')->getProvince(false);
            $this->assign('_province', $province);
            $this->meta_title = '新增仓库';
            $this->display();
        }
    }

    /**
     * 编辑后台菜单
     * @author changkai <job_ck@126.com>
     */
    public function editStorage()
    {
        if (IS_POST) {
            //创建修改仓库信息的动态验证
            $rules = array(array('storage_name', 'require', '仓库名称不能为空！'), array('province_code', 'require', '省市不能为空！'), array('address', 'require', '仓库详情地址不能为空！'), array('storage_desc', 'require', '仓库简介不能为空！'),);
            $storage = M("storage");
            if ($storage->validate($rules)->create()) {
                $id = I('post.storage_id');
                $re = $storage->where('storage_id=' . $id)->save();
                if ($re) {
                    $this->success('修改仓库信息成功!', U('index'));
                } else {
                    $this->error('没有修改仓库信息!');
                }

            } else {
                $this->error($storage->getError());
            }
        } else {
            //城市三级联动
            $province = A('MenuPct')->getProvince(false);
            $this->assign('_province', $province);
            //获取仓库信息
            $id = I('get.storage_id');
            $list = M("storage")->field(true)->find($id);
            if (false === $list) {
                $this->error('读取仓库信息失败!');
            }
            $this->assign('_list', $list);
            $this->meta_title = '修改仓库信息';
            $this->display();
        }
    }

    /**
     * 删除后台菜单
     * @author changkai <job_ck@126.com>
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('storage_id' => array('in', $id));
        if (M('storage')->where($map)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

}
