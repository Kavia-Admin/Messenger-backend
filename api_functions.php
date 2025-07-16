<?php
// ... assume preceding includes and auth remain unchanged

if($api == 'send_message') {
    $from = param('from');
    $to = param('to');
    $message = param('message');
    $type = param('type', 1);
    $data = param('data', '');
    $status = 0;
    $expires_in = param('expiry', null); // In seconds. Null = no expiry.
    $msgid = addMessage($from, $to, $message, $type, $data, $status, $expires_in);
    json_response(array('result'=> 'OK', 'message_id' => $msgid, 'expires_in_seconds' => $expires_in));
}

// Other endpoint logic remains unchanged

?>
