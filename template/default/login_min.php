<?php
//登录页面简化版
global $_QET, $conf, $accredit, $cdnserver;
//检测是否登录
if (isset($_COOKIE['THEKEY'])) {
    show_msg('温馨提示', '您当前已经处于登录状态！', 1);
}
?>
<!DOCTYPE html>
<html lang="ch">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $conf['sitename'] . ($conf['title'] == '' ? '' : ' - ' . $conf['title']) ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/layui/css/layui.css"/>
    <link rel="shortcut icon" href="<?= ROOT_DIR_S ?>/assets/favicon.ico" type="image/x-icon"/>
</head>
<style>
    #AppHide {
        text-align: center;
        padding-top: 20vh;
        moz-user-select: -moz-none;
        -moz-user-select: none;
        -o-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
        min-height: 100vh;
        font-size: 18px;
    }

    #AppHide img {
        width: 28px;
        margin: auto auto 2em;
    }

    .demo-login-container {
        width: 94%;
        max-width: 320px;
        margin: 21px auto 0;
    }

    .demo-login-other .layui-icon {
        position: relative;
        display: inline-block;
        margin: 0 2px;
        top: 2px;
        font-size: 26px;
    }

    .geetest_footer button {
        min-width: auto;
        text-align: center;
    }

    .geetest_footer_left {
        left: 43% !important;
    }

    .geetest_feedback, .geetest_footer_right {
        display: none !important;
    }

    .Privacy {
        width: 96%;
        margin: auto;
    }

    .Privacy a {
        color: #ff683a;
        text-decoration: none;
    }
</style>
<body>
<div id="AppHide" style="background-color: #fff;">
    <img src="/assets/img/loading.gif" alt="load"/>
    <div>正在载入中,请等待~</div>
</div>
<div id="App" style="display: none;">
    <div v-if="UserData.login==1">
        <div class="demo-login-container" style="text-align:center;font-size: 1.2em">
            <span v-if="StateType==1">
                账号登录
            </span>
            <span v-if="StateType==2">
                账号注册
            </span>
            <span v-if="StateType==3">
                手机登录
            </span>
        </div>
        <div class="layui-form">
            <div class="demo-login-container" v-if="StateType==1">
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-username"></i>
                        </div>
                        <input type="text" v-model="user" value="" lay-verify="required" placeholder="用户名"
                               lay-reqtext="请填写用户名" autocomplete="off" class="layui-input" lay-affix="clear">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-password"></i>
                        </div>
                        <input type="password" v-model="pass" value="" lay-verify="required" placeholder="密   码"
                               lay-reqtext="请填写密码" autocomplete="off" class="layui-input" lay-affix="eye">
                    </div>
                </div>
                <div class="layui-form-item">
                    <a @click="FindPassword()" v-if="UserData.AccountRetrieval==1">忘记密码？</a>
                    <a @click="StateType=2" style="float: right">注册帐号</a>
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" v-if="UserData.CaptchaType!=8" lay-submit
                            @click="loginRequest()">登录
                    </button>
                    <button class="layui-btn layui-btn-fluid" id="Login1" v-else lay-submit
                            @click="CaptchaGet('Login1')">登录
                    </button>
                </div>
            </div>
            <div class="demo-login-container" v-if="StateType==2">
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-username"></i>
                        </div>
                        <input type="text" value="" v-model="user" lay-verify="required" placeholder="账号"
                               autocomplete="off" class="layui-input" lay-affix="clear">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-password"></i>
                        </div>
                        <input type="password" v-model="pass" value="" lay-verify="required" placeholder="密码"
                               autocomplete="off" class="layui-input" id="reg-password" lay-affix="eye">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-login-qq"></i>
                        </div>
                        <input type="text" value="" v-model="qq" lay-verify="required" placeholder="QQ"
                               autocomplete="off" class="layui-input" lay-affix="clear">
                    </div>
                </div>
                <div class="layui-form-item" v-if="UserData.RegisterPhone==1&&UserData.phone==1">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <div class="layui-input-wrap">
                                <div class="layui-input-prefix">
                                    <i class="layui-icon layui-icon-cellphone"></i>
                                </div>
                                <input type="text" v-model="phone" value=""
                                       placeholder="手机号" lay-reqtext="请填写手机号" autocomplete="off"
                                       class="layui-input" lay-affix="clear">
                            </div>
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 11px;">
                                <button v-if="UserData.CaptchaType!=8" type="button"
                                        class="layui-btn layui-btn-fluid layui-btn-primary"
                                        lay-on="reg-get-vercode" @click="SendRegisterCode">获取验证码
                                </button>
                                <button id="RegisterSms" v-else type="button"
                                        class="layui-btn layui-btn-fluid layui-btn-primary"
                                        lay-on="reg-get-vercode" @click="CaptchaGet('RegisterSms')">获取验证码
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <a @click="FindPassword()" v-if="UserData.AccountRetrieval==1">忘记密码？</a>
                    <a @click="StateType=1" style="float: right">账号登录</a>
                </div>
                <div class="layui-form-item">
                    <button v-if="UserData.CaptchaType!=8" @click="loginRegister()" class="layui-btn layui-btn-fluid"
                            lay-submit
                            lay-filter="demo-reg">注册
                    </button>
                    <button id="Register" v-else class="layui-btn layui-btn-fluid" @click="CaptchaGet('Register')"
                            lay-submit
                            lay-filter="demo-reg">注册
                    </button>
                </div>
            </div>
            <div class="demo-login-container" v-if="StateType==3">
                <div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <div class="layui-input-wrap">
                                <div class="layui-input-prefix">
                                    <i class="layui-icon layui-icon-cellphone"></i>
                                </div>
                                <input type="text" v-model="phone" value=""
                                       placeholder="手机号" lay-reqtext="请填写手机号" lay-verify="required"
                                       autocomplete="off"
                                       class="layui-input" lay-affix="clear">
                            </div>
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 11px;">
                                <button v-if="UserData.CaptchaType!=8" type="button"
                                        class="layui-btn layui-btn-fluid layui-btn-primary"
                                        lay-on="reg-get-vercode" @click="SendVerificationCode">获取验证码
                                </button>
                                <button id="Login2" v-else type="button"
                                        class="layui-btn layui-btn-fluid layui-btn-primary"
                                        lay-on="reg-get-vercode" @click="CaptchaGet('Login2')">获取验证码
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-vercode"></i>
                        </div>
                        <input type="text" v-model="code" value="" lay-verify="required" placeholder="验证码"
                               lay-reqtext="请填写验证码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <a @click="FindPassword()" v-if="UserData.AccountRetrieval==1">忘记密码？</a>
                    <a @click="StateType=1" style="float: right">账号登录</a>
                </div>
                <div class="layui-form-item">
                    <button @click="CodeLogin()" class="layui-btn layui-btn-fluid"
                            lay-submit
                            lay-filter="demo-reg">登录 or 注册
                    </button>
                </div>
            </div>

            <div class="demo-login-container">
                <div v-if="UserData.ProtocolSwitch" class="Privacy">
                    <input v-model="PrivacyState" style="display:inline-block;" type="checkbox">
                    已阅读并同意以下协议
                    <a v-if="UserData.UserAgreement!=''"
                       style="color: #ff683a"
                       :href="UserData.UserAgreement"
                       target="_blank"
                       title="用户协议">《用户协议》</a>
                    <a v-if="UserData.PrivacyPolicy!=''"
                       style="color: #ff683a"
                       :href="UserData.PrivacyPolicy" target="_blank"
                       title="隐私政策">《隐私政策》</a>
                    <a v-if="UserData.LegalNotice!=''"
                       style="color: #ff683a"
                       :href="UserData.LegalNotice" target="_blank"
                       title="法律声明">《法律声明》</a><span v-if="StateType==3">未注册手机号将自动为您创建账号！</span>
                    <hr>
                </div>
                <fieldset v-if="UserData.qqtype||UserData.phone!=-1" class="layui-elem-field"
                          style="text-align: center;">
                    <legend style="font-size:110%;margin-left:0 !important;padding:0 !important;">快捷登录</legend>
                    <div class="layui-field-box">
                        <a title="QQ快捷登录"
                           v-if="UserData.qqtype"
                           :href="UserData.qqlogin" target="_blank">
                            <i class="layui-icon layui-icon-login-qq" style="color: #3492ed;font-size: 24px;"></i>
                        </a>
                        <a title="手机号快捷登录"
                           v-if="StateType!=3&&UserData.phone!=-1"
                           @click="StateType=3"
                           style="margin-left: 4px;">
                            <i class="layui-icon layui-icon-cellphone" style="color: #fd4627;font-size: 24px;"></i>
                        </a>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    <div v-if="UserData.login!=1&&UserData.phone==1">
        <div class="demo-login-container" style="text-align:center;font-size: 1.2em">
            登录 or 注册
        </div>
        <div class="demo-login-container">
            <div class="layui-form-item">
                <div class="layui-row">
                    <div class="layui-col-xs7">
                        <div class="layui-input-wrap">
                            <div class="layui-input-prefix">
                                <i class="layui-icon layui-icon-cellphone"></i>
                            </div>
                            <input type="text" v-model="phone" value=""
                                   placeholder="手机号" lay-reqtext="请填写手机号" lay-verify="required"
                                   autocomplete="off"
                                   class="layui-input" lay-affix="clear">
                        </div>
                    </div>
                    <div class="layui-col-xs5">
                        <div style="margin-left: 11px;">
                            <button v-if="UserData.CaptchaType!=8" type="button"
                                    class="layui-btn layui-btn-fluid layui-btn-primary"
                                    lay-on="reg-get-vercode" @click="SendVerificationCode">获取验证码
                            </button>
                            <button id="Login2" v-else type="button"
                                    class="layui-btn layui-btn-fluid layui-btn-primary"
                                    lay-on="reg-get-vercode" @click="CaptchaGet('Login2')">获取验证码
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-wrap">
                    <div class="layui-input-prefix">
                        <i class="layui-icon layui-icon-vercode"></i>
                    </div>
                    <input type="text" v-model="code" value="" lay-verify="required" placeholder="验证码"
                           lay-reqtext="请填写验证码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item" v-if="UserData.AccountRetrieval==1">
                <a @click="FindPassword()">忘记密码？</a>
            </div>
            <div class="layui-form-item">
                <button @click="CodeLogin()" class="layui-btn layui-btn-fluid"
                        lay-submit
                        lay-filter="demo-reg">登录 or 注册
                </button>
            </div>
        </div>

        <div class="demo-login-container">
            <div v-if="UserData.ProtocolSwitch" class="Privacy">
                <input v-model="PrivacyState" style="display:inline-block;" type="checkbox">
                已阅读并同意以下协议
                <a v-if="UserData.UserAgreement!=''"
                   style="color: #ff683a"
                   :href="UserData.UserAgreement"
                   target="_blank"
                   title="用户协议">《用户协议》</a>
                <a v-if="UserData.PrivacyPolicy!=''"
                   style="color: #ff683a"
                   :href="UserData.PrivacyPolicy" target="_blank"
                   title="隐私政策">《隐私政策》</a>
                <a v-if="UserData.LegalNotice!=''"
                   style="color: #ff683a"
                   :href="UserData.LegalNotice" target="_blank"
                   title="法律声明">《法律声明》</a>未注册手机号将自动为您创建账号!
                <hr>
            </div>
            <fieldset v-if="UserData.qqtype||UserData.phone!=-1" class="layui-elem-field"
                      style="text-align: center;">
                <legend style="font-size:110%;margin-left:0 !important;padding:0 !important;">快捷登录</legend>
                <div class="layui-field-box">
                    <a title="QQ快捷登录"
                       v-if="UserData.qqtype"
                       :href="UserData.qqlogin" target="_blank">
                        <i class="layui-icon layui-icon-login-qq" style="color: #3492ed;font-size: 24px;"></i>
                    </a>
                </div>
            </fieldset>
        </div>

    </div>
    <div v-if="UserData.login!=1&&UserData.phone!=1&&UserData.qqtype">
        <div class="demo-login-container" style="text-align:center;font-size: 1.2em">
            QQ快捷登录
        </div>
        <div style="text-align: center;margin-top:2em;">
            <a title="QQ快捷登录"
               v-if="UserData.qqtype"
               :href="UserData.qqlogin" target="_blank">
                <i class="layui-icon layui-icon-login-qq" style="color: #3492ed;font-size: 80px;"></i>
            </a>
            <div class="layui-form-item" style="margin-top:2em;" v-if="UserData.AccountRetrieval==1">
                <a @click="FindPassword()">忘记密码？</a>
            </div>
        </div>
    </div>

    <div v-if="UserData.login!=1&&UserData.phone!=1&&!UserData.qqtype">
        <div class="demo-login-container" style="text-align:center;font-size: 1.2em">
            当前站点无可用登录方式
            <div class="layui-form-item" style="margin-top:2em;" v-if="UserData.AccountRetrieval==1">
                <a @click="FindPassword()" style="color: red">点击找回账号</a>
            </div>
        </div>
    </div>

</div>
</body>
<script src="<?php echo $cdnserver; ?>assets/layui/layui.all.js"></script>
<script src="<?php echo $cdnserver; ?>assets/js/jquery-3.4.1.min.js"></script>
<script src="<?php echo $cdnserver; ?>assets/js/vue3.js"></script>
<script src="<?php echo $cdnserver; ?>assets/js/sweetalert.min.js"></script>
<script>
    const HREF = '<?=href(2)?>';
    const ROOT_DIR_S = '<?=ROOT_DIR_S?>';
    const GID = '<?=(int)$_QET['gid'] ?? 0?>';
</script>
<script src="<?php echo $cdnserver; ?>assets/user/login/main.js?t=<?= $accredit['versions'] ?>"></script>
</html>

