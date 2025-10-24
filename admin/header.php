<?php

/**
 * 公共顶部文件
 */
if (PHP_VERSION_ID < 70100 || PHP_VERSION_ID >= 80000) {
    die('为了更好的使用程序,当前PHP版本最低设置为7.1，最高为7.4，请调整PHP版本，当前PHP版本：' . PHP_VERSION);
}
$protect_admin = true;
include '../includes/fun.global.php';
global $conf, $_QET;
if (isset($_QET['Loggedout'])) {
    $_SESSION['ADMIN_TOKEN'] = null;
    show_msg('操作成功', '成功退出登录!', '1', './login.php');
}
//升级数据校准模块
UpgradeDataCalibreModel();

if ((int)$conf['AdminHeaderTem'] === 2) {
    include 'headerTem2.php';
} else {
    include 'headerTem1.php';
}
