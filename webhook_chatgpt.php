<?php

// Get the request payload
$payload = file_get_contents('php://input');

// Send a response back (optional)
http_response_code(200);
echo "Webhook received successfully.";

?>
