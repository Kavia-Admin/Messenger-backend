<?php
// Mesibo Messenger - API Functions

// Add your API implementations here.

/**
 * API: add_reaction
 * Add a reaction (like/emoji) to a message by a user.
 * Params: from (user_id), message_id, reaction (string, e.g. emoji or type)
 */
// PUBLIC_INTERFACE
function api_add_reaction($params) {
    global $db;

    $from = isset($params['from']) ? intval($params['from']) : 0;
    $message_id = isset($params['message_id']) ? intval($params['message_id']) : 0;
    $reaction = isset($params['reaction']) ? trim($params['reaction']) : '';

    if (!$from || !$message_id || !$reaction) {
        return ['result' => 'error', 'error' => 'Missing parameters'];
    }

    // SQLite3: INSERT OR REPLACE (for uniqueness on message_id, user_id, reaction)
    $stmt = $db->prepare("INSERT OR REPLACE INTO message_reactions (message_id, user_id, reaction, created_ts) VALUES (?, ?, ?, strftime('%s', 'now'))");
    if (!$stmt->execute([$message_id, $from, $reaction])) {
        return ['result' => 'error', 'error' => 'DB error'];
    }

    // Real-time broadcast/broadcast placeholder
    broadcast_reaction_event('reaction_added', $message_id, $from, $reaction);

    return ['result' => 'success'];
}

/**
 * API: remove_reaction
 * Remove a reaction from a message by a user.
 * Params: from (user_id), message_id, reaction (string)
 */
// PUBLIC_INTERFACE
function api_remove_reaction($params) {
    global $db;

    $from = isset($params['from']) ? intval($params['from']) : 0;
    $message_id = isset($params['message_id']) ? intval($params['message_id']) : 0;
    $reaction = isset($params['reaction']) ? trim($params['reaction']) : '';

    if (!$from || !$message_id || !$reaction) {
        return ['result' => 'error', 'error' => 'Missing parameters'];
    }

    $stmt = $db->prepare("DELETE FROM message_reactions WHERE message_id=? AND user_id=? AND reaction=?");
    if (!$stmt->execute([$message_id, $from, $reaction])) {
        return ['result' => 'error', 'error' => 'DB error'];
    }

    broadcast_reaction_event('reaction_removed', $message_id, $from, $reaction);

    return ['result' => 'success'];
}

/**
 * API: get_reactions
 * Retrieve reactions for one or more messages (aggregated).
 * Params: message_id (can be comma-separated list or single)
 * Returns: { message_id: [ {reaction, count, users: [user_id,...]} ] }
 */
// PUBLIC_INTERFACE
function api_get_reactions($params) {
    global $db;

    $message_ids = [];
    if (isset($params['message_id'])) {
        if (is_array($params['message_id'])) $message_ids = $params['message_id'];
        else $message_ids = explode(',', $params['message_id']);
    }
    if (!count($message_ids)) {
        return ['result' => 'error', 'error' => 'No message_id(s) specified'];
    }

    // Build query
    $placeholders = implode(',', array_fill(0, count($message_ids), '?'));
    $sql = "SELECT message_id, reaction, user_id FROM message_reactions WHERE message_id IN ($placeholders)";
    $stmt = $db->prepare($sql);
    if (!$stmt->execute($message_ids)) return ['result' => 'error', 'error' => 'DB error'];

    $per_message = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mid = $row['message_id'];
        $reaction = $row['reaction'];
        $uid = $row['user_id'];
        if (!isset($per_message[$mid])) $per_message[$mid] = [];
        if (!isset($per_message[$mid][$reaction])) $per_message[$mid][$reaction] = ['reaction' => $reaction, 'count' => 0, 'users' => []];

        $per_message[$mid][$reaction]['count'] += 1;
        $per_message[$mid][$reaction]['users'][] = $uid;
    }
    // Convert to expected API response
    $result = [];
    foreach($per_message as $mid => $reactions) {
        $result[$mid] = array_values($reactions);
    }
    return ['result' => 'success', 'reactions' => $result];
}

/**
 * Internal function to broadcast real-time reaction updates.
 */
function broadcast_reaction_event($event, $message_id, $user_id, $reaction) {
    // Implement your real-time notification system here.
    // Placeholder only.
}
?>
