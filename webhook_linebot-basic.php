<?php

$channelAccessToken = '5EThX6Vk3J1vG2loJYE3sBL+2RIPhStAsGNsWl60p+5OUJxMArXpnRTdY42INPSRBzLQ+YMi3ZYGQ9IYMQ7xUxzJ5sP91Tl9mxn8sndw6wucWSKupVtPQeJqU3rn/HrAqGqllTSVAc/2awUFwsrHIgdB04t89/1O/w1cDnyilFU=';

$request = file_get_contents('php://input');
$request_json = json_decode($request, true);

foreach ($request_json['events'] as $event) {
    $reply_message = 'ฉันได้รับ Event "'.$event['type'].'" ของคุณแล้ว!';

    if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
        $text = $event['message']['text'];
        $reply_message = 'ฉันได้รับข้อความ "'.$text.'" ของคุณแล้ว!';
    }

    reply_message($reply_message);
}

function reply_message($reply_message) {
    global $channelAccessToken;
    $url = 'https://api.line.me/v2/bot/message/reply';

    $data = ['replyToken' => $event['replyToken'], 'messages' => [['type' => 'text', 'text' => $reply_message]]];
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
