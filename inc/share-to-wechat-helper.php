<?php

if (!isset( $_GET['url'] )) {
    exit();
}

$url = $_GET['url'];

include_once( 'lib/phpqrcode/qrlib.php' );
QRcode::png($url, false, QR_ECLEVEL_M, 3);