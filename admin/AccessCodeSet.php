<?php
//安全码配置
$protect_admin = true;
include_once '../includes/fun.global.php';
$AccessCode = $_SESSION['AccessCode'];
if (!empty($_SESSION['AccessCode'])) {
    $AccessCode = substr_cut($AccessCode, 2, ceil(strlen($AccessCode) / 5));
}
//获取来路域名
$domain = $_SERVER['HTTP_REFERER'];
//判断是否包含：main.php?act=ServerExtension
if (strpos($domain, 'main.php?act=ServerExtension') === false) {
    $type = 2;
} else {
    $type = 1;
}
?>
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>安全码配置</title>
    <link rel="stylesheet" type="text/css" href="../assets/layui/css/layui.css"/>
</head>
<body style="background-color:#eee;">
<div style="padding:1em;">
    <div class="layui-card">
        <div class="layui-card-header">
            安全码配置
        </div>
        <div class="layui-card-body">
            <div class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">安全码</label>
                    <div class="layui-input-block">
                        <input type="text"
                               placeholder="请输入从服务端获取的安全码" id="AccessCode" value="<?= $AccessCode ?>"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" onclick="submit()">立即提交</button>
                        <?php if ($type == 1) { ?>
                            <button type="reset" class="layui-btn layui-btn-primary"
                                    onclick="history.back(-1)">
                                返回上一页
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            如何获取安全码?
        </div>
        <div class="layui-card-body">
            <li>1、打开并登录服务端：<a href="https://cdn.79tian.com/api/wxapi/view/" target="_blank" style="color: red">点击打开</a>
            </li>
            <li>2、在主页，便捷导航内，找到
                <button class="layui-btn layui-btn-radius layui-btn-sm" style="background-color: #27c4fd;">设置安全码
                </button>
                按钮
            </li>
            <li>3、点击按钮设置安全码，设置完成后在此处填写即可访问某些特殊页面！</li>
            <img src="<?= ROOT_DIR ?>assets/img/acode.png" style="width: 350px;margin:1em 0;">
        </div>
    </div>
</div>
<script src="../assets/layui/layui.all.js"></script>
<script src="../assets/js/jquery-3.4.1.min.js"></script>
<script>
    function submit() {
        let AccessCode = $('#AccessCode').val();
        if (AccessCode.length < 5) {
            layer.msg('安全码长度不得小于5位！', {icon: 2});
            return false;
        }
        layer.open({
            title: '温馨提示',
            content: '确认要将安全码设置为：' + AccessCode + '吗？<hr>请牢记您的安全码，安全码将用于服务端与扩展工作台的通信！',
            btn: ['确认', '取消'],
            yes: function (index) {
                layer.close(index);
                $.ajax({
                    url: './main.php?act=AccessCodeSet',
                    type: 'POST',
                    data: {
                        AccessCode: AccessCode
                    },
                    success: function (data) {
                        if (data.code === 1) {
                            layer.alert(data.msg, {
                                icon: 1
                            });
                        } else {
                            layer.alert(data.msg, {icon: 2});
                        }
                    }
                });
            },
        });
    }
</script>
</body>
</html>
