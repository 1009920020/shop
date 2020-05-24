<?php

namespace Model;
use Think\Model;
class Goods_typeModel extends Model{
    //一次验证全部
    protected $patchValidate = true;
    //通过定义成员,设置表单规则
    protected $_validate = array(
        //aksdhaiudiw
        //用户名的验证,必须填写
        array('typeName','require','商品类型名称为必填项'),
        array('typeName','','商品类型名称已存在！',0,'unique'),
    );
}