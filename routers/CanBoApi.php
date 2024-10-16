<?php

require_once __DIR__ . '/../models/CanBo.php';
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();
$canBo = new CanBo($conn);

// Trong phần xử lý API
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'canbo') !== false) {
    if (isset($_GET['canbo_id'])) {
        $canbo_id = intval($_GET['canbo_id']); // Chuyển đổi giá trị thành số nguyên
        
        // Gọi hàm lấy cán bộ theo id từ model
        $canBos = $canBo->getCanBoById($canbo_id);
       
        

       
     
        // Kiểm tra xem có cán bộ nào không
        if ($canBos) {
            // Trả về dữ liệu JSON
            header('Content-Type: application/json');
            echo json_encode($canBos);
        } else {
            // Không tìm thấy cán bộ
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Không tìm thấy cán bộ."));
        }
    } else {
        // Nếu không có id
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Vui lòng cung cấp id."));
    }
    exit;
}


// Các phương thức khác như POST, PUT, DELETE cũng có thể được thêm vào đây.
?>
