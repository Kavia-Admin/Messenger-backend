<?php

/**
 * PUBLIC_INTERFACE
 * Add a message to the database, with optional expiry time in seconds.
 *
 * @param string $from Sender
 * @param string $to Recipient
 * @param string $message Message text
 * @param int $type Message type
 * @param string $data Extra data
 * @param int $status Message status
 * @param int|null $expires_in_secs Optional seconds until expiry (NULL for no expiry)
 * @return int Message ID
 */
function addMessage($from, $to, $message, $type, $data = '', $status = 0, $expires_in_secs = null) {
    global $db;
    $expiry = null;
    if ($expires_in_secs !== null) {
        $expiry = time() + intval($expires_in_secs);
    }
    $stmt = $db->prepare("INSERT INTO messages(`from`, `to`, `message`, `type`, `data`, `status`, `expiry_ts`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$from, $to, $message, $type, $data, $status, $expiry]);
    return $db->lastInsertId();
}

/**
 * PUBLIC_INTERFACE
 * Remove expired messages from the database.
 * Should be called periodically before message fetches.
 */
function removeExpiredMessages() {
    global $db;
    $now = time();
    $stmt = $db->prepare("DELETE FROM messages WHERE expiry_ts IS NOT NULL AND expiry_ts <= ?");
    $stmt->execute([$now]);
}

/**
 * PUBLIC_INTERFACE
 * Fetch messages for a conversation, hiding expired ones.
 */
function getMessages($user1, $user2, $limit = 50, $offset = 0) {
    global $db;
    removeExpiredMessages();
    $now = time();
    $stmt = $db->prepare("SELECT * FROM messages WHERE ((`from` = ? AND `to` = ?) OR (`from` = ? AND `to` = ?)) AND (expiry_ts IS NULL OR expiry_ts > ?) ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->execute([$user1, $user2, $user2, $user1, $now, $limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
