<?php
include 'phpqrcode.php'; 
$ss=QRcode::png('/');
echo $ss;