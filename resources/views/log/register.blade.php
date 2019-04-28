@extends('layouts/app')
@include('public/head')
@include('public/bottom')
<body>
<div class="maincont">
    <header>
        <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
        <div class="head-mid">
            <h1>会员注册</h1>
        </div>
    </header>
    <div class="head-top">
        <img src="/index/images/head.jpg" />
    </div><!--head-top/-->
    <form action="login/login" method="get" class="reg-login layui-form" onsubmit="return false">
        <h5>已经有账号了？点此<a class="orange" href="/log/log">登陆</a></h5>
        <div class="lrBox">
            <div class="lrList"><input type="text" placeholder="输入手机号码或者邮箱号" lay-vertify="required" name="email" id="email"/></div>
            <div class="lrList2"><input type="text" placeholder="输入短信验证码" lay-verify="required" name="duan"/> <button id="code">获取验证码</button></div>
            <div class="lrList"><input type="password" placeholder="设置新密码（6-18位数字或字母）" name="pwd" lay-verify="required" /></div>
            <div class="lrList"><input type="password" placeholder="再次输入密码" lay-verify="required" name="apwd" /></div>
        </div><!--lrBox/-->
        <div class="lrSub">
            <input type="submit" lay-submit lay-filter="*" value="立即注册" />
        </div><!--reg-login/-->
    </form>
    <div class="height1"></div>
</div><!--maincont-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/index/js/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/index/s/bootstrap.min.js"></script>
<script src="/index/js/style.js"></script>
</body>
</html>
<script>
    layui.use(['layer','form'],function(){
        var layer = layui.layer;
        var form = layui.form;

        //点击获取验证码
        $("#code").click(function(){
            var reg=/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/;
            var _email = $("#email").val();
            if(_email==''){
                layer.msg('请输入您的邮箱',{icon:5});
                return false;
            }else if(!reg.test(_email)){
                layer.msg('请输入正确格式的邮箱',{icon:5});
                return false;
            }else{
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post(
                    'sendEmail',
                    {_email:_email},
                    function(res){
                        if(res == 'ycz'){
                            layer.msg('邮件已存在',{icon:5});
                        }else if(res==1){
                            layer.msg('邮件发送成功',{icon:6});
                        }else{
                            layer.msg('邮件发送失败',{icon:5});
                        }
                    }
                );
                return false;
            }
        });

        //点击 注册
        form.on('submit(*)',function(data){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post(
                'registerdo',
                data.field,
                function(res){
                    layer.msg(res.font,{icon:res.code},function(){
                        if(res.code==6){
                            location.href="/log/log";
                        }
                    });
                }
            );
        });

    });
</script>
