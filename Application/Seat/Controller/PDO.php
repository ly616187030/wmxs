<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-13
 * Time: 15:51
 */
        //连接认证
        $pdo=new PDO('mysql:host=localhost;dbname=zhg','root','');

        //准备SQL并执行
        //新增数据
        $sql="insert into pro_student values (null,'小明','173')";

        //没有结果集  用exec执行结果集
        if($pdo->exec($sql)){
            echo $pdo->lastInsertId();
        }else{
            $error=$pdo->errorInfo();
            echo "错误编码为：".$error[1];
            echo "错误信息为：".$error[2];
            exit;
        }

        //修改数据
        $sql="update pro_student set name='南斯拉夫' where id=22";

        //没有结果集  用exec执行结果集
        if($res=$pdo->exec($sql)){
            var_dump($res);
        }else{
            $error=$pdo->errorInfo();
            echo "错误编码为：".$error[1];
            echo "错误信息为：".$error[2];
            exit;
        }

        //删除数据
        $sql="delete from  pro_student where id=22";

        //没有结果集  用exec执行结果集
        if($res=$pdo->exec($sql)){
            var_dump($res);
        }else{
            $error=$pdo->errorInfo();
            echo "错误编码为：".$error[1];
            echo "错误信息为：".$error[2];
            exit;
        }

        //查询数据
        $sql="select * from  pro_student where id=22";

        //有结果集,用query执行结果集,得到一个PDOStatement对象
        $stat=$pdo->query($sql);

        //解析获得的数据
        $row=$stat->fetch();

        //得到一个关联索引数组
//        var_dump($row);

        //获取结果集的行数
        $stat->rowCount();

        //获取结果集的列数
        $stat->columnCount();

        //通过fench的参数得到关联数组
        echo PDO::FETCH_ASSOC;
        var_dump($stat->fetch(PDO::FETCH_ASSOC));

        //通过fench的参数得到索引数组
        echo PDO::FETCH_NUM;
        var_dump($stat->fetch(PDO::FETCH_NUM));

        //获得全部结果集
        //自定义
        $rows=array();
        for($i=0;$i<$stat->rowCount();$i++){
            //得到关联数组
            $rows[]=$stat->fetch(PDO::FETCH_ASSOC);
        }

        //使用PDO提供的获取全部记录
        var_dump($stat->fetchAll(2));

        //获取某一列的字段的值
        echo $stat->fetchColumn(1);  //列值必须是索引值，索引值从0开始

        //将获得的记录变成一个对象
        var_dump($stat->fetchObject());

        //定义一个类
        class Admin{}
        var_dump($stat->fetchObject('Admin'));

