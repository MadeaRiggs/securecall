<?php

include ("callback_url_old.php");

if($cookieDomain == $safaricom_domain) {
    echo  $_SERVER['HTTP_REFERER'];
}












?>