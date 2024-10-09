<?php
//First show errormsg
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
// ob_start();
////////////////////////
require 'vendor/autoload.php';



use net\authorize\api\contract\v1 as AnetAPI;

use net\authorize\api\controller as AnetController;



define("AUTHORIZENET_LOG_FILE", "phplog");



function chargeCreditCard($creditCardData)

{

    /* Create a merchantAuthenticationType object with authentication details

       retrieved from the constants file */


    $RESPONSE_OK = "Ok";

    $process_result = [];




    $merchant_login_id = get_option('se_payment_authorized_login');

    $merchant_transaction_key = get_option('se_payment_authorized_transaction_key');



    if ( empty($merchant_login_id) || empty($merchant_transaction_key) ) {

        $process_result['tnx_error'] = 'Missing Marchant Information. Please contact with adminstrator to fill up marchant information from admin-> SE Settings > Payment Method > Authorize.net Settings. We are very sorry for the inconvenience.';

        return $process_result;

    }

    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

    $merchantAuthentication->setName($merchant_login_id);

    $merchantAuthentication->setTransactionKey($merchant_transaction_key);

    

    // Set the transaction's refId

    $refId = 'ref' . time();



    // Create the payment data for a credit card

    $creditCard = new AnetAPI\CreditCardType();

    $creditCard->setCardNumber($creditCardData['cardNumber']);

    $creditCard->setExpirationDate($creditCardData['exp_year'].'-'.$creditCardData['exp_month']);// "2038-12"

    $creditCard->setCardCode($creditCardData['cvv']);



    // Add the payment data to a paymentType object

    $paymentOne = new AnetAPI\PaymentType();

    $paymentOne->setCreditCard($creditCard);



    // Create order information

    $order = new AnetAPI\OrderType();

    $order->setInvoiceNumber($creditCardData['transaction_title']); // tnx id

    $order->setDescription($creditCardData['product_desc']);



    // Set the customer's Bill To address

    $customerAddress = new AnetAPI\CustomerAddressType();

    $customerAddress->setFirstName($creditCardData['first_name']);

    $customerAddress->setLastName($creditCardData['last_name']);

    // $customerAddress->setCompany("Souveniropolis");

    $customerAddress->setAddress($creditCardData['street_address']);

    $customerAddress->setCity($creditCardData['city']);

    $customerAddress->setState($creditCardData['state']);

    $customerAddress->setZip($creditCardData['postal']);

    $customerAddress->setCountry($creditCardData['country']);



    // Set the customer's identifying information

    $customerData = new AnetAPI\CustomerDataType();

    $customerData->setType("individual");

    $customerData->setId($creditCardData['registration_title']); // reg id

    $customerData->setEmail($creditCardData['email']);



    // Add values for transaction settings

    $duplicateWindowSetting = new AnetAPI\SettingType();

    $duplicateWindowSetting->setSettingName("duplicateWindow");

    $duplicateWindowSetting->setSettingValue("60");



    // Add some merchant defined fields. These fields won't be stored with the transaction,

    // but will be echoed back in the response.

    $merchantDefinedField1 = new AnetAPI\UserFieldType();

    $merchantDefinedField1->setName("customerLoyaltyNum");

    $merchantDefinedField1->setValue("1128836273");



    $merchantDefinedField2 = new AnetAPI\UserFieldType();

    $merchantDefinedField2->setName("favoriteColor");

    $merchantDefinedField2->setValue("blue");



    // Create a TransactionRequestType object and add the previous objects to it

    $transactionRequestType = new AnetAPI\TransactionRequestType();

    $transactionRequestType->setTransactionType("authCaptureTransaction");

    $transactionRequestType->setAmount($creditCardData['ticket_price']);

    $transactionRequestType->setOrder($order);

    $transactionRequestType->setPayment($paymentOne);

    $transactionRequestType->setBillTo($customerAddress);

    $transactionRequestType->setCustomer($customerData);

    $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

    $transactionRequestType->addToUserFields($merchantDefinedField1);

    $transactionRequestType->addToUserFields($merchantDefinedField2);



    // Assemble the complete transaction request

    $request = new AnetAPI\CreateTransactionRequest();

    $request->setMerchantAuthentication($merchantAuthentication);

    $request->setRefId($refId);

    $request->setTransactionRequest($transactionRequestType);



    // Create the controller and get the response

//$process_result['tnx_error'] = implode("-", $creditCardData);// Testing payment issues
//$process_result['tnx_error'] = ob_get_clean();// Testing payment issues
//return $process_result;// Testing payment issues

    $controller = new AnetController\CreateTransactionController($request); //issue is here



    $apiResponseURL = get_option('se_payment_paypal_sendbox') == 'enabled' ? \net\authorize\api\constants\ANetEnvironment::SANDBOX : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;



    $response = $controller->executeWithApiResponse($apiResponseURL);



    if ($response != null) {

        // Check to see if the API request was successfully received and acted upon

        if ($response->getMessages()->getResultCode() == $RESPONSE_OK) {

            // Since the API request was successful, look for a transaction response

            // and parse it to display the results of authorizing the card

            $tresponse = $response->getTransactionResponse();

        

            if ($tresponse != null && $tresponse->getMessages() != null) {

                // echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";

                // echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";

                // echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";

                // echo " Auth Code: " . $tresponse->getAuthCode() . "\n";

                // echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";

                $process_result['tnx_id'] = $tresponse->getTransId();

            } else {

                // echo "Transaction Failed \n";

                if ($tresponse->getErrors() != null) {

                    // echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";

                    $process_result['tnx_error'] = $tresponse->getErrors()[0]->getErrorText();

                }

            }

            // Or, print errors if the API request wasn't successful

        } else {

            // echo "Transaction Failed \n";

            $tresponse = $response->getTransactionResponse();

        

            if ($tresponse != null && $tresponse->getErrors() != null) {

                // echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";

                $process_result['tnx_error'] = $tresponse->getErrors()[0]->getErrorText();

            } else {

                // echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";

                    $process_result['tnx_error'] = $response->getMessages()->getMessage()[0]->getText() ;

            }

        }

    } else {

        $process_result['tnx_error'] = "No response returned \n";

    }



    return $process_result;

}





