<?php
namespace Business\Controller;
use Think\Controller;
class UploadExcelController extends Controller
{
    public function _initialize() {
        vendor('Classes/PHPExcel/IOFactory');
        vendor('Classes/PHPExcel');
    }
    public function index(){
        $posts=I('post.');
        if(IS_POST){
            if($posts['store_id']!=null){
                session('moban',$posts);
                $this->success('数据已保存，可以下载模版');
            }else{
                session('moban',null);
                $this->error('数据未添加!',U(''));
            }
        }

        $uid = is_login();
        if(is_administrator($uid)){
            $slist=M('store')->select();
        }else{
            $slist=M('store')->where('uid='.$uid)->select();
        }


        $shangpin=M('shangpin')->select();
        $dangkou=M('dangkou')->select();
        $this->assign('slist',$slist);
        $this->assign('shangpin',$shangpin);
        $this->assign('dangkou',$dangkou);
        $this->assign('info',session('moban'));
        $this->display();
    }
    public function canpin($store_id){
        $clist=M('canpin')->where('store_id ='.$store_id)->select();
        $this->ajaxReturn($clist);
    }
    public function download()
    {
        $info = session('moban');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('餐品表');

        $title = array(
            '0' => '所属店铺',
            '1' => '餐品名称',
            '2' => '餐品名称拼音简写',
            '3' => '餐品分类',
            '4' => '盛用器具',
            '5' => '所属档口',
            '6' => '餐品大图',
            '7' => '原价',
            '8' => '现价',
            '9' => '餐品单位',
            '10' => '排序',
            '11' => '餐品描述',
            '12' => '销售时间',
            '13' => '停售时间',
        );
        $content = array(
            '0' => $info['store_id'],
            '1' => '',
            '2' => '',
            '3' => $info['canping_id'],
            '4' => $info['sp_id'],
            '5' => $info['dangkou_id'],
            '6' => $info['canping_logo_id'],
            '7' => '',
            '8' => '',
            '9' => '',
            '10' => '',
            '11' => '',
            '12' => '',
            '13' => '',
        );
        for ($i = 'A', $j = 0; $i <= 'N'; $i++, $j++) {
            //设置单元格宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth(18);
            $objPHPExcel->getActiveSheet()->getStyle($i . '1')->getFont()->setBold(true);
            //设置水平对齐方式（HORIZONTAL_RIGHT，HORIZONTAL_LEFT，HORIZONTAL_CENTER，HORIZONTAL_JUSTIFY）
            $objPHPExcel->getActiveSheet()->getStyle($i . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置垂直居中
            $objPHPExcel->getActiveSheet()->getStyle($i . '1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //添加表头
            $objPHPExcel->getActiveSheet()->getCell($i . '1')->setValue($title[$j]);
            $objPHPExcel->getActiveSheet()->getCell($i . '2')->setValue($content[$j]);
        }
        //设置字号
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $savename = '模版事例';
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $datetime = date('Y-m-d', time());
        if (preg_match("/MSIE/", $ua)) {
            $savename = urlencode($savename); //处理IE导出名称乱码
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $savename . '.xls"');  //日期为文件名后缀
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //excel5为xls格式，excel2007为xlsx格式
        $objWriter->save('php://output');
    }
    public function upload(){
        if(IS_POST){
            $excel=I('post.url');
            $excel=str_replace(__ROOT__.'/',' ',$excel);
            $objPHPExcel = \PHPExcel_IOFactory::load(trim($excel));
            $objPHPExcel->setActiveSheetIndex(0);
            $sheet0=$objPHPExcel->getSheet(0);

            $rowCount=$sheet0->getHighestRow();//excel行数

            $data=array();
            for ($i = 2; $i <= $rowCount; $i++){
                $item['store_id']=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue();
                $item['cm_name']=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue();
                $item['cm_name_py']=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getValue();
                $item['canping_id']=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getValue();
                $item['sp_id']=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getValue();
                $item['dangkou_id']=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getValue();
                $item['big_img']=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getValue();
                $item['original_price']=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getValue();
                $item['now_price']=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getValue();
                $item['danwei']=$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getValue();
                $item['sort_order']=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getValue();
                $item['cm_desc']=$objPHPExcel->getActiveSheet()->getCell('L'.$i)->getValue();
                $item['s_time']=$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getValue();
                $item['e_time']=$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getValue();
                $data[]=$item;
            }
            //$this->ajaxReturn($rowCount);
            $success=0;
            $error=0;
            foreach($data as $k=>$v){
                if(D('Canming')->data($v)->add()){
                    $success++;
                }else {
                    $error++;
                }
            }
            $this->ajaxReturn(array('smsg'=>$success,'emsg'=>$error)) ;
        }
    }
}