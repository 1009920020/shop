<?php
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends controller{
    //商品展示
    function goods_list(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            if(empty($_GET['p'])){
                $p = 1;
            }  else {
                $p = $_GET['p'];
            }
            $info = D('Goods') -> where("status <> 2") -> order('goods_id') -> page($p.',10') -> select();
            $this->assign('info',$info);            
            $count = D('Goods') -> where("status <> 2") -> order('goods_id') -> count();
            $page = new \Think\Page($count,10);
            $show = $page -> show();
            $this -> assign('page',$show);
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    //商品添加
    function goods_add(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            //获取商品类型列表
            $goodstypelist = D('Goods_type') -> order('typeId') -> select();
            $this->assign('goodstypelist',$goodstypelist);
            
            $goods = new \Model\GoodsModel();
            $goodsinfo = $goods -> create();
            if($goodsinfo){
                $imgss = new \Model\UserModel(); 
                $pic = $imgss -> img_upload('./Public/Admin/Upload/goodsPic/','goods_pic','100,100');
                //$pic = $goods -> img_upload('./Public/Admin/Upload/goodsPic/','goods_pic','100,100');
                $goodsinfo['goods_pic'] = $pic[0];
                $goodsinfo['goods_small_pic'] = $pic[1];
		$this->assign('goods_add',$goodsinfo);
                session('goods_add',$goodsinfo);
                
            }  else {
                $this->assign('errorinfo',$goods->getError());
            }

        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        
        $this->display();
    }
	
    //执行添加商品
    function goods_doadd(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goods = new \Model\GoodsModel();
            $num = $goods -> where("view_index = '展示'") -> count();
            $typedate = session('datetype');
            if ($typedate == 'join_date'){
                $goods_add = session('goods_add');
            }  else {
                $goods_add = session('goods_edit');
            }
            if($goods_add['view_index'] == '1'){
                if((int)$num > 19){                    
                    $this->error("展示在首页的商品数据最多20条!！",U('Admin/Goods/goods_list'));
                    return;
                }
            }

            if($goods_add){
                $goods_add[$typedate] = date("Y-m-d H:i:s");
                if($goods_add['status'] == '0'){
                    $goods_add['status'] = "上架";
                }  else {
                    $goods_add['status'] = "下架";
                }
                if($goods_add['view_index'] == '0'){
                    $goods_add['view_index'] = "不展示";
                }  else {
                    $goods_add['view_index'] = "展示";
                }

                if ($typedate == 'join_date'){
                    $z = $goods->add($goods_add);
                    if(z){
                        //$this->success("添加商品成功!！");
                        $this ->redirect('goods_list', array('aid'=>$aid), 0, '添加商品成功!');
                    }else{
                        $this->error("添加商品失败!！",U('Admin/Goods/goods_add'));
                    }
                }  else {
                    $z = $goods->save($goods_add);
                    if(z){
                        //$this->success("添加商品成功!！");
                        $this ->redirect('goods_list', array('aid'=>$aid), 0, '修改商品成功!');
                    }else{
                        $this->error("修改商品失败!！",U('Admin/Goods/goods_edit'));
                    }
                }
            }  else {
                $this->error("未输入数据!！",U('Admin/Goods/goods_list'));
            }

        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        
        $this->display();
    }

    //商品修改
    function goods_edit(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goodsid = $_GET["goods_id"];
            $info = D('Goods') -> where("goods_id = ($goodsid)") -> order('goods_id') ->select();
            $this->assign('info',$info);
            
            //获取商品类型列表
            $goodstypelist = D('Goods_type') -> order('typeId') -> select();
            $this->assign('goodstypelist',$goodstypelist);
            
            $goods = new \Model\GoodsModel();
            $goodsinfo = $goods -> create();
            if($goodsinfo){
                $imgss = new \Model\UserModel(); 
                if(!empty($_FILES[$name]['name'])){
                    $pic = $imgss -> img_upload('./Public/Admin/Upload/goodsPic/','goods_pic','100,100');                
                    $goodsinfo['goods_pic'] = $pic[0];
                    $goodsinfo['goods_small_pic'] = $pic[1];
                }  else {
                    $goodsinfo['goods_pic'] = $info[0]['goods_pic'];
                    $goodsinfo['goods_small_pic'] = $info[0]['goods_small_pic'];
                }
                $goodsinfo['goods_id'] = $_GET['goods_id'];
		$this->assign('goods_edit',$goodsinfo);
                session('goods_edit',$goodsinfo);
            }  else {
                $this->assign('errorinfo',$goods->getError());
            }
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    
    //删除商品
    function goods_del(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $job = new \Model\GoodsModel();
            $del = $_GET['goods_id'];
            if(!empty($_GET) and $del){
                $info = $job -> delete($del);
                $this ->redirect('goods_list', array('aid'=>$aid), 0, '删除商品成功!');
            }  else {
                $this->error("未选择删除对象!！",U('Admin/Goods/goods_list'));
            }

        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    
    //商品类型展示
    function goods_type(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $info = D('Goods_type') -> where("typeName <> 'default'") -> order('typeId') -> select();
            $this->assign('info',$info);
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    
    //商品类型添加
    function goodstype_add(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goodstype = new \Model\Goods_typeModel();
            $goodstypeinfo = $goodstype -> create();
            if($goodstypeinfo){
                $this->assign('goodstype_add',$goodstypeinfo);
                session('goodstype_add',$goodstypeinfo);
            }  else {
                $this->assign('errorinfo',$goodstype->getError());
            }

        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        
        $this->display();
    }
	
    //执行添加商品类型
    function goodstype_doadd(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goodstype = new \Model\Goods_typeModel();
            $typedate = session('datetype');
            //判断是增加还是编辑
            if ($typedate == 'join_date'){
                $goodstype_add = session(goodstype_add);
            }  else {
                $goodstype_add = session('goodstype_edit');
            }
            if($goodstype_add){
                $goodstype_add[$typedate] = date("Y-m-d H:i:s");
                if ($typedate == 'join_date'){
                    $z = $goodstype->add($goodstype_add);
                    if(z){
                        //$this->success("添加商品类型成功!！");
                        $this ->redirect('goods_type', array('aid'=>$aid), 0, '添加商品类型成功!');
                    }else{
                        $this->error("添加商品类型失败!！",U('Admin/Goods/goodstype_add'));
                    }
                }  else {
                    $z = $goodstype->save($goodstype_add);
                    if(z){
                        //$this->success("修改商品类型成功!！");
                        $this ->redirect('goods_type', array('aid'=>$aid), 0, '修改商品类型成功!');
                    }else{
                        $this->error("修改商品类型失败!！",U('Admin/Goods/goodstype_edit'));
                    }
                }
            }  else {
                $this->error("未输入数据或者该类型已存在！请核实！",U('Admin/Goods/goods_type'));
            }

        }  else {
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        
        $this->display();
    }
    
    function goodstype_edit(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goodstypeid = $_GET["typeid"];
            $info = D('Goods_type')-> where("typeId = ($goodstypeid)") -> order('typeId') -> select();
            $this->assign('info',$info);
            
            $goodstype = new \Model\Goods_typeModel();
            $goodstypeinfo = $goodstype -> create();
            if($goodstypeinfo){
                $goodstypeinfo['typeId'] = $_GET['typeid'];
		$this->assign('goodstype_edit',$goodstypeinfo);
                session('goodstype_edit',$goodstypeinfo);
            }  else {
                $this->assign('errorinfo',$goodstype->getError());
            }
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    
    //删除商品类型
    function goodstype_del(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goodstype = new \Model\Goods_typeModel();
            $del = $_GET['typeid'];
            //要删除的类型名称
            $info = $goodstype ->where("typeId = $del") ->field("typeName") ->select();
            $typename = $info[0]['typename'];
            if(!empty($_GET) and $del){
                //将类型修改为默认
                D("Goods") -> where("goods_type = '$typename'") -> setField('goods_type','default');
                $info = $goodstype -> delete($del);
                $this ->redirect('goods_type', array('aid'=>$aid), 0, '删除商品类型成功!');
            }  else {
                $this->error("未选择删除对象!！",U('Admin/Goods/goods_type'));
            }

        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    function goods_rb(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goods = new \Model\GoodsModel();
            $mv = $_GET['goods_id'];
            if(!empty($_GET) and $mv){     
                $obj["status"] = '2';
                $rb_obj = $goods -> where("goods_id = $mv") -> save($obj);
                if($rb_obj){
                    $this ->redirect('goods_list', array(), 0, '移入回收站成功!');
                }
                
            }  else {
                $this->error("未选择移动对象!！",U('Admin/Goods/goods_list'));
            }

        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        $this->display();
    }
    
    //商品回收站
    function rb(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            if(empty($_GET['p'])){
                $p = 1;
            }  else {
                $p = $_GET['p'];
            }
            $info = D('Goods') -> where("status = 2") -> order('goods_id') -> page($p.',10') -> select();
            $this->assign('info',$info);            
            $count = D('Goods') -> where("status = 2") -> order('goods_id') -> count();
            $page = new \Think\Page($count,10);
            $show = $page -> show();
            $this -> assign('page',$show);
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    //移出商品回收站
    function rb_mv(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goods = new \Model\GoodsModel();
            $mv = $_GET['goods_id'];
            if(!empty($_GET) and $mv){  
                if($_GET['c'] == '上架'){
                    $obj["status"] = '上架';
                    $rb_obj = $goods -> where("goods_id = $mv") -> save($obj);
                }  else {
                    $obj["status"] = '下架';
                    $rb_obj = $goods -> where("goods_id = $mv") -> save($obj);
                }

                if($rb_obj){
                    $this ->redirect('rb', array(), 0, '商品恢复成功!');
                }
                
            }  else {
                $this->error("未选择恢复对象!！",U('Admin/Goods/rb'));
            }

        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }
        $this->display();
    }
    
    //订单展示
    function order_list(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            if(empty($_GET['p'])){
                $p = 1;
            }  else {
                $p = $_GET['p'];
            }
            $info = D('Orders') -> order('id') -> page($p.',10') -> select();
            $this->assign('info',$info);            
            $count = D('Orders') -> order('id') -> count();
            $page = new \Think\Page($count,10);
            $show = $page -> show();
            $this -> assign('page',$show);
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    function order_xq(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $id = $_GET['id'];
            $info = D('Orders') -> where("id = '$id'") -> select();
            $this -> assign('order_xq',$info);
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    function order_send(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $goodsorder = new \Model\OrdersModel();
            $send = $_GET['id'];
            if(!empty($_GET) and $send){
                $goodsorder -> status = '已发货';
                $goodsorder -> up_time = date("Y-m-d H:i:s");
                $goodsorder -> where("id = '$send'") ->save();
                $this->success("订单发货成功!",U('Admin/Goods/order_list'));
            }  else {
                $this->error("未选择发货对象!！",U('Admin/Goods/order_list'));
            }

        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    function order_del(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $id = $_GET['id'];
            $info = D('Orders') -> delete($id);
            $this->success("订单删除成功!",U('Admin/Goods/order_list'));
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
    function order_delall(){
        $aid = (int)session('admin_id');
        $a_status = D('Admin') -> select($aid);
        if(!empty($aid) and !empty($a_status)){
            $info = D('Orders') -> query("truncate table orders");
            if($info){
                $this->success("订单删除成功!",U('Admin/Goods/order_list'));
            }  else {
                $this->error("订单删除失败!",U('Admin/Goods/order_list'));
            }
            
        }  else {
            session(NULL);
            $this->error("管理员未登录！",U('Admin/Index/login'));
        }

        $this->display();
    }
    
}