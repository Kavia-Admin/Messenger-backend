<?php
require_once("api_functions.php");

// Add API endpoint handling here

$api = isset($_REQUEST['api']) ? $_REQUEST['api'] : null;

// Advanced Search APIs (public interface below)
if ($api === 'search_messages') {
    require_once 'mesibohelper.php';
    header('Content-Type: application/json');
    $query    = isset($_GET['query']) ? trim($_GET['query']) : '';
    $user_id  = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;
    $start_ts = isset($_GET['start_ts']) ? intval($_GET['start_ts']) : null;
    $end_ts   = isset($_GET['end_ts']) ? intval($_GET['end_ts']) : null;
    $type     = isset($_GET['type']) ? trim($_GET['type']) : null;

    $where = [];
    $params = [];
    if ($query !== '') {
        $where[] = '(message LIKE ?)';
        $params[] = '%' . $query . '%';
    }
    if ($user_id) {
        $where[] = '(from_user=? OR to_user=?)';
        $params[] = $user_id;
        $params[] = $user_id;
    }
    if ($group_id) {
        $where[] = '(group_id=?)';
        $params[] = $group_id;
    }
    if ($start_ts) {
        $where[] = '(ts>=?)';
        $params[] = $start_ts;
    }
    if ($end_ts) {
        $where[] = '(ts<=?)';
        $params[] = $end_ts;
    }
    if ($type) {
        $where[] = '(type=?)';
        $params[] = $type;
    }

    $sql = "SELECT * FROM messages";
    if (count($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY ts DESC LIMIT 100";

    $db = DbHelper::getInstance();
    $stm = $db->prepare($sql);
    $stm->execute($params);
    $messages = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['result' => 'OK', 'messages' => $messages]);
    exit;
}
if ($api === 'search_users') {
    require_once 'mesibohelper.php';
    header('Content-Type: application/json');
    $query   = isset($_GET['query']) ? trim($_GET['query']) : '';
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $phone   = isset($_GET['phone']) ? trim($_GET['phone']) : null;

    $where = [];
    $params = [];
    if ($query !== '') {
        $where[] = '(name LIKE ? OR phone LIKE ?)';
        $params[] = '%' . $query . '%';
        $params[] = '%' . $query . '%';
    }
    if ($user_id) {
        $where[] = '(id=?)';
        $params[] = $user_id;
    }
    if ($phone) {
        $where[] = '(phone=?)';
        $params[] = $phone;
    }
    $sql = "SELECT id, name, phone, status FROM users";
    if (count($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY name LIMIT 50";

    $db = DbHelper::getInstance();
    $stm = $db->prepare($sql);
    $stm->execute($params);
    $users = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['result' => 'OK', 'users' => $users]);
    exit;
}
if ($api === 'search_groups') {
    require_once 'mesibohelper.php';
    header('Content-Type: application/json');
    $query    = isset($_GET['query']) ? trim($_GET['query']) : '';
    $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;

    $where = [];
    $params = [];
    if ($query !== '') {
        $where[] = '(name LIKE ?)';
        $params[] = '%' . $query . '%';
    }
    if ($group_id) {
        $where[] = '(id=?)';
        $params[] = $group_id;
    }
    $sql = "SELECT id, name, type, members FROM groups";
    if (count($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY name LIMIT 50";

    $db = DbHelper::getInstance();
    $stm = $db->prepare($sql);
    $stm->execute($params);
    $groups = $stm->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['result' => 'OK', 'groups' => $groups]);
    exit;
}

// Existing API routing
switch($api) {
    case 'add_reaction':
        $result = api_add_reaction($_REQUEST);
        echo json_encode($result); exit;
    case 'remove_reaction':
        $result = api_remove_reaction($_REQUEST);
        echo json_encode($result); exit;
    case 'get_reactions':
        $result = api_get_reactions($_REQUEST);
        echo json_encode($result); exit;
    // Add further API routing below as needed...
    // For existing (edit_message, delete_message, etc.)
    default:
        // Existing dispatch logic (not shown)
        echo json_encode(['result' => 'error', 'error' => 'Unknown API']);
        exit;
}
?>
