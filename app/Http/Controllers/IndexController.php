<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class IndexController extends Controller
{
    public function index(Request $request)
    {
        $data=cache('data');
        if(!$data){
//            echo 2;
            $data = DB::table('shop_goods')->limit(4)->get();
            cache(['data'=>$data],60*24);
        }
        $count=cache('count');
        if(!$count){
            $count=DB::table('shop_goods')->count();
            cache(['count'=>$count],60*24);
        }
        $cart=cache('cart');
        if(!$cart){
            $cart=DB::table('shop_category')->where('pid',0)->limit(8)->get();
            cache(['cart'=>$cart],60*24);
        }
        foreach ($data as $k => $v){
            $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
            $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
            $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
        }
        $session=$request->session()->get('users');
//        dd($data);exit;
        return view('index/index',compact('data','session','count','cart'));
    }
    //新品  销量  价格的排序
    public function shop()
    {
        $name=request()->name;
        if ($name == '新品'){
            $order='create_time';
            $shun='desc';
        }else if ($name =='销量'){
            $order='goods_num';
            $shun='desc';
        }else{
            $order='goods_selfprice';
            $shun='asc';
        }
        $data = cache('data');
        if(!$data){
            $data = DB::table('shop_goods')->orderBy($order,$shun)->get();
            cache(['data'=>$data],60*24);
        }
        foreach ($data as $k => $v){
            $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
            $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
            $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
        }
        return view('index/order',['data'=>$data]);
    }
    //全部商品
    public function prolist()
    {
        $sou=request()->input();
        $name=$sou['sou'] ?? '';
        $where=[];
        if ($name){
            $where[]=[
                'goods_name','like',"%$name%"
            ];
        }
        $data = cache('data');
        if(!$data){
            $data = DB::table('shop_goods')->where($where)->get();
            cache(['data'=>$data],60*24);
        }
        foreach ($data as $k => $v){
            $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
            $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
            $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
        }
        return view('index/prolist',['data'=>$data]);
    }
    //加入购物车
    public function add()
    {
        $session=request()->session()->get('users');
        if ($session==null){
            return 0;die;
        }
        $data=request()->input();
        $data['user_id']=$session['user_id'];
        $where=[
            'goods_id'=>$data['goods_id'],
            'user_id'=>$data['user_id']
        ];
        $count=DB::table('shop_cart')->where($where)->count();
        if ($count>0){
            return ['font'=>'该商品已在购物车内','code'=>5];die;
        }else{
            $res=DB::table('shop_cart')->insert($data);
            if ($res){
                return ['font'=>'添加购物车成功','code'=>6];die;
            }else{
                return ['font'=>'添加购物车失败','code'=>5];die;
            }
        }
    }
    //购物车
    public function car()
    {
        $data=DB::table('shop_cart')
            ->join('shop_goods','shop_cart.goods_id','=','shop_goods.goods_id')
            ->where('is_del',1)
            ->get();
        foreach ($data as $k => $v){
            $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
            $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
            $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
            $data[$k]->newprice=$v->goods_selfprice*$v->cart_shuliang;
        }
//        dd($data);
        return view('index/car',['data'=>$data]);
    }
    //删除购物车单条数据
    public function cartDel()
    {
        $id=request()->id;
        $where = [
            'cart_id'=> $id
        ];
        // 用in 是因为单删
        $info = [
            'is_del'=>2
        ];
        $res = DB::table('shop_cart')->where($where)->update($info);
        if ($res){
            return ['font'=>'删除成功','code'=>6];die;
        }else{
            return ['font'=>'删除失败','code'=>5];die;
        }
    }
    //更改购买数量
    public function changeBuyNmber()
    {
        $id=request()->cart_id;
        $num=request()->goods_num;
        $data=[
            'cart_shuliang'=>$num
        ];
        DB::table('shop_cart')->where('cart_id',$id)->update($data);
    }
    //获取小计
    public function getSubTotal()
    {
        $num=request()->goods_num;
        $price=request()->price;
        return $newprice=$num*$price;

    }
    //单条数据商品详情
    public function proinfo()
    {
        $goods_id=request()->goods_id;
        $data=DB::table('shop_goods')->where('goods_id',$goods_id)->get();
        foreach ($data as $k => $v){
            $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
            $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
            $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
        }
        $session=request()->session()->get('users');
        if ($session){
            $adata=[
                'goods_id'=>$goods_id,
                'user_id'=>$session['user_id']
            ];
            $count=DB::table('shop_shou')->where($adata)->count();
            if ($count>0){
                DB::table('shop_liu')->where($adata)->update(['create_time'=>time()]);
            }
            $adata['create_time']=time();
            DB::table('shop_liu')->insert($adata);
        }
        return view('index/proinfo',['data'=>$data]);
    }
    //购买件数得到的价格
    public function newprice()
    {
        $id=request()->id;
        $num=request()->kucun;
        $data=DB::table('shop_goods')->where('goods_id',$id)->get();
        $price=$data[0]->goods_selfprice;
        $newprice=$num*$price;
        return $newprice;
    }
    //收藏
    public function shoucang()
    {
        $session=request()->session()->get('users');
        if ($session==null){
            return 1;die;
        }
        $id=request()->id;
        $data=[
            'goods_id'=>$id,
            'user_id'=>$session['user_id']
        ];
        $count=DB::table('shop_shou')->where($data)->count();
        if ($count>0){
            $shou=DB::table('shop_shou')->where($data)->get();
            $res=DB::table('shop_shou')->where('s_id',$shou[0]->s_id)->delete();
            if ($res){
                return ['font'=>'取消收藏成功','code'=>6];die;
            }else{
                return ['font'=>'取消收藏失败','code'=>5];die;
            }
        }else{
            $res=DB::table('shop_shou')->insert($data);
            if ($res){
                return ['font'=>'收藏成功','code'=>1];die;
            }else{
                return ['font'=>'收藏失败','code'=>2];die;
            }
        }

    }
    //结算页面
    public function pay()
    {
        $session=request()->session()->get('users');
        if ($session==null) {
            return redirect('/login/login')->with('status', '请登录');
        }
        $id=request()->cart_id;
        $cart_id=explode(',',$id);
        $data=DB::table('shop_cart')
            ->whereIn('cart_id',$cart_id)
            ->join('shop_goods','shop_cart.goods_id','=','shop_goods.goods_id')
            ->get();
        $price=0;
        foreach ($data as $k => $v){
            $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
            $data[$k]->zongjia=$data[$k]->goods_selfprice*$data[$k]->cart_shuliang;
            $price+=$data[$k]->zongjia;
        }
        $address=DB::table('shop_address')
            ->where('user_id',$session['user_id'])
            ->orderBy('is_default')
            ->get();
        return view('index/pay',compact('data','address','price'));
    }
    //提交成功页面
    public function success()
    {
        $price = \request()->price;
        $config = config('alipay');
        $path = base_path();
        include_once $path."/app/libs/alipay/pagepay/service/AlipayTradeService.php";
//        echo 11;exit;
        include_once $path."/app/libs/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php";
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = '20190403'.rand(100,999);

        //订单名称，必填
        $subject = '美食';

        //付款金额，必填
        $total_amount = $price;

        //商品描述，可空
        $body = '2013';

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        //输出表单
        return (dump($response));
    }
}
