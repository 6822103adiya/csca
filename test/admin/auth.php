<?php
/**
 * Admin Authentication Check
 */

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Verify admin still exists and is active
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT is_active FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
    
    if (!$admin || !$admin['is_active']) {
        session_destroy();
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Auth check error: " . $e->getMessage());
}

