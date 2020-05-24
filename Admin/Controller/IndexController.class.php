<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends controller{
    //验证码
    function verifyImg(){
        $img = array(
            'fontSize'  =>  15,              // 验证码字体大小(px)
            'useCurve'  =>  true,            // 是否画混淆曲线
            'useNoise'  =>  true,            // 是否添加杂点	
            'imageH'    =>  35,               // 验证码图片高度
            'imageW'    =>  80,               // 验证码图片宽度
            'length'    =>  3,               // 验证码位数
            'fontttf'   =>  '4.ttf',         // 验证码字体，不设置随机获取
        );
        $very = new \Think\Verify($img);
        $very -> entry();
    }
    
    //后台首页
    function index(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        //在没有登录请求及session的管理员ID记录为空、
        //或按session的管理员ID记录搜索不到结果的情况下，注销session并返回登录界面
        if((!empty($_POST) and empty($aid)) or !empty($a_status)){
            if(!empty($_POST)){
                $very = new \Think\Verify();
                if ($very -> check($_POST['captcha'])){
                    $db = new \Model\AdminModel();
                    $info = $db -> create();
                    //去除帐号特殊字符，防止SQL注入
                    $uname = htmlspecialchars($info['uname']);
                    //验证登录信息
                    $info = $db -> checkNamePwd($uname,$info['pwd']);
                    if($info){
                        $id = $info['id'];
                        $rname = $db -> where("id = ($id)") -> field("uname") -> select();
                        if($rname[0]['uname'] == $info['uname']){
                            session('admin_id',$info['id']);
                            session('admin_name',$info['uname']);
                            $this->redirect('Admin/Index/index');
                        }
                    }  else {
                        $this->error("用户名或密码错误",U('Admin/Index/login'));
                    }
                }  else {
                    $this->error("验证码错误！",U('Admin/Index/login'));
                }
            }
            $this->display();
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
    }    
    
    function login(){
        $aid = session('admin_id');
        if(empty($aid)){
            $this->display();
        }  else {
            echo "<SCRIPT language=JavaScript>alert('已登录！');location.href='javascript:history.go(-1);';</SCRIPT>"; 
        }
    }
    
    function logout(){
        $aid = session('admin_id');
        if(!empty($aid)){
            session('admin_id',NULL);
            session('admin_name',NULL);
            $this->display();
            $this->success("管理员已退出！",U('Admin/Index/login'));
        }  else {
            $this->error("非法操作……","javascript:history.go(-1);");
        }
    }
}