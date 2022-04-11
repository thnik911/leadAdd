<?php
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
writetolog($_REQUEST, 'new request');


$name = $_REQUEST['name'];
$phone = $_REQUEST['phone'];
$email = $_REQUEST['email'];
$source = $_REQUEST['source'];
$telegramID = $_REQUEST['telegramID'];
$source2 = $_REQUEST['source2'];
$city = $_REQUEST['city'];

//AUTH Б24
require_once('auth.php');

if($source == 'BotFromVK'){
    $sourceForB24 = 'STORE';
}elseif($source == 'BotFromFB'){
    $sourceForB24 = 96;
}elseif($source == 'BotFromTG'){
    $sourceForB24 = 'CALLBACK';
}elseif($source == 'BotFromInsta'){
    $sourceForB24 = 97;
}

if($city == 'Москва'){
    $cityForB24 = 1216;
}elseif($city == 'Москва +3'){
    $cityForB24 = 1217;
}elseif($city == 'Москва +6'){
    $cityForB24 = 1218;
}

$leadAdd = executeREST(
    'crm.lead.add',
    array(
            'fields' => array(
                'TITLE' => $name,
                'NAME' => $name,
                //'COMMENTS' => $source,
                'PHONE' => array( array( "VALUE" => $phone, "VALUE_TYPE" => "WORK" ) ),
                'EMAIL' => array( array( "VALUE" => $email, "VALUE_TYPE" => "WORK" ) ),
                'UF_USERID_TELEGRAM' => $telegramID,
                'SOURCE_ID' => $sourceForB24,
                'SOURCE_DESCRIPTION' => $source2,
                'UF_CRM_1643981451' => $cityForB24,

            ),
        ),
$domain, $auth, $user);

function executeREST ($method, array $params, $domain, $auth, $user) {
    $queryUrl = 'https://'.$domain.'/rest/'.$user.'/'.$auth.'/'.$method.'.json';
    $queryData = http_build_query($params);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
    ));
    return json_decode(curl_exec($curl), true);
    curl_close($curl);
}

function writeToLog($data, $title = '') {
$log = "\n------------------------\n";
$log .= date("Y.m.d G:i:s") . "\n";
$log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
$log .= print_r($data, 1);
$log .= "\n------------------------\n";
file_put_contents(getcwd() . '/logs/leadAddFromTG.log', $log, FILE_APPEND);
return true;
}

?>