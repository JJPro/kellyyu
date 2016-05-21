<?php
/**
 * Share To Wechat
 *
 * generates the QR code for sharing to wechat
 */

    if (!isset( $_GET['url'] )) {
        exit();
    }

    $url = $_GET['url'];

    $iframe_src = str_replace(pathinfo(__FILE__, PATHINFO_BASENAME), 'share-to-wechat-helper.php', $_SERVER['REQUEST_URI']);

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h3>分享到微信</h3>
        <p>
            扫描以下二维码以分享到微信:
        </p>

        <iframe src="<?php echo $iframe_src; ?>"></iframe>

        <style>
            iframe {border: none; height: 300px;}
        </style>

    </body>
</html>