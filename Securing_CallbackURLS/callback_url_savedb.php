<?php


function insert_response($jsonMpesaResponse){

    /**
     * READ CAREFULLY
     * 1.0 Create a database, or import the table mobile_payments.sql
     * 1.1 Change the db config section below to reflect your system
     * 1.2 Ensure you have updated your access token to simulate the transaction
     * 1.4 Simulate the transaction
     *
     * Kindly, note the changes on our simulate.php, otherwise this will not work as expected.
     **/

    # 1.1. Config Section
    $dbName = 'radius';
    $dbHost = 'localhost';
    $dbUser = 'root';
    /** @var TYPE_NAME $dbPass */
    $dbPass = '  m';

    # 1.1.1 establish a connection
    try{
        $con = new PDO("mysql:dbhost=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        echo "Connection was successful";
    }
    catch(PDOException $e){
        die("Error Connecting ".$e->getMessage());
    }

    # 1.1.2 Insert Response to Database
    try{
        $insert = $con->prepare("INSERT INTO `mpesa_stkpush_payments`(`transaction_id`, `resultDesc`, `resultCode`, `merchantRequestID`, `checkoutRequestID`, `amount`, `mpesaReceiptNumber`, `balance`, `b2CUtilityAccountAvailableFunds`, `transactionDate`, `phoneNumber`, `created_at`, `modified_at`) 
VALUES (default ,:resultDesc, :resultCode, :merchantRequestID, :checkoutRequestID, :amount, :mpesaReceiptNumber, :balance, :b2CUtilityAccountAvailableFunds, :transactionDate, :phoneNumber, default, default)");
        $insert->execute((array)($jsonMpesaResponse));

        # 1.1.2o Optional - Log the transaction to a .txt or .log file(May Expose your transactions if anyone gets the links, be careful with this. If you don't need it, comment it out or secure it)
        $Transaction = fopen('Transaction.txt', 'a');
        fwrite($Transaction, json_encode($jsonMpesaResponse));
        fclose($Transaction);
    }
    catch(PDOException $e){

        # 1.1.2b Log the error to a file. Optionally, you can set it to send a text message or an email notification during production.
        $errLog = fopen('error.txt', 'a');
        fwrite($errLog, $e->getMessage());
        fclose($errLog);

        # 1.1.2o Optional. Log the failed transaction. Remember, it has only failed to save to your database but M-PESA Transaction itself was successful.
        $logFailedTransaction = fopen('failedTransaction.txt', 'a');
        fwrite($logFailedTransaction, json_encode($jsonMpesaResponse));
        fclose($logFailedTransaction);
    }
}

function insert_session_data($jsonMpesaResponse){

    /**
     * READ CAREFULLY
     * 1.0 Create a database, or import the table mobile_payments.sql
     * 1.1 Change the db config section below to reflect your system
     * 1.2 Ensure you have updated your access token to simulate the transaction
     * 1.4 Simulate the transaction
     *
     * Kindly, note the changes on our simulate.php, otherwise this will not work as expected.
     **/

    # 1.1. Config Section
    $dbName = 'mpesa';
    $dbHost = 'localhost';
    $dbUser = 'root';
    /** @var TYPE_NAME $dbPass */
    $dbPass = '  m';

    # 1.1.1 establish a connection
    try{
        $con = new PDO("mysql:dbhost=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        echo "Connection was successful";
    }
    catch(PDOException $e){
        die("Error Connecting ".$e->getMessage());
    }

    # 1.1.2 Insert Response to Database
    try{
        $insert = $con->prepare("INSERT INTO `stkpush_payments`(`transaction_id`, `resultDesc`, `resultCode`, `merchantRequestID`, `checkoutRequestID`, `amount`, `mpesaReceiptNumber`, `balance`, `b2CUtilityAccountAvailableFunds`, `transactionDate`, `phoneNumber`, `created_at`, `modified_at`) 
VALUES (default ,:resultDesc, :resultCode, :merchantRequestID, :checkoutRequestID, :amount, :mpesaReceiptNumber, :balance, :b2CUtilityAccountAvailableFunds, :transactionDate, :phoneNumber, default, default)");
        $insert->execute((array)($jsonMpesaResponse));

        # 1.1.2o Optional - Log the transaction to a .txt or .log file(May Expose your transactions if anyone gets the links, be careful with this. If you don't need it, comment it out or secure it)
        $Transaction = fopen('Transaction.txt', 'a');
        fwrite($Transaction, json_encode($jsonMpesaResponse));
        fclose($Transaction);
    }
    catch(PDOException $e){

        # 1.1.2b Log the error to a file. Optionally, you can set it to send a text message or an email notification during production.
        $errLog = fopen('error.txt', 'a');
        fwrite($errLog, $e->getMessage());
        fclose($errLog);

        # 1.1.2o Optional. Log the failed transaction. Remember, it has only failed to save to your database but M-PESA Transaction itself was successful.
        $logFailedTransaction = fopen('failedTransaction.txt', 'a');
        fwrite($logFailedTransaction, json_encode($jsonMpesaResponse));
        fclose($logFailedTransaction);
    }
}

$callbackJSONData=file_get_contents('php://input');
$callbackData=json_decode($callbackJSONData);
$resultCode=$callbackData->Body->stkCallback->ResultCode;
$resultDesc=$callbackData->Body->stkCallback->ResultDesc;
$merchantRequestID=$callbackData->Body->stkCallback->MerchantRequestID;
$checkoutRequestID=$callbackData->Body->stkCallback->CheckoutRequestID;

$amount=$callbackData->stkCallback->Body->CallbackMetadata->Item[0]->Value;
$mpesaReceiptNumber=$callbackData->Body->stkCallback->CallbackMetadata->Item[1]->Value;
$balance=$callbackData->stkCallback->Body->CallbackMetadata->Item[2]->Value;
$b2CUtilityAccountAvailableFunds=$callbackData->Body->stkCallback->CallbackMetadata->Item[3]->Value;
$transactionDate=$callbackData->Body->stkCallback->CallbackMetadata->Item[4]->Value;
$phoneNumber=$callbackData->Body->stkCallback->CallbackMetadata->Item[5]->Value;

$result=[
    "resultDesc"=>$resultDesc,
    "resultCode"=>$resultCode,
    "merchantRequestID"=>$merchantRequestID,
    "checkoutRequestID"=>$checkoutRequestID,
    "amount"=>$amount,
    "mpesaReceiptNumber"=>$mpesaReceiptNumber,
    "balance"=>$balance,
    "b2CUtilityAccountAvailableFunds"=>$b2CUtilityAccountAvailableFunds,
    "transactionDate"=>$transactionDate,
    "phoneNumber"=>$phoneNumber
];

if ($resultCode==0)
{
    //Capture the user's phone number then insert the data to the said row.
    insert_response($result);
    $logFile = "stkPushCallbackResponse.json";
    $log = fopen($logFile, "a");
    fwrite($log, json_encode($result));
    fclose($log);
}
else if ($resultCode == 1)
{
    // success payment
    $logFile = "stkPushCallbackResponse.json";
    $log = fopen($logFile, "a");
    fwrite($log, json_encode($result));
    fclose($log);
}
else if($resultCode == 1032)
{
    //cancelled
}
else if($resultCode == 2001)
{
    //incorrect pin entered/wrong number
}
else {

}

//1 -- insufficient funds
//2001 -- incorrect pin entered
//return json_encode($result);
//1037 -- timeout user cannot be reached

?>
