<?php
// +----------------------------------------------------------------------
// | Project: fun.global.php
// +----------------------------------------------------------------------
// | Creation: 2023/8/29 21:53
// +----------------------------------------------------------------------
// | Filename: Find.php
// +----------------------------------------------------------------------
// | Explain: 用户账号找回
// +----------------------------------------------------------------------
global $_QET, $conf, $accredit, $cdnserver;
if ($conf['AccountRetrieval'] != 1) {
    show_msg('非常抱歉', '当前站点未开启账号自助找回功能，如果需要找回账号，可联系平台客服', 4);
}
//检测是否登录
if (isset($_COOKIE['THEKEY'])) {

    $url = false;
    if (isset($_QET['gid']) && $_QET['gid'] >= 1) {
        $url = href(2) . ROOT_DIR . '?mod=route&p=Goods&gid=' . $_QET['gid'];
    } else if (isset($_QET['r']) && $_QET['r'] === 'user') {
        $url = href(2) . ROOT_DIR . '?mod=route&p=User';
    }
    show_msg('温馨提示', '您已经登录了，无需找回账号！', 1, $url);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $conf['sitename'] ?> - 账号找回</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/layui/css/layui.css"/>
    <link rel="shortcut icon" href="<?= ROOT_DIR_S ?>/assets/favicon.ico" type="image/x-icon"/>
</head>
<style>
    .demo-reg-container {
        max-width: 290px;
        margin: 21px auto 0
    }

    .demo-reg-other .layui-icon {
        position: relative;
        display: inline-block;
        margin: 0 2px;
        top: 2px;
        font-size: 26px
    }

    .geetest_footer button {
        min-width: auto;
        text-align: center
    }

    .geetest_footer_left {
        left: 43% !important
    }

    .geetest_feedback, .geetest_footer_right {
        display: none !important
    }
</style>
<body>
<div id="App">
    <div style="padding: 1em">
        <div class="layui-tab layui-tab-brief">
            <ul class="layui-tab-title" style="text-align: center;">
                <li :class="Type==1?'layui-this':''" @click="QQCodeGet(2)">使用QQ找回</li>
                <?php if ($conf['sms_switch_user'] == 1) { ?>
                    <li :class="Type==2?'layui-this':''" @click="Type=2">使用手机号找回</li>
                <?php } ?>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item " :class="Type==1?'layui-show':''" style="text-align: center;">
                    <div v-if="QQData.state">
                        <div style="font-size: 1.1em;font-weight:400;margin: 1em 2em 0.5em 2em;color: #16b968">
                            {{QQData.message}}
                        </div>
                        <div>
                            <img :src="QQData.image" alt="QQ二维码" @click="QQCodeGet(2)"
                                 style="width:80%;max-width: 200px;max-height: 200px;box-shadow: 3px 3px 12px #ccc;">
                        </div>
                    </div>
                    <div v-else>
                        <div style="font-size: 1.2em;font-weight:400;margin: 1em 2em 0.5em 2em;color: #fd4627">
                            {{QQData.message}}
                        </div>
                        <div>
                            <img src="<?= ROOT_DIR_S ?>/assets/img/404.png" alt="载入中"
                                 @click="QQCodeGet(2)"
                                 style="width:80%;max-width: 200px;max-height: 200px;box-shadow: 3px 3px 12px #ccc;">
                        </div>
                    </div>
                    <button class="layui-btn layui-btn-normal" style="margin-top: 1em;" @click="QQCodeGet(2)">
                        获取新的二维码
                    </button>
                </div>
                <?php if ($conf['sms_switch_user'] == 1) { ?>
                    <div class="layui-tab-item" :class="Type==2?'layui-show':''">
                        <div class="layui-form">
                            <div class="demo-reg-container">
                                <div class="layui-form-item">
                                    <div class="layui-row">
                                        <div class="layui-col-xs7">
                                            <div class="layui-input-wrap">
                                                <div class="layui-input-prefix">
                                                    <i class="layui-icon layui-icon-cellphone"></i>
                                                </div>
                                                <input type="text" v-model="cellphone" name="cellphone" value=""
                                                       lay-verify="required|phone"
                                                       placeholder="手机号" lay-reqtext="请填写手机号"
                                                       autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-col-xs5">
                                            <div style="margin-left: 11px;">
                                                <button type="button" v-if="CaptchaType!=8"
                                                        class="layui-btn layui-btn-fluid layui-btn-primary"
                                                        @click="SendVerificationCode()">
                                                    获取验证码
                                                </button>
                                                <button type="button" v-else
                                                        id=""
                                                        class="layui-btn layui-btn-fluid layui-btn-primary"
                                                        @click="CaptchaGet('AccountRetrieval')">
                                                    获取验证码
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
                                        <input type="text" v-model="vercode" name="vercode" value=""
                                               lay-verify="required"
                                               placeholder="验证码" lay-reqtext="请填写验证码" autocomplete="off"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <button class="layui-btn layui-btn-fluid" @click="CodeLogin()" lay-submit>找回账号
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
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
    const CaptchaType = <?=(int)$conf['CaptchaType']?>;
    const GtCaptchaId = '<?=$conf['GtCaptchaId']?>';
    const REQUEST = '<?=$_QET['r'] ?? ''?>';
    const GID = '<?=(int)$_QET['gid'] ?? 0?>';
</script>
<script src="<?php echo $cdnserver; ?>assets/user/login/find.js?t=<?= $accredit['versions'] ?>"></script>
</body>
</html>
