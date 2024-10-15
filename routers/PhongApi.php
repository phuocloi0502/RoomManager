<?php
require_once __DIR__ . '/../models/Phong.php';

$phong = new Phong($conn);

// Xử lý yêu cầu GET cho Phòng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'phong') !== false) {
    header('Content-Type: application/json'); // Đặt header cho JSON
    $stmt = $phong->getAll();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rooms);
    exit;
}

// Các phương thức khác như POST, PUT, DELETE cũng có thể được thêm vào đây.
?>
