<?php

$channelAccessToken = '5EThX6Vk3J1vG2loJYE3sBL+2RIPhStAsGNsWl60p+5OUJxMArXpnRTdY42INPSRBzLQ+YMi3ZYGQ9IYMQ7xUxzJ5sP91Tl9mxn8sndw6wucWSKupVtPQeJqU3rn/HrAqGqllTSVAc/2awUFwsrHIgdB04t89/1O/w1cDnyilFU=';
$gas_url = 'https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec'; // Replace with your actual GAS web app URL

$request = file_get_contents('php://input');
$request_json = json_decode($request, true);

foreach ($request_json['events'] as $event) {
    $user = $event['source']['userId'];
    $message = $event['message']['text'];
    $reply_message = 'ฉันได้รับข้อความ "'.$message.'" ของคุณแล้ว!';

    // Log both the incoming message and the reply message
    logMessagesToGoogleAppsScript($user, $message, $reply_message);

    // Send the reply message
    replyMessage($event['replyToken'], $reply_message);
}

function logMessagesToGoogleAppsScript($user, $message, $reply_message) {
    global $gas_url;
    $data = array(
        'user' => $user,
        'message' => $message,
        'reply_message' => $reply_message
    );

    // Send a POST request to Google Apps Script to log the messages
    sendPostRequest($gas_url, $data);
}

function sendPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}

function replyMessage($replyToken, $reply_message) {
    global $channelAccessToken;
    $url = 'https://api.line.me/v2/bot/message/reply';

    $data = ['replyToken' => $replyToken, 'messages' => [['type' => 'text', 'text' => $reply_message]]];
    $post_body = json_encode($data);

    $post_header = ['Content-Type: application/json', 'Authorization: Bearer ' . $channelAccessToken];

    sendPostRequest($url, $post_body, $post_header);
}
