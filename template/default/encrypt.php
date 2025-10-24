<?php if (!defined('IN_CRONLITE')) die;
global $cdnpublic, $conf;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>请输入密码访问本站</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="max-age=30">
    <meta name="renderer" content="origin">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/layui/css/layui.css"/>
    <link rel="stylesheet" href="<?= ROOT_DIR ?>assets/css/encrypt.css"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no,user-scalable=0"/>
    <link rel="shortcut icon" href="<?= ROOT_DIR ?>assets/favicon.ico" type="image/x-icon"/>
    <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic; ?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic; ?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<style>
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
</style>
<div class="docs init-docs" id="doc">
    <div class="acss-header">
        <div class="verify-form">
            <div class="clearfix">
                <div class="verify-input ac-close clearfix">
                    <dl class="pickpw clearfix">
                        <dt>请输入访问密码：</dt>
                        <dd class="clearfix input-area">
                            <input class="QKKaIE LxgeIt" type="text" placeholder="请输入访问密码" id="accessCode"/>
                            <div id="submitBtn">
                                <a class="submit-a g-button-blue-large" href="javascript:;" title="访问密码">
                                <span class="g-button-right">
                                    <span class="text submit-btn-text" style="width: auto;">验证密码</span>
                                </span>
                                </a>
                            </div>
                        </dd>
                    </dl>
                    <div style="margin-top:2em">
                        <?= $conf['PasswordAccessTips'] ?>
                    </div>
                </div>
            </div>
        </div>
        <p style="text-align: center;margin-top: 2em;"><?= $conf['statistics'] ?></p>
        <?php if (!empty($conf['RecordNumber'])) { ?>
            <p style="text-align: center;margin-top: 0.5em;">
                备案号:<a style="text-decoration:none;color: #000" href="https://beian.miit.gov.cn/"
                          target="_blank">
                    <?= $conf['RecordNumber'] ?>
                </a>
            </p>
        <?php } ?>
    </div>
</div>
<script src="<?php echo $cdnserver; ?>assets/layui/layui.all.js"></script>
<script src="<?php echo $cdnserver; ?>assets/js/jquery-3.4.1.min.js"></script>
<script src="/assets/js/gt4.js"></script>
<script type="text/javascript">
    const CaptchaType = "<?=$conf['CaptchaType']?>";
    const GtCaptchaId = "<?=$conf['GtCaptchaId']?>";
    let image = "<?= ROOT_DIR_S ?>/user/ajax.php?act=VerificationCode&n=PasswordAccess&t=";
    $("#submitBtn").click(function () {
        var code = $("#accessCode").val();
        if (code == '') {
            layer.alert('请填写完整！', {
                icon: 2
            });
            return false;
        }
        if (CaptchaType === "8") {
            GtCode();
        } else {
            TokenCode();
        }
    })

    //极验验证码
    function GtCode() {
        let captchaBox = document.getElementById('submitBtn');
        initGeetest4({
            captchaId: GtCaptchaId,
            product: 'bind',
            language: 'zho',
            userInfo: 'submitBtn', //验证类型,必须提交和验证时一致
        }, function (captchaObj) {
            captchaObj.reset();
            captchaObj.showBox();
            captchaObj.appendTo(captchaBox).onSuccess(function (e) {
                var result = captchaObj.getValidate();
                if (!result) {
                    alert('请先完成验证！');
                    window.location.reload();
                    return false;
                }
                result.name = 'submitBtn'; //判断验证类型
                Ajax({
                    pass: $("#accessCode").val(),
                    token: "<?=$token?>",
                    GT: result,
                });
            })
        });
    }

    //普通验证码
    function TokenCode() {
        let content = `
            <div class="layui-form" style="padding:1em 1em 0 1em;width: 300px;">
                <div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <div class="layui-input-wrap">
                                <div class="layui-input-prefix">
                                    <i class="layui-icon layui-icon-vercode"></i>
                                </div>
                                <input type="text" id="captcha" value="" placeholder="图片验证码"
                                       lay-reqtext="请填写验证码" class="layui-input" >
                            </div>
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 10px;">
                                <img src="` + image + `" style="width: 100%;height: 100%;" id="Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        //验证码弹窗
        layer.open({
            type: 1,
            title: '请输入验证码',
            content: content,
            btn: ['确定', '取消'],
            yes: function () {
                var code = $("#captcha").val();
                if (code == '') {
                    layer.alert('请输入验证码', {
                        icon: 2
                    });
                    return false;
                }
                layer.closeAll();
                Ajax({
                    pass: $("#accessCode").val(),
                    token: "<?=$token?>",
                    vercode: $("#captcha").val(),
                });
            }, success: function () {
                $("#Image").click(function () {
                    $("#Image").attr('src', image + Math.random());
                })
            }
        })
    }

    //提交验证
    function Ajax(data) {
        var is = layer.msg('验证中，请稍后...', {icon: 16, time: 9999999});
        $.ajax({
            type: "POST",
            url: './main.php?act=PasswordAccessVerify',
            data: data,
            dataType: "json",
            success: function (res) {
                layer.close(is);
                if (res.code == 1) {
                    layer.alert(res.msg, {
                        icon: 1, btn1: function () {
                            location.reload();
                        }
                    });
                } else {
                    layer.alert(res.msg, {
                        icon: 2
                    });
                }
            },
            error: function () {
                layer.msg('服务器异常！');
            }
        });
    }
</script>
</body>
</html>