<?php

namespace Model;
use Think\Model;
class AdminModel extends Model{
    //密码盐加密了
    function checkNamePwd($name,$pwd){
        $info = $this->where("uname = '$name'")->find();
        if($info){
            if($info['pwd'] == sha1($pwd.$info['salt'])){
                return $info;
            }
            return null;
        }
    }
}