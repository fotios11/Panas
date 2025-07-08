<?php
session_start();

// Database Connection
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new PDO('sqlite:database.sqlite');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $db;
}

// Check Login Status
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Require User Login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Get Current User Data
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get User Balance
function getUserBalance($user_id) {
    $db = getDB();
    // Sum incomes - sum expenses + starting_balance
    $stmt = $db->prepare("
        SELECT
            (IFNULL((SELECT SUM(amount) FROM transactions WHERE user_id = ? AND type = 'income'), 0)
            - IFNULL((SELECT SUM(amount) FROM transactions WHERE user_id = ? AND type = 'expense'), 0)
            + IFNULL((SELECT starting_balance FROM users WHERE id = ?), 0)
            ) AS balance
    ");
    $stmt->execute([$user_id, $user_id, $user_id]);
    return $stmt->fetchColumn();
}
