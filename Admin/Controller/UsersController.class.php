<?php
namespace Admin\Controller;
use Think\Controller;

class UsersController extends controller{
    //用户展示
    function users_list(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            if(empty($_GET['p'])){
                $p = 1;
            }  else {
                $p = $_GET['p'];
            }
            $info = D('User') -> order('id') -> page($p.',10') -> select();
            $this->assign('info',$info);            
            $count = D('User') -> order('id') -> count();
            $page = new \Think\Page($count,10);
            $show = $page -> show();
            $this -> assign('page',$show);
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    //用户添加
    function users_add(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $users = new \Model\UserModel();
            $usersinfo = $users -> create();
            if($usersinfo){
                if(!empty($_FILES[$name]['name'])){
                    $pic = $users -> img_upload('./Public/Home/Upload/avatar/','avatar','100,100');
                    $usersinfo['avatar'] = $pic[1];
                }  else {
                    $usersinfo['avatar'] = constant('IMG_URL1')."avatar.png";
                }
                if ($usersinfo['pwd'] == $_POST['repwd']){
                    if ($usersinfo['sex'] == '0'){
                        $usersinfo['sex'] = '男';
                    }  else {
                        $usersinfo['sex'] = '女';
                    }

                    $this->assign('info',$usersinfo);
                    //密码盐加密
                    $usersinfo['salt'] = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_RANDOM));
                    $usersinfo['pwd'] = sha1($usersinfo['pwd'].$usersinfo['salt']);
                    $usersinfo['mbda'] = sha1($usersinfo['mbda'].$usersinfo['salt']);
                    $usersinfo['join_date'] = session('p_time');
                    if (!empty($usersinfo)){
                        session('users_add_info',$usersinfo);
                    }
                }else{
                    $this->error("两次密码不一致！请重试！","javascript:history.go(-1);");
                }
        }  else {
            $this->assign('errorinfo',$users->getError());
        }

        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        
        $this->display();
    }
	
    //执行添加用户
    function users_doadd(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $users = new \Model\UserModel();
            $typedate = session('datetype');
            if ($typedate == 'join_date'){
                $info = session('users_add_info');
            }  else {
                $info = session('users_edit_info');
            }
            
            if(!empty($info)){
                $uname = $info['uname'];
                $very = new \Think\Verify();
                if ($very -> check($_POST['captcha'])){
                    $info[$typedate] = date("Y-m-d H:i:s");
                    if ($typedate == 'join_date'){
                        if($users -> add($info)){
                            $uid = $users -> getLastInsID();
                            $this ->redirect('users_list', array(), 0, '添加成功!');
                            session('users_add_info',NULL);
                        }  else {
                            $this->error("添加失败！请重试！！",U('Admin/Users/users_add'));
                        }    
                    }  else {
                        if($users -> save($info)){
                            $this ->redirect('users_list', array(), 0, '<br />修改用户  <b>$uname</b> 信息成功！');
                            session('users_edit_info',NULL);
                        }  else {
                            $this->error("修改失败！请重试！！",U('Admin/Users/users_list'));
                        }
                    }
                }  else {
                    $this->error("验证码错误！","javascript:history.go(-1);");
                }
            }  else {
                $this ->redirect('users_list', array(), 2, '未输入数据!');
            }

        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        
        $this->display();
    }

    //用户修改
    function users_edit(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $usersid = $_GET["id"];
            $info = D('User')->where("id = ($usersid)")->select();
            $this->assign('info',$info);
            
            $users = new \Model\UserModel();
            $usersinfo = $users -> create();
            if($usersinfo){
                if(!empty($_FILES[$name]['name'])){
                    $pic = $users -> img_upload('./Public/Home/Upload/avatar/','avatar','100,100');
                    $usersinfo['avatar'] = $pic[1];
                }  else {
                    $usersinfo['avatar'] = constant('IMG_URL1')."avatar.png";
                }
                $usersinfo['id'] = $_POST['id'];
                $usersinfo['uname'] = $_POST['uname1'];
                
                if($_POST['pwd1'] != $info[0]['pwd'] and $_POST['mbda1'] != $info[0]['mbda']){
                    $usersinfo['salt'] = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_RANDOM));
                    $usersinfo['pwd'] = sha1($_POST['pwd1'].$usersinfo['salt']);
                    $usersinfo['mbda'] = sha1($_POST['mbda1'].$usersinfo['salt']);
                }
                elseif($_POST['pwd1'] == $info[0]['pwd'] and $_POST['mbda1'] != $info[0]['mbda']){
                    $this->error("必须是密码和密保都更改！或者都不更改！！",U('Admin/Users/users_edit'));
                }
                elseif($_POST['pwd1'] != $info[0]['pwd'] and $_POST['mbda1'] == $info[0]['mbda']){
                    $this->error("必须是密码和密保都更改！或者都不更改！！",U('Admin/Users/users_edit'));
                }  else {
                    $usersinfo['pwd'] = $_POST['pwd1'];
                    $usersinfo['mbda'] = $_POST['mbda1'];
                }
                $usersinfo['address'] = $_POST['address1'];
		$this->assign('info',$usersinfo);
                session('users_edit_info',$usersinfo);
            }  else {
                $this->assign('errorinfo',$users->getError());
            }
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    
    //删除用户
    function users_del(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $job = new \Model\UserModel();
            $del = $_GET['id'];
            if(!empty($_GET) and $del){
                $info = $job -> delete($del);
                $this ->redirect('users_list', array('aid'=>$aid), 2, '删除用户成功!');
            }  else {
                $this->error("未选择删除对象！",U('Admin/Users/users_list'));
            }

        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
}