<?php
header('Content-type: application/pdf');
echo callService();

function callService()
{
    $token = getoauthToken();

    $url = "https://adsrestapiformsprocessing-pxxxxxtrial.hanatrial.ondemand.com/ads.restapi/v1/adsRender/pdf";

    $headers = array();
    $headers[] = "Authorization: Bearer " . $token;
    $headers[] = "Content-Type: application/json";

    $templateFileName = "f1.xdp";
    $encxdp = encodeFileToBase64Binary($templateFileName);

    $dataFileName = "f1_data.xml";
    $encdata = encodeFileToBase64Binary($dataFileName);
    ;

    $jsonObj->xdpTemplate = $encxdp;
    $jsonObj->xmlData = $encdata;
    $json = json_encode($jsonObj);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $data = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    $result = json_decode($data); // token will be with in this json

    return base64_decode($result->fileContent);
}

function getoauthToken()
{
    // create curl resource
    $base_url = 'https://oauthasservices-pxxxxxtrial.hanatrial.ondemand.com/oauth2/api/v1/token';

    $client_id = "<your client id>";
    $client_secret = "<your secret>";
    $grant_type = "client_credentials";
    $scope = "generate-ads-output";
    $encoded_Auth_Key = base64_encode($client_id . ":" . $client_secret);

    $headers = array();
    $headers[] = "Authorization: Basic " . $encoded_Auth_Key;
    $headers[] = "Content-Type: application/x-www-form-urlencoded";

    $dataAccess = "grant_type=" . $grant_type . "&scope=" . $scope;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataAccess);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $data = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch) . "</BR>";
    }

    // close curl resource to free up system resources
    curl_close($ch);

    $auth_token = json_decode($data); // token will be with in this json

    return $auth_token->access_token;
}

function encodeFileToBase64Binary($fileName)
{
    return base64_encode(file_get_contents($fileName));
}

?>
