<?php

namespace Home\Controller;
use Think\Controller;
use Model;

class UserController extends Controller{    
    //登录系统
    function login(){
        $uid = session('uid');
        if(empty($uid)){
            $this->display();
        }  else {
            $this->error("已登录！",'javascript:history.go(-1);');
        }
    }
            
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
            
    function logout(){
        $uid = session('uid');
        if(!empty($uid)){
            session('uid',NULL);
            session('uname',NULL);
            $this->display();
        }  else {
            $this->error("非法操作……","javascript:history.go(-1);");
        }
    }

    //注册
    function regist(){
        $uid = session('uid');
        if(empty($uid)){
            $user = new \Model\UserModel();   
            //展示,收集        
            $info = $user -> create();
            if($info){                
                $uname = $info['uname'];
                $pduser = $user -> where("uname='$uname'") -> select();
                $str = '<a href="regist">重试</a></b>';
                if (!empty($pduser)){
                    $tishi = "用户名 <b>$uname</b> 已存在！请 ".$str;
                } else {
                    $info['avatar'] = constant('IMG_URL1')."avatar.png";
                    if ($info['pwd'] == $_POST['repwd'] and empty($pduser)){
                        if ($info['sex'] == '0'){
                            $info['sex'] = '男';
                        }  else {
                            $info['sex'] = '女';
                        }
                        //密码盐加密
                        $info['salt'] = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_RANDOM));
                        $info['pwd'] = sha1($info['pwd'].$info['salt']);
                        $info['mbda'] = sha1($info['mbda'].$info['salt']);
                        $info['join_date'] = session('p_time');
                        session('regist_info',$info);
                        if (!empty(session('regist_info'))){                                
                            $tishi = "额哦，出现了验证码……";
                        }
                    }else{
                        $tishi = "两次密码不一致！请重试";
                    }
                }
            }  else {
                $this->assign('errorinfo',$user->getError());
            }
        }  else {
            $this->error("已登录！",'javascript:history.go(-1);');
        }

        $this->assign('tishi',$tishi);
        $this->display();
    }
    function register(){
        $uid = session('uid');
        if(empty($uid)){
            $user = new \Model\UserModel();  
            $very = new \Think\Verify();
            $info = session('regist_info');
            if(!empty($info)){
                $uname = $info['uname'];
                if ($very -> check($_POST['captcha'])){
                    $info['join_date'] = date("Y-m-d H:i:s");
                    if($user -> add($info)){
                        $uid = $user -> getLastInsID();
                        $tishi = "注册成功！请牢记：<br />用户名  <b>$uname</b><br />用户ID  <b>$uid</b>";
                        session('regist_info',NULL);
                    }  else {
                        $tishi = "注册失败！请重试";
                        $this->error("注册失败！请重试！！",U('Home/user/regist'));
                    }                
                }  else {
                    $this->error("验证码错误！","javascript:history.go(-1);");
                }
            }  else {
                $this->error("未提交注册，请重试！！",U('Home/user/regist'));
            }
            
        }  else {
            $this->error("已登录！",'javascript:history.go(-1);');
        }
        
        $this->assign('tishi',$tishi);
        $this->display();
    }
    function center(){
        $temp = (int)session('uid');
        if(!empty($temp)){
            $db = D('User');
            $user = $db->create();
            if (!empty($temp)){
                $info = $db -> select($temp);
                if (!empty($info)){
                    $this -> assign('userinfo',$info);
                    if($_GET['uid'] != session('uid') or empty($_GET['uid'])){
                        $this->error("非法操作！！！",'javascript:history.go(-1);');
                    }
                }  else {
                    //如果在数据库中查不到该用户了，可能已被除名，马上清空session
                    session(NULL);
                }
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    function edituserinfo(){
        $uid = session('uid');
        if(!empty($uid)){
            $db = new \Model\UserModel();
            $userinfo = $db -> select($uid);
            if ($userinfo){
                $this -> assign('userinfo',$userinfo);
            } else {
                session(NULL);
                $tishi[] = "原数据获取失败，请重新登录……";
            }

            $userinfo1 = $db -> create();
            if($userinfo1){
                $avatar = $db -> img_upload('./Public/Home/Upload/avatar/','avatar','100,100');
                $userinfo1['avatar']= $avatar[1];
                if ($_POST['tel'] != "" and $_POST['email'] != "" and $_POST['address'] != ""){
                    if ($userinfo1['sex'] == '0'){
                        $userinfo1['sex'] = '男';
                    }  else {
                        $userinfo1['sex'] = '女';
                    }
                    $userinfo1['up_time']= date("Y-m-d H:i:s");
                                        
                    $info = $db-> where("id=($uid)") -> save($userinfo1);
                    if ($info !== false){
                        $tishi[] = "修改成功！";
                    } else {
                        $tishi[] = "修改失败！";
                    }
                }
            } else {
                $this->assign('errorinfo',$db->getError());
            }
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    
        $this->assign('tishi',$tishi);
        $this->display();
    }
    
    function editpwd(){
        $uid = (int)session('uid');
        $db = new \Model\UserModel();
        if(!empty($uid)){
            $userinfo = $db -> select($uid);
            if(!empty($userinfo)){
                $this->assign('userinfo',$userinfo);
                $usersmodel = $db -> create();
                if($usersmodel){
                    if ($userinfo){
                        //密码盐
                        $usersmodel['salt'] = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_RANDOM));
                        if($usersmodel['pwd'] == $_POST['repwd']){
                            //将密码进行密码盐加密
                            $usersmodel['pwd'] = sha1($usersmodel['pwd'].$usersmodel['salt']);
                            $usersmodel['mbda'] = sha1($usersmodel['mbda'].$usersmodel['salt']);
                            
                            $tishi = "额哦，出现了验证码……";
                            $usersmodel['uname'] = $userinfo[0]['uname'];
                            session('editpwd',$usersmodel);
                        }  else {
                            $this->error("密码与确认密码不一致！","javascript:history.go(-1);");
                        }
                    } else {
                        $this->error("原数据获取失败，请重试……","javascript:history.go(-1);");
                    }
                } else {
                    $this->assign('errorinfo',$db->getError());
                } 
            }  else {
                session(NULL);
            }

        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
        $this->assign('tishi',$tishi);
        $this->display();
    }
    
    function doeditpwd(){
        $uid = (int)session('uid');
        if(!empty($uid)){     
            //$db = new \Model\UserModel();
            $very = new \Think\Verify();
            $info = session('editpwd');
            if(!empty($info)){
                $uname = $info['uname'];
                if ($very -> check($_POST['captcha'])){
                    $db = new \Model\UserModel();
                    if (!empty($db -> select($uid))){
                        
                        $info['up_time'] = date("Y-m-d H:i:s");
                        
                        if($db -> where("id = $uid") -> save($info)){
                            $pwd = session('editpwd_pwd');
                            $mbda = session('editpwd_mbda');session('editpwd',NULL);
                            session('uid',NULL);
                            session('uname',NULL);
                            session('editpwd_pwd',NULL);
                            session('editpwd_mbda',NULL);                            
                            $uid = $db -> getLastInsID();
                            $this->success("用户ID：$uid&nbsp;&nbsp;用户名  <b>$uname</b>修改密码/密保成功！请牢记！</br>",U('Home/user/login'));
                            
                        }  else {
                            $this->error("修改失败！请重试！！",U('Home/user/editpwd'));
                        }
                    }  else {
                        session(NULL);
                    }
                
                }  else {
                    $this->error("验证码错误！","javascript:history.go(-1);");
                }
            }  else {
                $this->error("未提交修改，请重试！！",U('Home/user/center/uid/'.$uid));
            }
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
        $this->assign('tishi',$tishi);
        $this->display();
    }
    
        
    function backpwd(){
        session('backpwd',NULL);
        $db = new \Model\UserModel();
        $info = $db -> create();
        $uid = (int)session('uid');
        if(empty($uid)){
            if($info){
                $id = (int)$info["id"];
                $un = $db -> where("id=($id)") -> select();
                $mbda = sha1($info["mbda"].$un[0]['salt']);
                if ($un[0]['uname'] == $info["uname"] and $un[0]['mbda'] == $mbda){
                    $temp = $un[0]['pwd'];
                    if(count($temp) != 0){
                        $this->assign('backpwduname',$un[0]['uname']);
                        session('backpwd',$info);
                    }
                } else {
                    $this->error("操作结果：信息错误，重置失败！请重试");
                }
            }  else {
                $this->assign('errorinfo',$db->getError());
            }
        }  else {
            $this->error("已登录！",'javascript:history.go(-1);');
        }
        $this -> assign('pwd',$pwd);
        $this->display();
    }
    
    
    function dobackpwd(){
        $db = new \Model\UserModel();
        $very = new \Think\Verify();
        $uid = (int)session('uid');
        if(empty($uid)){
            $info = $db -> create();
            if($info){
                if ($very -> check($_POST['captcha'])){
                    //验证的信息
                    $yz = session('backpwd');
                    dump($yz);
                    if($info['pwd'] == $_POST['repwd']){
                        $id = $yz['id'];
                        $uname = $yz['uname'];
                        $mbda = $yz['mbda'];
                        
                        $info['salt'] = base64_encode(mcrypt_create_iv(32,MCRYPT_DEV_RANDOM));
                        //将密码、密保答案重新进行密码盐加密
                        $info['pwd'] = sha1($info['pwd'].$info['salt']);
                        $info['mbda'] = sha1($mbda.$info['salt']);
                        $info['up_time'] = date("Y-m-d H:i:s");
                        if($db -> where("id = $id") -> save($info)){
                            $uid = $db -> getLastInsID();
                            session('backpwd',NULL);
                            $this->success("用户ID：$id<br/>用户名  <b>$uname</b><br/>重置密码成功！请牢记！<br/>",U('Home/user/login'));
                        }  else {
                            $this->error("重置密码失败！请重试！！",U('Home/user/backpwd'));
                        }

                    }  else {
                        $this->error("密码与确认密码不一致！","javascript:history.go(-1);");
                    }
                }  else {
                    $this->error("验证码错误！","javascript:history.go(-1);");
                }
            }  else {
                $this->error("未输入密码！","javascript:history.go(-1);");
            }
        }  else {
            $this->error("已登录！",'javascript:history.go(-1);');
        }
        $this->display();
    }
            

    function order(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                $db = D('Orders');
                $order = $db -> where("user_id=($uid)") -> select();
                if ($order){
                    $this -> assign('order',$order);
                }
            }  else {
                session(NULL);
            }

            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }

    function buy(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                session("ddlx",1);
                $db = D('Cart');
                $dd = $db->create();
                if (!empty($dd[count])){
                    $dd['user_id'] = $uid;
                    $this -> assign('buy',$dd);
                } else {
                    $this->error("数量不可为空！请重试……","javascript:history.go(-1);");
                }  
            }  else {
                session(NULL);
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    function cart(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                session("ddlx",0);
                $db = D('Cart');
                $cartlist = $db->create();
                $cartlist['money'] = $cartlist['goods_price']*$cartlist['count'];
                $cartlist['user_id'] = $uid;
                $qe = $cartlist["goods_id"];
                if ($_POST['tocart'] != 'true'){
                    if ($qe){
                        $temp = $db -> where("goods_id=($qe) and user_id=($uid)") -> select();
                    } 

                    if (!empty($cartlist['count'])){
                        if (!empty($temp) and $cartlist["goods_id"] == $temp[0]['goods_id']){
                            $cartlist["count"] = (int)$temp[0]['count'] + (int)$cartlist['count'];
                            $cartlist["id"] = (int)$temp[0]['id'];
                            $db -> save($cartlist);

                        }
                        if (empty($temp) or $temp == FALSE){
                            $db -> add($cartlist);
                        }
                    }  else {
                         $this->error("数量不可为空！","javascript:history.go(-1);");
                    }
                }

                $info = $db -> where("user_id = $uid") -> select();
                //直接进购物车时无需判断，直接展示所有记录
                if (!empty($info)){
                    $this -> assign('cartlist',$info);
                }  else {
                    $this -> assign('cartlist',array(""=>""));
                }
            }  else {
                session(NULL);
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    function clearcart(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                $db = D('Cart');
                $info = $db -> where("user_id=($uid)") -> delete();

                if ($info){
                    $this->success("清空购物车成功！");
                } else {
                    $this->error("清空购物车失败！请重试……",U('Home/user/cart'));
                }
            }  else {
                session(NULL);
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }

    function payall(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                //获取单订单信息
               $info = D('Orders') -> create();
               session('ordersing',$info);
               $this -> assign('ordersing',$info);
               
               //计算该用户购物车中的总金额
               if (session('ddlx') == 0){       
                    $cart = D('Cart');            
                    $info = $cart -> where("user_id=($uid)") -> field('money,count') -> select();
                    foreach ($info as $k => $v){
                        $hb4[] = $v['money'] * $v['count'];
                     }
                     $money = array_sum($hb4);
                     $this -> assign('money',$money);
               }

               //付款方式
               $info1 = D('Pay') -> select();
               $this -> assign('pay',$info1);

               //收货地址
               $address = D('Address') -> where("id=($uid)") -> field('addr_id,id',true) -> select(); 
               foreach ($address as $k => $v){
                   foreach ($v as $kk => $vv){
                       $addr[] =  str_ireplace(",|:",' | ',$vv);
                   }
               }
               $this -> assign('addr',$addr);
            }  else {
                session(NULL);
            }

           $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    
    function paysuccess(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                if (session('ddlx') == 0){        
                    //购物车结算
                    $cart = D('Cart');            
                    $info = $cart -> where("user_id=($uid)") -> field('id,user_id,money',true) -> select();
                    foreach ($info as $k => $v){
                        $hb1[] = $v['goods_id'];
                        $hb2[] = $v['goods_name'];
                        $hb3[] = $v['goods_price'];
                        $hb4[] = $v['count'];
                    }
                    //判断订单数量是否为0，为0则拒绝操作并提示
                    if (!empty($hb4)){
                        $hbh1 = implode(',',$hb1);
                        $hbh2 = implode(',',$hb2);
                        $hbh3 = implode(',',$hb3);
                        $hbh4 = implode(',',$hb4);
                        foreach ($hb3 as $k =>$v){
                            $money1[] = $v*$hb4[$k];
                        }
                        $money = 0;
                        foreach ($money1 as $k =>$v){
                            $money = $money + $v;
                        }
                        $info = array('user_id'=>session('uid'),'goods_id'=>$hbh1,'goods_name'=>$hbh2,'goods_price'=>$hbh3,'count'=>$hbh4,'money'=>$money,'order_time'=>date("Y-m-d H:i:s"));
                    }  else {
                        $this->error("未购买商品，本次操作被拒绝！具体原因：订单数量为零。请重试……",U('Home/Index/index'));
                    }
                }
                if (session('ddlx') == 0 or session('ddlx') == 1){
                    $ddxx = $_POST;
                    $ddxx['status'] = '未发货';
                    $info['status'] = '未发货';
                    $ddxx['order_time'] = date("Y-m-d H:i:s");
                    $info['order_time'] = date("Y-m-d H:i:s");
                    $db = D('Orders');
                    $ord = $db -> create();
                    $add = addr.$ord ['address'];
                    $pay = $ord ['pay_way'];

                    $address = D('Address') -> where("id=($uid)") -> field("($add)") -> select();
                    foreach ($address[0] as $k => $v){
                        $ddxx["address"] = str_ireplace(",|:"," | ",$v,$count);
                        $info["address"] = str_ireplace(",|:"," | ",$v,$count);
                    }
                    $pay_way = D('Pay') -> where("id = ($pay)") -> select();
                    foreach ($pay_way as $k => $v){
                         $ddxx["pay_way"] = $v["pay_method"];
                         $info["pay_way"] = $v["pay_method"];
                    }
                }
                if (session('ddlx') == 1){
                    $db -> add($ddxx);
                    //减库存
                    $goodsid = $ddxx['goods_id'];
                    $num = D("Goods") -> where("goods_id = ($goodsid)") -> field("num") -> select();
                    $num1 = (int)$num[0]['num'] - (int)$ddxx['count'];
                    D("Goods") -> num = $num1;
                    D("Goods") -> where("goods_id = ($goodsid)") -> save();
                    $this->success("下单成功！",U('User/order/uid/'.$uid));
                }  else {
                    if ($db -> add($info)){
                        //减库存
                        foreach ($hb1 as $k => $v){
                            $num = D("Goods") -> where("goods_id = ($v)") -> field("num") -> select();
                            $num1[] = (int)$num[0]['num'] - (int)$hb4[$k];
                        }
                        foreach ($hb1 as $k => $v){
                            D("Goods") -> num = $num1[$k];
                            D("Goods") -> where("goods_id = ($v)") -> save();
                        }
                        
                        $this->success("下单成功！",U('User/order/uid/'.$uid));
                        $info = $cart -> where("user_id=($uid)") -> delete();
                    }
                }        
                $orderid = $db -> getLastInsID();
                $this -> assign('orderid',$orderid); 
            }  else {
                session(NULL);
            }
            $this->display();

        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }

    
    function address(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                $db = D('Address');
                $info = $db -> where("id=($uid)") -> field('id,addr_id',true) ->select();
                $this -> assign('addre',$info);
                foreach ($info as $k => $v){
                    foreach ($v as $kk => $vv){
                        $addlist[] = explode(",|:",$vv);
                    }
                }
                foreach ($addlist as $k => $v){
                    $name[] = $v[0];            
                    $tel[] = $v[1];
                    $add[] = $v[2];
                }
                $this -> assign('name',$name);
                $this -> assign('tel',$tel);
                $this -> assign('addr',$add);
            }  else {
                session(NULL);
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    
    function upaddr(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                $db = D('Address');        
                $addr = $_GET["addr"];
                $info = $db -> where("id=($uid)") -> field("($addr)") ->select();

                foreach ($info as $k => $v){
                    foreach ($v as $kk => $vv){
                        $addxx = explode(",|:",$vv);
                    }
                }
                session("addr",$addr);
                $addr_id = $db -> where("id=($uid)") -> field("addr_id") ->select();
                session("addr_id",(int)$addr_id[0]["addr_id"]);

                $this -> assign('addxx',$addxx);
            }  else {
                session(NULL);
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    
    function doupaddr(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                $db = D('Address');
                if(!empty($_POST)){
                    $info = $_POST;
                    $addstr = $info[user_name].",|:".$info[tel].",|:".$info[addr];
                    $addr_id = session('addr_id');
                    $addr = session('addr');
                    $add = array($addr => "$addstr");
                    $rst = $db -> where("id=($uid) and addr_id=($addr_id)") -> save($add);
                    if ($rst){
                        $this->success("修改地址信息成功！");
                    } else {
                        $this->error("修改地址信息失败！请重试……","javascript:history.go(-1);");
                    }
                }
            }  else {
                session(NULL);
            }
                    
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    function deladd(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            if(!empty(D('User') -> select($uid))){
                $db = D('Address');        
                $addr = $_GET["addr"];
                $addr_id = $db -> where("id=($uid)") -> field("addr_id") -> select();
                $addr_id = (int)$addr_id[0]['addr_id'];
                $add = array($addr => "");
                $rst = $db -> where("id = ($uid) and addr_id = ($addr_id)") -> save($add);
                if ($rst){
                    $this->success("删除地址信息成功！");
                } else {
                    $this->error("删除地址信息失败！请重试……");
                }
            }  else {
                session(NULL);
            }
            $this->display();
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
    
    function add_address(){
        $uid = (int)session('uid');
        if(!empty($uid)){
            $very = new \Think\Verify();
            if ($very -> check($_POST['captcha'])){
                if(!empty(D('User') -> select($uid))){
                    $db = D('Address');
                    $pdin = $db -> where("id=($uid)") -> select();
                    $user = D('User');
                    $userinfo = $user -> where("id=($uid)") -> select();
                    if(empty($pdin) and ($userinfo[0]['uname']) == $_POST['uname']){
                        $db -> id = $uid;
                        $info = $_POST;
                        $addstr = $info[uname].",|:".$info[tel].",|:".$info[address];
                        $db -> addr0 = $addstr;
                        if($db -> add()){
                            $this->success("添加地址表单信息成功！");
                        }  else {
                            $this->error("添加地址表单信息失败！请重试……",'javascript:history.go(-1);');
                        }
                    }  else {
                        $this->error("输入的用户名与登录账号的用户名不符或已存在表单！",'javascript:history.go(-1);');
                    }
                }  else {
                    session(NULL);
                }
            }  else {
                $this->error("验证码错误！",U('Home/User/address'));
            }
        }  else {
            $this->error("未登录！",U('Home/User/login'));
        }
    }
}