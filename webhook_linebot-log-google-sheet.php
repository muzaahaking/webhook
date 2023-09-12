<?php

$channelAccessToken = '5EThX6Vk3J1vG2loJYE3sBL+2RIPhStAsGNsWl60p+5OUJxMArXpnRTdY42INPSRBzLQ+YMi3ZYGQ9IYMQ7xUxzJ5sP91Tl9mxn8sndw6wucWSKupVtPQeJqU3rn/HrAqGqllTSVAc/2awUFwsrHIgdB04t89/1O/w1cDnyilFU=';

$request = file_get_contents('php://input');
$request_json = json_decode($request, true);

foreach ($request_json['events'] as $event) {
    // Extract the user and message information
    $user = $event['source']['userId'];
    $message = $event['message']['text'];

    // Log the message to Google Apps Script
    logMessageToGoogleAppsScript($user, $message);

    // Create a reply message
    $reply_message = 'ฉันได้รับข้อความ "'.$message.'" ของคุณแล้ว!';

    // Send the reply message
    reply_message($event['replyToken'], $reply_message);
}

function logMessageToGoogleAppsScript($user, $message) {
    // Set up the URL of your Google Apps Script
    $gas_url = 'https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec';

    // Prepare the data to send to Google Apps Script
    $data = array(
        'user' => $user,
        'message' => $message
    );

    // Send a POST request to Google Apps Script to log the message
    $ch = curl_init($gas_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
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
