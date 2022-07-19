<?php
$stkCallbackResponse = file_get_contents('php://input');
$requestingServerData = $_SERVER;
file_put_contents('api-audit-access1.log',print_r($requestingServerData,true));
//$method = $_SERVER['HTTP_X_DYNATRACE_APPLICATION'];
//$result = explode(';',$method);
//$cookieDomain = explode('=',$result[2]);
//$safaricom_domain =$cookieDomain[1];
////echo $safaricom_domain;
$logFile = "stkPushCallbackPaidResponse.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse);
fclose($log);

?>
