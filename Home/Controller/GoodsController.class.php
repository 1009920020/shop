<?php

namespace Home\Controller;
use Think\Controller;

class GoodsController extends Controller{
    function goods_list(){
        $temp = (int)session('uid');
        if(!empty($temp)){
            $db2 = D('Goods');
            $db1 = D('Goods_type');
            $info1 = $db1 -> where("status = '0' and typeName <> 'default'") -> select();
            $this -> assign('goodstypelist',$info1);
            session('gooodstypes',$info1);
            
            if(empty($_GET['type'])){
                if(empty($_GET['p'])){
                    $p = 1;
                }  else {
                    $p = $_GET['p'];
                }
                $info2 = $db2 -> where("status = '上架'") -> order("goods_id") -> page($p.',15') -> select();      
                $this -> assign('list',$info2);

                $count = $db2 -> where("status = '上架'") -> order("goods_id") -> count();
                $page = new \Think\Page($count,15);
                $show = $page -> show();
                $this -> assign('page',$show);

            } else {
                if(empty($_GET['p'])){
                    $p = 1;
                }  else {
                    $p = $_GET['p'];
                }
                $type = $_GET['type'];
                $info3 = $db2 -> where("goods_type = '$type' and status = '上架'") -> order("goods_id") -> page($p.',15') -> select();
                $this -> assign('list',$info3);
                $count = $db2 -> where("goods_type = '$type' and status = '上架'") -> order("goods_id") -> count();
                $page = new \Think\Page($count,15);
                $show = $page -> show();
                $this -> assign('page',$show);
            }
            //顶部搜索
            if(!empty($_POST['kw'])){
                if(empty($_GET['p'])){
                    $p = 1;
                }  else {
                    $p = $_GET['p'];
                }
                $name = $_POST['kw'];
                $sql = "SELECT * FROM `goods` where goods_name like '%$name%' and status = '上架'";                
                $info4 = $db2 -> query($sql);
                $this -> assign('list',$info4);
                $sql2 = "SELECT count(*) FROM `goods` where goods_name like '%$name%' and status = '上架'";   
                $count = $db2 -> query($sql2);
                $page = new \Think\Page((int)$count,15);
                $show = $page -> show();
                $this -> assign('page',$show);
            }
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }

        $this->display();
    }

}

