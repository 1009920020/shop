<?php

namespace Model;
use Think\Model;

class UserModel extends Model{
    function checkNamePwd($name,$pwd){
        $info = $this->where("uname = '$name'")->find();
        if($info){
            if($info['pwd'] == sha1($pwd.$info['salt'])){
                return $info;
            }
            return null;
        }
    }
    
    function img_upload($dir,$name,$size){
        if(!empty($_FILES[$name]['name'])){
            $avatarpath = array('rootPath' => $dir);
            $up = new \Think\Upload($avatarpath);
            $z = $up -> uploadOne($_FILES[$name]);
            //文件已存在则覆盖
            $up -> replace = TRUE;
            $b_img = $up -> rootPath.$z['savepath'].$z['savename'];
            $s_img = $up -> rootPath.$z['savepath'].'s_'.$z['savename'];

            $img = new \Think\Image();
            $img -> open($b_img);
            $img -> thumb($size);
            $img -> save($s_img);

            $str[0] = substr($b_img,1);
			$str[1] = substr($s_img,1);
            return $str;
        }
    }

    //一次验证全部
    protected $patchValidate = true;
    //通过定义成员,设置表单规则
    protected $_validate = array(
        //aksdhaiudiw
        //用户名的验证,必须填写
        array('uname','require','用户名为必填项'),
        array('uname','','用户名已存在！',0,'unique'),
        
        array('pwd','require','密码为必填项'),
        array('pwd','/^[0-9a-zA-Z_]{1,}$/','密码必须为字母、数字和下划线组成'),
        array('pwd','3,16','密码位数是3到16之间',0,'length'),
        
        array('repwd','require','确认密码为必填项'),
        array('repwd','/^[0-9a-zA-Z_]{1,}$/','确认密码必须为字母、数字和下划线组成'),
        array('repwd','3,16','确认密码位数是3到16之间',0,'length'),
        
        array('repwd','pwd','两次密码必须一致',0,'confirm'),
        
        array('tel','number','电话必须是数字组成'),
        array('tel','7,11','电话位数是7到11之间',0,'length'),
        
        array('mbda','require','密保答案为必填项'),
        array('mbda','3,18','密保答案位数是3到18之间',0,'length'),
        
        array('address','5,255','地址位数是3到18之间',0,'length'),
        
        array('avatar','require','头像必须上传'),
    );
    
}
