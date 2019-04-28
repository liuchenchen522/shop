<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Mail;
class LoginController extends Controller
{
    //注册
    public function reg()
    {
        return view('login/reg');
    }
    //注册执行
    public function regdo()
    {
        $data=request()->input();
        $session = request()->session()->get('emailCode');
        if ($data['duan'] != $session['rand']){
            return ['font'=>'邮箱或验证码错误','code'=>5];die;
        }
        if ($data['email'] != $session['email']){
            return ['font'=>'邮箱或验证码错误','code'=>5];die;
        }
        if ($data['pwd'] != $data['apwd']){
            return ['font'=>'两次密码不一致','code'=>5];die;
        }
        if ((time()-$session['time'])>300){
            return ['font'=>'验证码已失效','code'=>5];die;
        }
        unset($data['apwd']);
        $data['pwd'] = md5($data['pwd']);
        $res=DB::table('shop_users')->insert($data);
        if ($res){
            return ['font'=>'注册成功','code'=>6];die;
        }else{
            return ['font'=>'注册失败','code'=>5];die;
        }
    }

    //登录
    public function login()
    {
        return view('login/login');
    }

    //登录执行
    public function logindo()
    {
        $data=request()->input();
        $count=DB::table('shop_users')->where('email',$data['email'])->count();
        $info=DB::table('shop_users')->where('email',$data['email'])->get();
        if (!$count){
            return ['font'=>'用户名或密码错误','code'=>5];die;
        }
        if ($info[0]->pwd != md5($data['pwd'])){
            return ['font'=>'用户名或密码错误','code'=>5];die;
        }else{
            $users=[
                'user_id'=> $info[0]->id,
                'time'=>time(),
                'users_email'=>$data['email']
            ];
            request()->session()->put('users',$users);
            return ['font'=>'登录成功','code'=>6];die;
        }
    }
    //发送邮箱
    public function checkEmail()
    {
        $email=request()->_email;
        $count = DB::table('shop_users')->where('email',$email)->count();
        if($count>0){
            echo 'ycz';die;
        }
        $rand=rand(100000,999999);
        Mail::send(
            'email.email',
            ['content'=>$rand],
            function ($message)use($email,$rand){
                $message->subject('验证码');
                $res=$message->to($email);
                if ($res){
                    $code=[
                      'time'=>time(),
                      'rand'=>$rand,
                      'email'=>$email
                    ];
                    request()->session()->put('emailCode', $code);
                }else{
                    echo 2;
                }
            }
        );
    }

    public function test()
    {
        $data = request()->session()->all();
        dd($data);
    }


}
