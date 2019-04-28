<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Mail;
class LogController extends Controller
{
    //注册
    public function register(){
        return view('log/register');
    }

    //发送验证码
    public function sendEmail(){
        $email = request()->_email;
        $count = DB::table('shop_users')->where('email',$email)->count();
        if($count>0){
            echo 'ycz';exit;
        }
        $rand=rand(100000,999999);
        Mail::send(
            'email.email',
            ['content'=>$rand],
            function($message)use($email,$rand){
                $message->subject('验证码');
                $message->to($email);
            }
        );
        $code=[
            'time'=>time(),
            'rand'=>$rand,
            'email'=>$email
        ];
//                    dd($res);exit;
        request()->session()->put('emailCode',$code);
        return 1;
    }
    //注册执行
    public function registerdo(){
        $data = request()->input();
//        dd($data);
        $session = request()->session()->get('emailCode');
        if($data['duan'] != $session['rand']){
            return ['font'=>'邮箱或验证码错误','code'=>5];exit;
        }
        if($data['email'] != $session['email']){
            return ['font'=>'邮箱或验证码错误','code'=>5];exit;
        }
        if($data['pwd'] != $data['apwd']){
            return ['font'=>'密码与确认密码不一致，请重新输入','code'=>5];exit;
        }
        if((time()-$session['time'])>120){
            return ['font'=>'验证码已失效','code'=>5];exit;
        }
        unset($data['apwd']);
        $data['pwd']=md5($data['pwd']);
        cache();
        $res = DB::table('shop_users')->insert($data);
        if($res){
            return ['font'=>'注册成功','code'=>6];exit;
        }else{
            return ['font'=>'注册失败','code'=>5];exit;
        }
    }


    //登录
    public function log(){
        return view('log/log');
    }

    //登录成功
    public function logdo(){
//        $data = request()->input();
//        $count = DB::table('shop_users')->where('email',$data['email'])->count();
//        $info = DB::table('shop_users')->where('email',$data['email'])->get();
//        if(!$count){
//            return ['font'=>'用户名或密码错误','code'=>5];exit;
//        }
//        if($info[0]->pwd != md5($data['pwd'])){
//            return ['font'=>'用户名或密码错误','code'=>5];exit;
//        }else{
//            $user=[
//                'user_id'=>$info[0]->id,
//                'time'=>time(),
//                'user_email'=>$data['email']
//            ];
//            request()->session()->get('user',$user);
//            cache(['email'=>$data['email']],60*24);
////            $email = cache('email');
////            dd($email);exit;
//            return ['font'=>'登录成功','code'=>6];exit;
//        }
        $data = request()->input();
        //dd($data);
        $email = $data['email'];
        $pwd = $data['pwd'];
        $userInfo = DB::table('shop_users')->where('email',$email)->first();
        //dd($userInfo->error_num);
        if (!empty($userInfo)){
            $now = time();
            $error_num = $userInfo->error_num;
            $last_error_time = $userInfo->last_error_time;
            $where = [
                'id' => $userInfo->id
            ];
            if ($userInfo->pwd == md5($pwd)) {
                // fail("密码正确");
                if ($error_num >= 5 && $now-$last_error_time < 3600) {
                    $second = 60-ceil(($now-$last_error_time)/60);
                    return ['code'=>5,'font'=>'号码已锁定，请'.$second.'分钟后重新登录'];
                }

                //错误次数清零，错误时间改为null
                $updateInfo = [
                    'error_num' => 0,
                    'last_error_time' => null
                ];
                DB::table('shop_users')->where($where)->update($updateInfo);

                // 存memcache
                cache(['email'=>$email],6*24);
                //dd(cache('u_email'));
                return ['code'=>6,'font'=>'登录成功'];

            } else {
                // fail("密码有误");
                if ($now-$last_error_time>3600) {
                    $updateInfo = [
                        'error_num' => 1,
                        'last_error_time' => $now
                    ];
                    $res = DB::table('shop_users')->where($where)->update($updateInfo);
                    if($res){
                        return ['code'=>5,'font'=>'账号或者密码有误，还有4次机会'];
                    }
                } else {
                    if ($error_num>=5) {
                        $second = 60-ceil(($now-$last_error_time)/60);
                        return ['code'=>5,'font'=>'密码锁定，请'.$second.'分钟后登录'];

                    } else {
                        $updateInfo = [
                            'error_num' => $error_num+1,
                            'last_error_time' => $now
                        ];
                        $res = DB::table('shop_users')->where($where)->update($updateInfo);
                        if ($res) {
                            $count = 5 - ($error_num+1);
                            return ['code'=>5,'font'=>'账号或者密码有误，还有'.$count.'次机会'];
                        }
                    }

                }

            }
        } else {
            return ['code'=>5,'font'=>'邮箱有误或密码'];
        }
    }

    //
    public function test(){
        $data = request()->session()->all();
        dd($data);
    }

    public function loglist(){
        $email=cache('email');
        return view('log/loglist',['email'=>$email]);
    }
}

