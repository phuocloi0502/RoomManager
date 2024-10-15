<?php
require_once __DIR__ . '/../models/DatPhong.php';

$datPhong = new DatPhong($conn);

// Xử lý yêu cầu GET cho Đặt Phòng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'datphong') !== false) {
    header('Content-Type: application/json'); // Đặt header cho JSON
    $stmt = $datPhong->getAll();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($bookings);
    exit;
}

// Các phương thức khác như POST, PUT, DELETE cũng có thể được thêm vào đây.
?>
