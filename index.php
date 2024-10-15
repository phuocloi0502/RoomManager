<?php
// Thiết lập chế độ hiển thị lỗi (chỉ cho môi trường phát triển)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lấy phần URL để điều hướng
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Tách URL thành các phần
$parts = explode('/', trim($requestUri, '/'));

// Kiểm tra xem phần đầu tiên có phải là "RoomManager"
if (count($parts) > 1 && $parts[0] === 'RoomManager') {
    // Lấy tên model từ URL
    $model = strtolower($parts[1]);
    
    // Tải file API tương ứng
    switch ($model) {
        case 'khachhang':
            require_once __DIR__ . '/routers/api.php';
            // Tạo các API cho Khách Hàng
            break;

        case 'phong':
            require_once __DIR__ . '/routers/api.php';
            // Tạo các API cho Phòng
            break;

        case 'datphong':
            require_once __DIR__ . '/routers/api.php';
            // Tạo các API cho Đặt Phòng
            break;

        default:
            http_response_code(404);
            echo json_encode(["message" => "Not Found"]);
            break;
    }
} else {
    // Nếu không đúng, trả về thông báo lỗi
    http_response_code(404);
    echo json_encode(["message" => "Not Found"]);
}
?>
