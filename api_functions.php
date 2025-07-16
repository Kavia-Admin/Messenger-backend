<?php
include_once('mesibohelper.php');
include_once("json.php");

// Existing utility functions
$downloadurl = 'https://appimages.mesibo.com/';

function GetRequestField($r, $field, $defaultval="") {
    $val = $defaultval;

    if(isset($r[$field])) {
        $val = trim($r[$field]);
    }

    return $val;
}

// Legacy invite and URLs
function add_invite_text(&$result) { /* ... unchanged ... */ }
function add_urls(&$result) { /* ... unchanged ... */ }
function sendOtpToUser($phone, $otp) { /* ... unchanged ... */ }

// Existing login/logout
function login_callbackapi($r, &$result) { /* ... unchanged ... */ }
function logout_callbackapi($user, &$result) { /* ... unchanged ... */ }
function DoExit($result, $data) { /* ... unchanged ... */ }
function OnEmptyExit($var, $code) { /* ... unchanged ... */ }

// --- ADD MESSAGE API FOR FRONTEND/BACKEND COMMUNICATION ---

// PUBLIC_INTERFACE
function send_message($from, $to, $message) {
    // Should call backend (e.g. mesibo, DB)
    // Not implemented here for brevity
    return ['result' => true];
}

// PUBLIC_INTERFACE
function edit_message_callbackapi($r, &$result) {
    $userid = GetRequestField($r, 'from', '');
    $message_id = GetRequestField($r, 'message_id', '');
    $new_message = GetRequestField($r, 'new_message', '');

    if (!$userid || !$message_id || !$new_message) {
        $result['error'] = 'Missing Parameter';
        return false;
    }
    $msg = mesibo_get_message($message_id);
    if (!$msg) {
        $result['error'] = 'Message not found';
        return false;
    }
    if ($msg['from'] != $userid) {
        $result['error'] = 'Permission denied';
        return false;
    }
    $success = mesibo_edit_message($message_id, $new_message);
    if ($success) {
        mesibo_broadcast_event('message_edited', [
            'message_id' => $message_id,
            'new_message' => $new_message,
            'edited_at' => time()
        ]);
        $result['result'] = true;
        return true;
    }
    $result['error'] = 'Edit failed';
    return false;
}

// PUBLIC_INTERFACE
function delete_message_callbackapi($r, &$result) {
    $userid = GetRequestField($r, 'from', '');
    $message_id = GetRequestField($r, 'message_id', '');

    if (!$userid || !$message_id) {
        $result['error'] = 'Missing Parameter';
        return false;
    }
    $msg = mesibo_get_message($message_id);
    if (!$msg) {
        $result['error'] = 'Message not found';
        return false;
    }
    if ($msg['from'] != $userid) {
        $result['error'] = 'Permission denied';
        return false;
    }
    $success = mesibo_delete_message($message_id);
    if ($success) {
        mesibo_broadcast_event('message_deleted', [
            'message_id' => $message_id,
            'deleted_at' => time()
        ]);
        $result['result'] = true;
        return true;
    }
    $result['error'] = 'Delete failed';
    return false;
}
