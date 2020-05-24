<?php
namespace Admin\Controller;
use Think\Controller;

class SetController extends controller{
    function setAdmin_edit(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $db = new \Model\UserModel();
            $userinfo = D("Admin") -> select($aid);
            if(!empty($userinfo)){
                $usersmodel = $db -> create();
                if($usersmodel){
                    //密码盐
                    $usersmodel['salt'] = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_RANDOM));
                    if($usersmodel['pwd'] == $_POST['repwd']){
                        //将密码进行密码盐加密
                        $usersmodel['pwd'] = sha1($usersmodel['pwd'].$usersmodel['salt']);

                        session('admin_editpwd',$usersmodel);
                    }  else {
                        $this->error("密码与确认密码不一致！","javascript:history.go(-1);");
                    }
                } else {
                    $this->assign('errorinfo',$db->getError());
                } 
            }  else {
                session(NULL);
                $this->error("管理员未登录！",U('Admin/Index/login'));
            }

        }
        $this->display();
    }
    
    function setAdmin_doadd(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){ 
            $very = new \Think\Verify();
            $info = session('admin_editpwd');
            if(!empty($info)){
                if ($very -> check($_POST['captcha'])){
                    $db = new \Model\AdminModel();
                    if (!empty($db -> select($aid))){
                        if($db -> where("id = $aid") -> save($info)){
                            session('admin_id',NULL);
                            session('admin_name',NULL);
                            session('editpwd',NULL);
                            $this->success("修改密码成功！",U('Admin/Index/login'));
                            
                        }  else {
                            $this->error("修改失败！请重试！！",U('Admin/Set/setAdmin_edit'));
                        }
                    }  else {
                        session(NULL);
                    }
                
                }  else {
                    $this->error("验证码错误！","javascript:history.go(-1);");
                }
            }  else {
                $this->error("未提交修改，请重试！！",U('Admin/Set/setAdmin_edit'));
            }
        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        $this->display();
    }
}
