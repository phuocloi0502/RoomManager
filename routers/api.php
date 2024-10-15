<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/KhachHang.php';
require_once __DIR__ . '/../models/Phong.php';
require_once __DIR__ . '/../models/DatPhong.php';

$database = new Database();
$db = $database->getConnection();

$khachHang = new KhachHang($db);
$phong = new Phong($db);
$datPhong = new DatPhong($db);

// Xử lý yêu cầu cho Khách Hàng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'khachhang') !== false) {
    header('Content-Type: application/json');

    $stmt = $khachHang->getAll();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($customers);
    exit;
}

// Xử lý yêu cầu cho Phòng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'phong') !== false) {
    header('Content-Type: application/json');
    $stmt = $phong->getAll();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rooms);
    exit;
}

// Xử lý yêu cầu cho Đặt Phòng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'datphong') !== false) {
    header('Content-Type: application/json');
    $stmt = $datPhong->getAll();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reservations);
    exit;
}

// Các phương thức POST, PUT, DELETE cho từng model...
?>
