<?php
// Hata raporlamayı aç (geçici - test için)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Functions.php'yi include et (database bağlantısı burada)
$functions_path = __DIR__ . '/includes/functions.php';

if (!file_exists($functions_path)) {
    http_response_code(500);
    die(json_encode([
        'success' => false, 
        'error' => 'Functions file not found',
        'path' => $functions_path,
        'dir' => __DIR__
    ]));
}

require_once $functions_path;

// JSON header
header('Content-Type: application/json');

try {
    // POST verilerini al
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Veri kontrolü
    if (!$data || !isset($data['click_type'])) {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid data', 
            'received' => $json_data
        ]);
        exit;
    }
    
    // Session başlat
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Session ID oluştur
    if (!isset($_SESSION['visitor_id'])) {
        $_SESSION['visitor_id'] = uniqid('visitor_', true);
    }
    
    // User agent bilgilerini al
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Basit browser/OS detection
    $browser = 'Unknown';
    $os = 'Unknown';
    $device_type = 'desktop';
    
    // Browser detection
    if (strpos($user_agent, 'Chrome') !== false) {
        $browser = 'Chrome';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        $browser = 'Safari';
    } elseif (strpos($user_agent, 'Firefox') !== false) {
        $browser = 'Firefox';
    } elseif (strpos($user_agent, 'Edge') !== false) {
        $browser = 'Edge';
    }
    
    // OS detection
    if (strpos($user_agent, 'Windows') !== false) {
        $os = 'Windows';
    } elseif (strpos($user_agent, 'Mac') !== false) {
        $os = 'MacOS';
    } elseif (strpos($user_agent, 'Linux') !== false) {
        $os = 'Linux';
    } elseif (strpos($user_agent, 'Android') !== false) {
        $os = 'Android';
    } elseif (strpos($user_agent, 'iOS') !== false) {
        $os = 'iOS';
    }
    
    // Device type detection
    if (preg_match('/mobile|android|iphone/i', $user_agent)) {
        $device_type = 'mobile';
    } elseif (preg_match('/tablet|ipad/i', $user_agent)) {
        $device_type = 'tablet';
    }
    
    // Database kontrolü
    if (!isset($db)) {
        throw new Exception('Database connection not available');
    }
    
    // Veritabanına kaydet
    $result = $db->query("
        INSERT INTO analytics (
            page_url, 
            click_type,
            ip_address, 
            user_agent, 
            browser, 
            os, 
            device_type, 
            referrer, 
            session_id,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ", [
        $data['page_url'] ?? '/',
        $data['click_type'],
        $_SERVER['REMOTE_ADDR'] ?? '',
        $user_agent,
        $browser,
        $os,
        $device_type,
        $_SERVER['HTTP_REFERER'] ?? '',
        $_SESSION['visitor_id']
    ]);
    
    echo json_encode([
        'success' => true,
        'click_type' => $data['click_type'],
        'message' => 'Tracking saved successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}