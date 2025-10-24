<?php
// +----------------------------------------------------------------------
// | Project: fun.global.php
// +----------------------------------------------------------------------
// | Creation: 2023/7/01 21:39
// +----------------------------------------------------------------------
// | Filename: login.php
// +----------------------------------------------------------------------
// | Explain: 强制登录
// +----------------------------------------------------------------------
global $_QET, $conf, $accredit, $cdnserver;
//检测是否登录
if (isset($_COOKIE['THEKEY'])) {
    show_msg('温馨提示', '您当前已经处于登录状态！', 1, href(2) . ROOT_DIR_S);
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
    <link rel="stylesheet" href="<?= ROOT_DIR_S ?>/assets/user/login/style.css?t=<?= $accredit['versions'] ?>">
    <link rel="shortcut icon" href="<?= ROOT_DIR_S ?>/assets/favicon.ico" type="image/x-icon"/>
</head>
<body>
<div id="AppHide">
    <img src="/assets/img/loading.gif" alt="load"/>
    <div>正在载入中,请等待~</div>
</div>
<div id="App" style="display: none;" class="container">
    <div class="form-warp">
        <form class="sign-in-form" v-if="UserData.login==1">
            <h2 class="form-title">{{ LoginType==1?'账号登录':'手机登录' }}</h2>
            <div class="forms" v-if="LoginType==1">
                <input placeholder="登录账号" v-model="user"/>
                <input type="password" placeholder="登录密码" v-model="pass"/>
                <div class="forms" v-if="UserData.CaptchaType!=8">
                    <div class="submit-btn" @click="loginRequest()">立即登录</div>
                </div>
                <div v-else id="Login1">
                    <div @click="CaptchaGet('Login1')" class="submit-btn">立即登录</div>
                </div>
                <div v-if="UserData.ProtocolSwitch" class="Privacy">
                    <input v-model="PrivacyState" type="checkbox"> 已阅读并同意以下协议<a
                            v-if="UserData.UserAgreement!=''"
                            :href="UserData.UserAgreement"
                            target="_blank"
                            title="用户协议">《用户协议》</a><a
                            v-if="UserData.PrivacyPolicy!=''"
                            :href="UserData.PrivacyPolicy" target="_blank"
                            title="隐私政策">《隐私政策》</a><a
                            v-if="UserData.LegalNotice!=''" :href="UserData.LegalNotice" target="_blank"
                            title="法律声明">《法律声明》</a>！
                </div>
            </div>

            <div v-if="LoginType==2">
                <div class="forms" v-if="phoneType==1">
                    <input placeholder="您的手机号" v-model="phone"/>
                    <div class="forms" v-if="UserData.CaptchaType!=8">
                        <div class="submit-btn" @click="SendVerificationCode()">发送验证码</div>
                    </div>
                    <div v-else id="Login2">
                        <div class="submit-btn" @click="CaptchaGet('Login2')">发送短信</div>
                    </div>
                </div>
                <div v-else class="forms">
                    <input placeholder="您的手机号" :value="phone" disabled/>
                    <input type="text" placeholder="收到的短信验证码" v-model="code"/>
                    <div class="submit-btn" @click="CodeLogin()">立即登录</div>
                </div>
                <div v-if="UserData.ProtocolSwitch" class="Privacy">
                    <br>
                    <input v-model="PrivacyState" type="checkbox"> 已阅读并同意以下协议<a
                            v-if="UserData.UserAgreement!=''" :href="UserData.UserAgreement"
                            target="_blank"
                            title="用户协议">《用户协议》</a><a v-if="UserData.PrivacyPolicy!=''"
                                                              :href="UserData.PrivacyPolicy"
                                                              target="_blank"
                                                              title="隐私政策">《隐私政策》</a><a
                            v-if="UserData.LegalNotice!=''" :href="UserData.LegalNotice" target="_blank"
                            title="法律声明">《法律声明》</a>，未注册手机号将自动为您创建账号！
                </div>
            </div>

            <div style="width: 100%;text-align: center;">
                <a class="layui-icon layui-icon-login-qq"
                   v-if="UserData.qqtype"
                   title="QQ快捷登录"
                   :href="UserData.qqlogin"
                   target="_blank"
                   style="font-size: 32px;color: rgb(106,111,231);cursor: pointer;"></a>
                <span class="layui-icon layui-icon-cellphone"
                      v-if="LoginType==1&&UserData.phone!=-1"
                      @click="LoginType=2"
                      title="手机号快捷登录"
                      style="font-size: 30px;color: rgb(253,160,39);cursor: pointer;margin-left: 5px;"></span>

                <span class="layui-icon layui-icon-username"
                      title="账号密码登录"
                      v-if="LoginType==2"
                      @click="LoginType=1"
                      style="font-size: 34px;color: rgba(246,33,33,0.65);cursor: pointer;margin-left: 5px;"></span>

                <span class="layui-icon layui-icon-password"
                      v-if="UserData.AccountRetrieval==1"
                      title="找回密码"
                      @click="FindPassword()"
                      style="font-size: 34px;color: rgb(94,106,255);cursor: pointer;margin-left: 8px;"></span>
            </div>
        </form>

        <form class="sign-up-form" v-if="UserData.login==1">
            <h2 class="form-title" id="RegisterTitle">账户注册</h2>
            <input placeholder="登录账号" v-model="user"/>
            <input type="password" v-model="pass" placeholder="登录密码"/>

            <input placeholder="绑定QQ,用于找回账号" v-model="qq"/>
            <input v-if="UserData.RegisterPhone==1&&UserData.phone==1" placeholder="请填写手机号"
                   v-model="phone"/>
            <div v-if="UserData.RegisterPhone==1&&UserData.phone==1">
                <input placeholder="请填写验证码"
                       v-model="code"
                       style="width: 180px;border-bottom-right-radius: 0;border-top-right-radius: 0;"
                />
                <a class="layui-btn layui-btn-radius layui-btns" @click="SendRegisterCode"
                   v-if="UserData.CaptchaType!=8">获取</a>
                <a class="layui-btn layui-btn-radius layui-btns" @click="CaptchaGet('RegisterSms')" v-else>获取</a>
            </div>
            <div class="forms" v-if="UserData.CaptchaType!=8">
                <div class="submit-btn" @click="loginRegister()">立即注册</div>
            </div>
            <div class="forms" v-else id="Register">
                <div class="submit-btn" @click="CaptchaGet('Register')">立即注册</div>
            </div>
            <div v-if="UserData.ProtocolSwitch" class="Privacy">
                <input v-model="PrivacyState" type="checkbox"> 已阅读并同意以下协议<a v-if="UserData.UserAgreement!=''"
                                                                                      :href="UserData.UserAgreement"
                                                                                      target="_blank"
                                                                                      title="用户协议">《用户协议》</a><a
                        v-if="UserData.PrivacyPolicy!=''"
                        :href="UserData.PrivacyPolicy" target="_blank"
                        title="隐私政策">《隐私政策》</a><a
                        v-if="UserData.LegalNotice!=''" :href="UserData.LegalNotice" target="_blank"
                        title="法律声明">《法律声明》</a>
            </div>
        </form>

        <form class="sign-in-form" v-if="UserData.login!=1&&UserData.phone==1">
            <h2 class="form-title">登录 or 注册</h2>
            <div class="forms" v-if="phoneType==1">
                <input placeholder="您的手机号" v-model="phone"/>
                <div class="forms" v-if="UserData.CaptchaType!=8">
                    <div class="submit-btn" @click="SendVerificationCode()">发送验证码</div>
                </div>
                <div v-else id="Login2">
                    <div class="submit-btn" @click="CaptchaGet('Login2')">发送短信</div>
                </div>
            </div>
            <div v-else class="forms">
                <input placeholder="您的手机号" :value="phone" disabled/>
                <input type="text" placeholder="收到的短信验证码" v-model="code"/>
                <div class="submit-btn" @click="CodeLogin()">立即登录</div>
            </div>
            <div v-if="UserData.ProtocolSwitch" class="Privacy">
                <br>
                <input v-model="PrivacyState" type="checkbox"> 已阅读并同意以下协议<a
                        v-if="UserData.UserAgreement!=''" :href="UserData.UserAgreement"
                        target="_blank"
                        title="用户协议">《用户协议》</a><a v-if="UserData.PrivacyPolicy!=''"
                                                          :href="UserData.PrivacyPolicy"
                                                          target="_blank"
                                                          title="隐私政策">《隐私政策》</a><a
                        v-if="UserData.LegalNotice!=''" :href="UserData.LegalNotice" target="_blank"
                        title="法律声明">《法律声明》</a>，未注册手机号将自动为您创建账号！
            </div>
            <div style="width: 100%;margin-top: 1em;text-align: center">
                <a class="layui-icon layui-icon-login-qq"
                   v-if="UserData.qqtype"
                   title="QQ快捷登录"
                   :href="UserData.qqlogin"
                   target="_blank"
                   style="font-size: 32px;color: rgb(106,111,231);cursor: pointer;"></a>

                <span class="layui-icon layui-icon-password"
                      v-if="UserData.AccountRetrieval==1"
                      title="找回密码"
                      @click="FindPassword()"
                      style="font-size: 34px;color: rgb(94,106,255);cursor: pointer;margin-left: 8px"></span>
            </div>
        </form>

        <form class="sign-in-form" v-if="UserData.login!=1&&UserData.phone!=1&&UserData.qqtype">
            <h2 class="form-title">登录 or 注册 <span class="layui-icon layui-icon-password"
                                                      v-if="UserData.AccountRetrieval==1"
                                                      title="找回密码"
                                                      @click="FindPassword()"
                                                      style="font-size: 24px;color: rgb(255,104,58);cursor: pointer;margin-left: 8px;"></span>
            </h2>
            <a class="layui-icon layui-icon-login-qq"
               title="QQ快捷登录"
               :href="UserData.qqlogin"
               target="_blank"
               style="font-size: 5em;color: #6d73fa;cursor: pointer;margin-top: 0.3em;"></a>


        </form>

        <form class="sign-in-form" v-if="UserData.login!=1&&UserData.phone!=1&&!UserData.qqtype">
            <span class="layui-icon layui-icon-password"
                  v-if="UserData.AccountRetrieval==1"
                  title="找回密码"
                  @click="FindPassword()"
                  style="font-size: 24px;color: rgb(255,104,58);cursor: pointer;margin-left: 8px;"></span>
            <h2 class="form-title">无可用登录方式</h2>
            <h4>请咨询站长</h4>
        </form>

    </div>
    <div class="desc-warp" id="buttos">
        <div class="desc-warp-item sign-up-desc">
            <div class="content" v-if="UserData.login==1">
                <button id="sign-up-btn" @click="Inform(2);StateType=2">注册</button>
            </div>
            <div class="content" v-if="UserData.login!=1&&UserData.phone==1">
                <button id="sign-in-btn" @click="StateType=3">手机登录</button>
            </div>
            <div class="content" v-if="UserData.login!=1&&UserData.phone!=1&&UserData.qqtype">
                <button id="sign-in-btn">快捷登录</button>
            </div>
            <div class="content" v-if="UserData.login!=1&&UserData.phone!=1&&!UserData.qqtype">
                <button id="sign-in-btn">无登录方式</button>
            </div>
            <img src="<?php echo $cdnserver; ?>assets/user/login/img/log.svg" alt="">
        </div>
        <div class="desc-warp-item sign-in-desc">
            <div class="content" v-if="UserData.login==1">
                <button id="sign-in-btn" @click="Inform(1);StateType=1">登录</button>
            </div>
            <img src="<?php echo $cdnserver; ?>assets/user/login/img/register.svg" alt="">
        </div>
    </div>
</div>
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
</body>
</html>