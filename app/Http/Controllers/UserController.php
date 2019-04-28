<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class UserController extends Controller
{
    //我的
    public function user()
    {
        return view('user/user');
    }
    //待支付....
    public function order()
    {
        return view('user/order');
    }
    //收藏
    public function shoucang()
    {
        $session=request()->session()->get('users');
        if ($session==null){
            return redirect('/login/login')->with('status','请登录');
        }else{
            $data=DB::table('shop_shou')
                ->join('shop_goods','shop_shou.goods_id','=','shop_goods.goods_id')
                ->where('shop_shou.user_id',$session['user_id'])
                ->get();
            foreach ($data as $k => $v){
                $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
                $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
                $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
            }
            return view('user/shoucang',['data'=>$data]);
        }

    }
    //取消收藏
    public function delshou()
    {
        $id=request()->s_id;
        $res=DB::table('shop_shou')->where('s_id',$id)->delete();
        if ($res){
            return redirect('/user/shoucang')->with('status','取消收藏成功');
        }else{
            return redirect('/user/shoucang')->with('status','取消收藏失败');
        }
    }
    //浏览记录
    public function liu()
    {
        $session=request()->session()->get('users');
        if ($session==null){
            return redirect('/login/login')->with('status','请登录');
        }else{
            $data=DB::table('shop_liu')
                ->join('shop_goods','shop_liu.goods_id','=','shop_goods.goods_id')
                ->where('shop_liu.user_id',$session['user_id'])
                ->orderBy('shop_liu.create_time','desc')
                ->limit(10)
                ->get();
            foreach ($data as $k => $v){
                $data[$k]->goods_big_pic = ltrim($v->goods_big_pic, '|');
                $data[$k]->goods_mid_pic = ltrim($v->goods_mid_pic, '|');
                $data[$k]->goods_small_pic = ltrim($v->goods_small_pic, '|');
            }
            return view('user/liu',['data'=>$data]);
        }
    }
    //取消浏览记录
    public function delliu()
    {
        $id=request()->liu_id;
        $res=DB::table('shop_liu')->where('liu_id',$id)->delete();
        if ($res){
            return redirect('/user/liu')->with('status','删除浏览记录成功');
        }else{
            return redirect('/user/liu')->with('status','删除浏览记录失败');
        }
    }
    //全部清除浏览记录
    public function quanliu()
    {
        $res=DB::table('shop_liu')->delete();
        if ($res){
            return redirect('/user/liu')->with('status','清空浏览记录成功');
        }else{
            return redirect('/user/liu')->with('status','清空浏览记录失败');
        }
    }
    //收货地址
    public function Aaaddress()
    {
        $session=request()->session()->get('users');
        if ($session==null){
            return redirect('/login/login')->with('status','请登录');
        }else {
            $data=DB::table('shop_address')
                ->where('user_id',$session['user_id'])
                ->get();
            return view('user/Aaaddress',compact('data'));
        }
    }
    //三级联动
    public function province($pid)
    {
        $data=DB::table('shop_area')->where('pid',$pid)->get();
        if (empty($data)){
            return false;
        }else{
            return $data;
        }
    }
    //获取市县
    public function getcity()
    {
        $id=request()->id;
        if (empty($id)){
            die;
        }
        $data=DB::table('shop_area')->where('pid',$id)->get();
        return ['data'=>$data,'code'=>1];
    }
    //添加收货地址
    public function address()
    {
        $data=$this->province(0);
        return view('user/address',compact('data'));
    }
    //添加收货地址处理页面
    public function addressdo()
    {
        $session=request()->session()->get('users');
        $data=request()->input();
        $data['user_id']=$session['user_id'];
        $res=DB::table('shop_address')->insert($data);
        if ($res){
            if ($res){
                return redirect('/user/Aaaddress')->with('status','添加成功');
            }else{
                return redirect('/user/Aaaddress')->with('status','添加失败');
            }
        }
    }
    //退出
    public function tui()
    {
       request()->session()->forget('users');
       return redirect('/login/login')->with('status','退出成功');
    }
}
