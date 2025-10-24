<?php
if (!defined('IN_CRONLITE')) die;
?>
<!DOCTYPE html>
<html lang=zh-CN>
<head>
    <meta charset=utf-8>
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <title><?= $conf['sitename'] . ($conf['title'] == '' ? '' : ' - ' . $conf['title']) ?></title>
    <meta name="keywords" content="<?= $conf['keywords'] ?>">
    <meta name="description" content="<?= $conf['description'] ?>">
    <script>var coverSupport = 'CSS' in window && typeof CSS.supports === 'function' && (CSS.supports('top: env(a)') || CSS.supports('top: constant(a)'))
        document.write('<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0' + (coverSupport ? ', viewport-fit=cover' : '') + '" />')</script>
    <link rel=stylesheet
          href="<?php echo $cdnserver; ?>assets/template/default/static/index.97465e7b.css?t=<?= $accredit['versions'] ?>">
    <link rel="stylesheet" href="<?php echo $cdnserver; ?>assets/css/Global.css?t=<?= $accredit['versions'] ?>">
    <link rel="shortcut icon" href="./assets/favicon.ico" type="image/x-icon"/>
</head>
<body>
<noscript><strong>您的浏览器好像不支持js,必须支持js才可访问本站哦</strong></noscript>
<div id=app></div>
<div style="font-size: 0.5rem;text-align: center;position: fixed;bottom: -10rem;left: 0;width: 100%;z-index: 0;"><?= $conf['statistics'] ?></div>
<script src="<?php echo $cdnserver; ?>assets/template/default/static/js/chunk-vendors.5815f872.js?t=<?= $accredit['versions'] ?>"></script>
<script src="<?php echo $cdnserver; ?>assets/template/default/static/js/index.3748ba20.js?t=<?= $accredit['versions'] ?>"></script>
</body>
</html>