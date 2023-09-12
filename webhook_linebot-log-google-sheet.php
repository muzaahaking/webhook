<?php
$channelAccessToken = 'nVP4ZB/FEDZU03bs11XhfGBm2VS7ZXAcajKFslpHSDpsDGwr1DpfKqpTHEl5JaiRrK09b2wMPyh4J80OQo8+Zn5+IDcPtMDBnzdlwhrpnpzgC8Zb+YrA2mjav8VlmEgU1IE+1bRt4SO8qet91iHsjgdB04t89/1O/w1cDnyilFU=';
$gas_url = 'https://script.google.com/macros/s/AKfycbyVFLNU1NR0ijGnc9m8vOCL94E37-BifbTGPEGoVV4iW81XbvN53Fq72PJGJfUrU0HY/exec';

$request = file_get_contents('php://input');
$request_json = json_decode($request, true);

foreach ($request_json['events'] as $event) {
    if ($event['type'] == 'message') {
        if ($event['message']['type'] == 'text') {
            $message = $event['message']['text'];

            $reply_message = 'ฉันได้รับข้อความ "' . $message . '" ของคุณแล้ว!';
        } else {
            $reply_message = 'ฉันได้รับ "' . $event['message']['type'] . '" ของคุณแล้ว!';
        }
    } else {
        $reply_message = 'ฉันได้รับ Event "' . $event['type'] . '" ของคุณแล้ว!';
    }

    $user = $event['replyToken'];

    reply_message($user, $reply_message);

    // Use the actual data for logMessagesToGoogleAppsScript
    logMessagesToGoogleAppsScript($user, $message, $reply_message);
}

function reply_message($replyToken, $reply_message) {
    global $channelAccessToken;
    $url = 'https://api.line.me/v2/bot/message/reply';

    $data = ['replyToken' => $replyToken, 'messages' => [['type' => 'text', 'text' => $reply_message]]];
    $post_body = json_encode($data);

    $post_header = ['Content-Type: application/json', 'Authorization: Bearer ' . $channelAccessToken];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function logMessagesToGoogleAppsScript($user, $message, $reply_message) {
    global $gas_url;

    // Construct the URL with query parameters
    $gas_url .= "?user=" . urlencode($user) . "&message=" . urlencode($message) . "&reply_message=" . urlencode($reply_message);

    // Set up cURL for the GET request
    $ch = curl_init($gas_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Send the GET request
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
?>
