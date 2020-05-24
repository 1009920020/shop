<?php

namespace Model;
use Think\Model;
class GoodsModel extends Model{	
    //一次验证全部
    protected $patchValidate = true;
    //通过定义成员,设置表单规则
    protected $_validate = array(
        //aksdhaiudiw
        //用户名的验证,必须填写
        array('goods_name','require','商品名称为必填项'),
		
        array('goods_price','require','现价为必填项'),
        array('goods_price','/^(([^0][0-9]+|0)\.([0-9]{1,2})$)|^(([^0][0-9]+|0)$)|^(([1-9]+)\.([0-9]{1,2})$)|^(([1-9]+)$)/','现价必须是整数或小数，最多两位小数'),
	array('goods_old_price','require','原价为必填项'),
        array('goods_old_price','/^(([^0][0-9]+|0)\.([0-9]{1,2})$)|^(([^0][0-9]+|0)$)|^(([1-9]+)\.([0-9]{1,2})$)|^(([1-9]+)$)/','原价必须是整数或小数，最多两位小数'),
        
        array('goods_description','require','商品描述为必填项'),
        
        array('num','require','库存为必填项'),
        array('num','number','库存必须是整数'),
        
        array('goods_pic','require','商品例图必须上传'),
        array('goods_small_pic','require','商品缩略图必须上传'),
    );
    
}
