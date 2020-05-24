<?php
//命名空间
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){        
        $db1 = D('Adv');
        $info1 = $db1 -> select();
        $this -> assign('advlist',$info1);
        
        $db2 = D('Goods');
        $info2 = $db2 -> where("view_index = '展示' and status = '上架'") -> select();
        $this -> assign('goodslist',$info2);
        if(!empty($_POST)){
            $very = new \Think\Verify();
            if ($very -> check($_POST['captcha'])){
                $db = new \Model\UserModel();
                $info = D('User') -> create();
                //去除帐号特殊字符，防止SQL注入
                $uname = htmlspecialchars($info['uname']);
                $info = $db -> checkNamePwd($uname,$info['pwd']);                
                if($info){
                    $id = $info['id'];
                    //再次验证ID和用户名是否是同一用户
                    $rname = $db -> where("id = ($id)") -> select();
                    if($rname[0]['uname'] == $info['uname'] and $rname[0]['status'] != 1){
                        session('uid',$info['id']);
                        session('uname',$info['uname']);
                        $db1 = D('Goods_type');
                        $info1 = $db1 ->where("typeName <> 'default'") -> select();
                        session('goodstypelist',$info1);
                        $this->redirect('Index/index');
                    }  else {
                        $this->error("您的账户可能已被禁用！请联系管理人员核实",U('Home/User/login'));
                    }
                }  else {
                    $this->error("用户名或密码错误",U('Home/User/login'));
                }
            }  else {
                $this->error("验证码错误！",U('Home/User/login'));
            }
        }
        $this->display();
    }
    
    public function goods(){
        $db1 = D('Goods');
        $info = $db1 -> select($_GET['id']);
        $this -> assign('goodslist',$info);
        $this->display();
    }

}