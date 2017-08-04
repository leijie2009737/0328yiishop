<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>用户注册</title>
    <link rel="stylesheet" href="<?=Yii::getAlias('@web')?>/style/base.css" type="text/css">
    <link rel="stylesheet" href="<?=Yii::getAlias('@web')?>/style/global.css" type="text/css">
    <link rel="stylesheet" href="<?=Yii::getAlias('@web')?>/style/header.css" type="text/css">
    <link rel="stylesheet" href="<?=Yii::getAlias('@web')?>/style/login.css" type="text/css">
    <link rel="stylesheet" href="<?=Yii::getAlias('@web')?>/style/footer.css" type="text/css">
</head>
<body>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li>您好，欢迎来到京西！[<a href="login.html">登录</a>] [<a href="register.html">免费注册</a>] </li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="index.html"><img src="<?=Yii::getAlias('@web')?>/images/logo.png" alt="京西商城"></a></h2>
    </div>
</div>
<!-- 页面头部 end -->

<!-- 登录主体部分start -->
<div class="login w990 bc mt10 regist">
    <div class="login_hd">
        <h2>用户注册</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <!--<form action="" method="post" id="login_form">-->
            <?php $form = \yii\widgets\ActiveForm::begin(['id'=>'login_form'])?>
            <ul>
                <li id="li_username">
                    <label for="">用户名：</label>
                    <input type="text" class="txt" name="Member[username]" />
                    <p style="color: red"></p>
                </li>
                <li id="li_password">
                    <label for="">密码：</label>
                    <input type="password" class="txt" name="Member[password]" />
                    <p style="color: red"></p>
                </li>
                <li id="li_re_password">
                    <label for="">确认密码：</label>
                    <input type="password" class="txt" name="Member[repassword]" />
                    <p style="color: red"></p>
                </li>
                <li id="li_email">
                    <label for="">邮箱：</label>
                    <input type="text" class="txt" name="Member[email]" />
                    <p style="color: red"></p>
                </li>
                <li id="li_tel">
                    <label for="">手机号码：</label>
                    <input type="text" class="txt" value="" name="Member[tel]" id="tel" placeholder=""/>
                    <p style="color: red"></p>
                </li>
                <li id="li_telCode">
                    <label for="">验证码：</label>
                    <input type="text" class="txt" value="" placeholder="请输入短信验证码" name="Member[telCode]" disabled="disabled" id="captcha"/> <input type="button" onclick="bindPhoneNum(this)" id="get_captcha" value="获取验证码" style="height: 25px;padding:3px 8px"/>
                    <p style="color: red"></p>
                </li>
                <li class="checkcode" id="li_code">
                    <?=$form->field($model,'code')->widget(\yii\captcha\Captcha::className(),['captchaAction'=>'member/captcha'])?>
                    <p style="color: red"></p>
                </li>

                <li>
                    <label for="">&nbsp;</label>
                    <input type="checkbox" class="chb" checked="checked" /> 我已阅读并同意《用户注册协议》
                    <p></p>
                </li>
                <li>
                    <label for="">&nbsp;</label>
                    <!--<input type="submit" value="" class="login_btn" />-->
                    <input type="button" value="" class="login_btn" />
                </li>
            </ul>
            <!--</form>-->
            <?php \yii\widgets\ActiveForm::end()?>


        </div>

        <div class="mobile fl">
            <h3>手机快速注册</h3>
            <p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
            <p><strong>1069099988</strong></p>
        </div>

    </div>
</div>
<!-- 登录主体部分end -->

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><img src="<?=Yii::getAlias('@web')?>/images/xin.png" alt="" /></a>
        <a href=""><img src="<?=Yii::getAlias('@web')?>/images/kexin.jpg" alt="" /></a>
        <a href=""><img src="<?=Yii::getAlias('@web')?>/images/police.jpg" alt="" /></a>
        <a href=""><img src="/images/beian.gif" alt="" /></a>
    </p>
</div>
<!-- 底部版权 end -->
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    function bindPhoneNum(){
        $(function (){
            var tels = $("#tel").val();
            var flag = false;
            var myreg = /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if(tels == ''){
                alert("手机号码不能为空！")
            }else if(tels.length !=11){
                alert("请输入有效的手机号码！")
            }else if(!myreg.test(tels)){
                alert("请输入有效的手机号码！")
            }else {
                flag = true;
                $('#captcha').prop('disabled',false);

                var time=60;
                var interval = setInterval(function(){
                    time--;
                    if(time<=0){
                        clearInterval(interval);
                        var html = '获取验证码';
                        $('#get_captcha').prop('disabled',false);
                    } else{
                        var html = time + ' 秒后再次获取';
                        $('#get_captcha').prop('disabled',true);
                    }

                    $('#get_captcha').val(html);
                },1000);
                //console.debug(tels);
                $.post('tel',{tels:tels},function (data) {
                    var json = JSON.parse(data);
                    //console.log(json);
                    if(json.status){
                        alert('短信发送成功');
                        //跳转到登录页
                        //window.location.href="/member/login";
                    }else{
                        //注册失败 显示错误信息
                        $(json.msg).each(function(i,errors){
                            $.each(errors,function(name,error){
                                $("#li_"+name+" p").text(error.join(","));
                            });

                        });
                    }
                })
            }
        })
    }


    //AJAX提交表单
    $(".login_btn").click(function(){
        //清除错误信息
        $("#login_form p").text("");
        $.post('/member/ajax-regist',$("#login_form").serialize(),function(data){
            //console.log(data);
            var json = JSON.parse(data);
            //console.log(json);
            if(json.status){
                alert('注册成功');
                //跳转到登录页
                window.location.href="/member/login";
            }else{
                //注册失败 显示错误信息
                $(json.msg).each(function(i,errors){
                    //console.log(errors);

                    $.each(errors,function(name,error){
                        $("#li_"+name+" p").text(error.join(","));
                    });

                });
            }
        });
    });


    //更换验证码
    $("#member-code-image").click(function(){
        $.getJSON('/member/captcha?refresh=1',function(json){
            $("#member-code-image").attr('src',json.url);
            //console.log(json.url);
        });
    });

    //    手机验证码




</script>
</body>
</html>