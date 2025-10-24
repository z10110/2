//站长后台登录js
var AdminLogin = {
    GetNode() {
        $.ajax({
            type: 'POST',
            url: 'main.php?act=ApiList',
            data: {
                type: 2,
            },
            dataType: "json",
            success: function (data) {
                let content = '';
                for (const dataKey in data.data) {
                    if (dataKey == (data.at - 1)) {
                        content += '<option selected value="' + dataKey + '" id="SelectS_' + dataKey + '">' + data.data[dataKey].name + ' - ' + (data.data[dataKey].ping) + '</option>';
                    } else {
                        content += '<option value="' + dataKey + '">' + data.data[dataKey].name + ' - ' + (data.data[dataKey].ping) + '</option>';
                    }
                }
                $(".SelectS").html(content);
                $("#SelectS").show();
            }, error: function () {
                $("#SelectS").hide();
            }
        })
    },
    GetNodeSet(id) {
        $.ajax({
            type: 'POST',
            url: 'main.php?act=ApiSet',
            data: {
                id: id,
            },
            dataType: "json",
            success: function (data) {
                if (data.code >= 1) {
                    layer.msg(data.msg, {
                        icon: 1,
                    });
                } else {
                    layer.alert(data.msg, {
                        icon: 2,
                    });
                }
                AdminLogin.GetNode();
            }, error: function () {
                $("#SelectS").hide();
            }
        })
    },
    do_user_login: function () {
        var user = $("input[name='user']").val();
        var pass = $("input[name='pass']").val();
        if (user == '' || pass == '') {
            layer.alert('请将账号密码填写完整！', {
                icon: 2,
            });
            return;
        }
        if (CaptchaType === "8") {
            AdminLogin.GtCodeLogin(user, pass);
        } else {
            AdminLogin.CodeLogin(user, pass);
        }
    }, GtCodeLogin(user, pass = false, name = 'Login') { //极验验证码登录
        let captchaBox = document.getElementById(name);
        initGeetest4({
            captchaId: GtCaptchaId,
            product: 'bind',
            language: 'zho',
            userInfo: name, //验证类型,必须提交和验证时一致
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
                result.name = name; //判断验证类型
                if (name == 'Login') {
                    //账号密码登录
                    AdminLogin.login_ajax({
                        pass: pass,
                        user: user,
                        GT: result,
                    });
                } else {
                    //手机号验证码发送
                    AdminLogin.do_admin_get_mobile_code(result, 2);
                }
            })
        });
    }, CodeLogin(user, pass = false, name = 'Login') { //验证码登录
        if (name == 'Login') {
            var image = './ajax.php?act=VerificationCode&n=Login_uvc&t=';
        } else {
            var image = './ajax.php?act=VerificationCode&n=AdminLogin_sms_vis&t=';
        }
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
                if (name == 'Login') {
                    AdminLogin.login_ajax({
                        pass: pass,
                        user: user,
                        vercode: $("#captcha").val(),
                    });
                } else {
                    //手机号验证码发送
                    AdminLogin.do_admin_get_mobile_code($("#captcha").val(), 2);
                }
            }, success: function () {
                $("#Image").click(function () {
                    console.log('123');
                    $("#Image").attr('src', image + Math.random());
                })
            }
        })
    }, login_ajax(data) {
        layer.msg('正在登录中,请稍后...', {icon: 16, time: 9999999});
        $.ajax({
            type: "post", url: "ajax.php?act=login_account", data: data, dataType: "json", success: function (data) {
                layer.closeAll();
                if (data.code == 1) {
                    layer.msg(data.msg, {icon: 1});
                    layer.alert(data.msg, {
                        icon: 1, yes: function () {
                            location.href = './index.php';
                        }
                    })
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            }, error: function () {
                layer.alert('登录请求发送失败！');
            }
        });
    }, do_login_scan: function () {
        layer.msg('正在获取二维码数据', {icon: 16, time: 9999999});
        $.ajax({
            type: "POST", url: "ajax.php?act=login_scan", dataType: "json", success: function (data) {
                layer.closeAll();
                if (data.code > 0) {
                    layer.open({
                        title: '请打开小程序"晴天窝"完成扫码',
                        content: '<img class="AdminImageLogin" src="' + data.image + '" />',
                        btn: false,
                        area: ['300px', '340px'],
                        offset: '150px',
                        end: function () {
                            location.reload();
                        }
                    });
                    AdminLogin.do_login_wx_monitoring();
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            }, error: function () {
                layer.closeAll();
                layer.alert('服务器异常！');
            },
        });
    }, do_login_app: function () {
        layer.open({
            title: '是否要使用服务端APP登录？',
            content: '点击确认后请前往服务端APP验证界面完成登录验证！',
            btn: ['确认', '取消'],
            icon: 3,
            anim: 6,
            btn1: function () {
                layer.msg('正在发送服务端APP登录验证码', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: "ajax.php?act=login_app", dataType: "json", success: function (data) {
                        if (data.code > 0) {
                            $(".Token").attr('data-token', data.token);
                            AdminLogin.do_login_wx_monitoring(data.token);
                            layer.alert(data.msg, {icon: 1});
                        } else {
                            layer.alert(data.msg, {icon: 2});
                        }

                    }, error: function () {
                        layer.alert('服务器异常！');
                    },
                });
            },
        });
    }, do_login_wx: function () {
        layer.open({
            title: '是否要使用微信登录？',
            content: '点击确定后会在微信端发送登录验证码,点击确认后即可完成登录！',
            btn: ['确认发送', '取消'],
            icon: 3,
            anim: 6,
            btn1: function () {
                layer.msg('正在发送微信登录验证码', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: "ajax.php?act=login", dataType: "json", success: function (data) {
                        if (data.code > 0) {
                            $(".Token").attr('data-token', data.token);
                            AdminLogin.do_login_wx_monitoring(data.token);
                            layer.alert(data.msg, {icon: 1});
                        } else {
                            layer.alert(data.msg, {icon: 2});
                        }

                    }, error: function () {
                        layer.alert('服务器异常！');
                    },
                });
            },
        });
    }, do_login_wx_monitoring: function (token = null) {
        $.ajax({
            type: "POST", url: "ajax.php?act=login_log", dataType: "json", success: function (data) {
                AdminLogin.monitoring(data);
                if (data.code == '-2') {
                    layer.alert(data.msg, {
                        icon: 2, end: function () {
                            location.reload();
                        },
                    });
                } else if (data.code == '-1') {
                    setTimeout(function () {
                        AdminLogin.do_login_wx_monitoring(token);
                    }, 1500);
                }

            }, error: function () {
                layer.alert('服务器异常！');
            },
        });
    }, monitoring: function (data) {
        $("#form").hide(100);
        $("#Weix").show(100);
        $(".WeixText").html(data.msg);
        if (data.code == 1) {
            setTimeout(function () {
                location.reload();
            }, 1500);
        }

    }, do_admin_login_token: function () {
        var token = $("input[name='token']").val();
        if (token == '') {
            layer.msg('请填写完整！', {
                icon: 2, title: '温馨提示',
            });
            return false;
        }

        layer.open({
            title: '确认手动输入验证码登录？',
            content: '点击确认后将验证你填写的token，验证成功后将登录后台！',
            btn: ['确认验证', '取消'],
            icon: 3,
            anim: 6,
            btn1: function () {
                layer.msg('正在验证,请稍后', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST",
                    url: "ajax.php?act=login_token",
                    data: {token: token},
                    dataType: "json",
                    success: function (data) {
                        if (data.code > 0) {
                            AdminLogin.monitoring(data);
                            layer.alert(data.msg, {icon: 1});
                        } else {
                            layer.alert(data.msg, {icon: 2});
                        }
                    },
                    error: function () {
                        layer.alert('服务器异常！');
                    },
                });
            },
        });
    }, do_admin_get_mobile_code(type = 2, state = 1) {
        var phone = $("input[name='phone']").val();
        if (phone == '') {
            layer.alert('请先填写手机号', {icon: 2});
            return false;
        }
        let btnName = $("#DoLoginCode").text();
        //判断是否包含“获取”
        if (btnName.indexOf("获取") == -1) {
            layer.alert($("#DoLoginCode").attr("tips"), {icon: 2});
            return false;
        }
        if (type == 2 && state === 1) {
            if (CaptchaType === "8") {
                AdminLogin.GtCodeLogin(phone, false, 'SmsVis');
            } else {
                AdminLogin.CodeLogin(phone, false, 'SmsVis');
            }
            return false;
        }
        if (CaptchaType === "8") {
            var data = {
                mobile: phone, GT: type,
            };
        } else {
            var data = {
                mobile: phone, code: type,
            };
        }
        layer.msg('正在发送,请稍后', {icon: 16, time: 9999999});
        $.ajax({
            type: "POST",
            url: 'ajax.php?act=Send_verification_code_login',
            data: data,
            dataType: "json",
            success: function (data) {
                layer.closeAll();
                if (data.code > 0) {
                    AdminLogin.do_admin_codes(60);
                    layer.alert(data.msg, {icon: 1});
                } else if (data.code == -2) {
                    layer.msg(data.msg, {icon: 2});
                    $("#smsvis #vc").attr('src', 'ajax.php?act=VerificationCode&n=AdminLogin_sms_vis');
                    $("#smsvis input[name='vercode']").val('');
                } else {
                    layer.msg(data.msg, {icon: 2});
                }
            },
            error: function () {
                layer.alert('服务器异常！');
            },
        });
    }, do_admin_codes(m) {
        $("#DoLoginCode").attr("ms", m)
        $("#DoLoginCode").attr("tips", m + '秒后可重发')
        $("#DoLoginCode").html(m + '秒');
        var ms = $("#DoLoginCode").attr("ms") - 0;
        if (ms <= 0) {
            $("#DoLoginCode").html("获取\n");
        } else {
            setTimeout(function () {
                AdminLogin.do_admin_codes(ms - 1);
            }, 1000);
        }
    }, do_admin_login_phone_verify() {
        var code = $("input[name='vercode']").val();
        layer.open({
            title: '是否确认登录？',
            content: '若确认验证码填写正确，可点击确定登录后台！',
            btn: ['确认', '取消'],
            icon: 3,
            anim: 6,
            btn1: function () {
                layer.msg('正在验证,请稍后', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST",
                    url: 'ajax.php?act=Send_verification_login',
                    data: {code: code},
                    dataType: "json",
                    success: function (data) {
                        if (data.code > 0) {
                            layer.alert(data.msg, {
                                icon: 1, title: '恭喜', end: function (lyaero, index) {
                                    location.reload();
                                }
                            });
                        } else {
                            layer.alert(data.msg, {icon: 2});
                        }
                    },
                    error: function () {
                        layer.alert('服务器异常！');
                    },
                });
            },
        });
    },
};

AdminLogin.GetNode();
if ($("#DoLoginCode").attr("ms") > 0) {
    AdminLogin.do_admin_codes($("#DoLoginCode").attr("ms") - 0);
}
$(".SelectS").on("change", function () {
    AdminLogin.GetNodeSet($(".SelectS").val());
});