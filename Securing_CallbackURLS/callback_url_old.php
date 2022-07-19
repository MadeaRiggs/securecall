<?php
$stkCallbackResponse = file_get_contents('php://input');


/* audit log*/
$requestingServerData = $_SERVER;
file_put_contents('api-audit-access1.log', print_r($requestingServerData,true), FILE_APPEND);

//
//$method = $_SERVER['HTTP_CF_CONNECTING_IP'];
//$method = $_SERVER['HTTP_X_FORWARDED_FOR'];
$method = $_SERVER['HTTP_X_DYNATRACE_APPLICATION'];
$result = explode(';',$method);
$cookieDomain = explode('=',$result[2]);
$safaricom_domain =$cookieDomain[1];
echo $safaricom_domain;

$safIps =
    [
        '196.201.214.207',
        '196.201.214.208',
        '196.201.214.209'
    ];

if (true){
    $requestingServerData = $_SERVER;
    file_put_contents('api-audit-access8.log',print_r($requestingServerData,true));
    $log ="------------------" .PHP_EOL."
    # ".date('Y-m-d h:i:s')."Unauthorized Access Detected: From IP {$method}".PHP_EOL."With the following Data".PHP_EOL.
        " {$stkCallbackResponse} ". PHP_EOL. "--------------------";
    $handle = fopen('intrusion_attempts1.txt','a');
    fwrite($handle,$log);
}
else
{
    $logFile = "stkPushCallbackResponse.json";
    $log = fopen($logFile, "a");
    fwrite($log, $stkCallbackResponse);
    fclose($log);
}
?>
