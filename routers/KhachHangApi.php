<?php
require_once __DIR__ . '/../models/KhachHang.php';

$khachHang = new KhachHang($conn);

// Xử lý yêu cầu GET cho Khách Hàng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'khachhang') !== false) {
    header('Content-Type: application/json'); // Đặt header cho JSON
    $stmt = $khachHang->getAll();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($customers);
    exit;
}

// Các phương thức khác như POST, PUT, DELETE cũng có thể được thêm vào đây.
?>
