<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-19
 * Time: 14:39
 */
    //封装PDO类
    class MyPDO{

        //属性设置链接设置
        private  $host='localhost';
        private  $port='3306';
        private  $dbname='';
        private  $user='root';
        private  $password='root';
        private  $charset='utf8';

        //属性保存PDO和PDOStatement类的对象
        private $pdo;     //pdo类的对象
        private $stmt;    //PDOStatement类的对象
        private $type;

        //构造方法初始化条件
        public function __contruct($arr=array()){
            //判断条件，逐个属性判断
            $this->type=isset($arr['type'])?$arr['type']:'mysql';
            $this->host=isset($arr['host'])?$arr['host']:'localhost';
            $this->port=isset($arr['port'])?$arr['port']:'3306';
            $this->dbname=isset($arr['dbname'])?$arr['dbname']:'zhg';
            $this->user=isset($arr['user'])?$arr['user']:'root';
            $this->password=isset($arr['password'])?$arr['password']:'';
            $this->charset=isset($arr['charset'])?$arr['charset']:'utf8';

            //使用PDO连接数据库
            $this->connect();

            //设置字符集
            $this->charset();
        }

        //使用PDO连接数据库
        private function connect(){

            $this->pdo=new PDO("{$this->type}:host={$this->host};port={$this->port};dbname={$this->dbname}","{$this->user}","{$this->password}");

            //判断是否链接成功
            if(!$this->pdo){
                exit('PDO链接数据库失败！');
            }
        }

        //设置字符集
        private  function charset(){
            $res=$this->pdo->exec("set names {$this->charset}");

            //链接失败处理
            if(!$res){
                echo "字符集设置失败！";
                echo "失败编码：".$this->pdo->errorInfo()[1].'<br/>';
                echo "失败原因：".$this->pdo->errorInfo()[2].'<br/>';
                exit;
            }
        }

        private  function steMode(){
            //将返回的数据变成关联数组
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
        }

        //获取一行数据记录
        public function getRow($sql){
            //PDO的对象执行$sql
            $this->stmt=$this->pdo->query($sql);



            //调用模式
            $this->setMode();

            //成功，返回一条记录
            return $this->stmt->fetch();
        }


         //封装两种错误处理
        //1.pdo类出现的错误
        private function PDOError(){}


        //query方法
        private function query($sql){
            if($this->stmt=$this->pdo->query($sql)){

            }
        }

        //2.pdoStatement出现的错误
        private  function PDOSError(){

        }


    }



