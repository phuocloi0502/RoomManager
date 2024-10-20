<?php
require_once __DIR__ . '/../models/Phong.php';
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();
$phong = new Phong($conn);

// File PhongApi.php - API lấy tất cả phòng
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'phong/all') !== false) {
    header('Content-Type: application/json'); // Đặt header cho JSON

    // Gọi hàm getAll để lấy tất cả các phòng
    $stmt = $phong->getAll();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy dữ liệu dưới dạng mảng

    // Trả về danh sách tất cả phòng
    echo json_encode($rooms);
    exit; // Thoát sau khi xử lý xong
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'phong/tang') !== false) {
    header('Content-Type: application/json'); // Đặt header cho JSON

    // Kiểm tra xem có tham số tang_id không
    if (isset($_GET['tang_id'])) {
        $tang_id = intval($_GET['tang_id']); // Chuyển đổi giá trị thành số nguyên

        // Gọi hàm lấy phòng theo tang_id từ model
        $stmt = $phong->getRoomByFloorId($tang_id);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy dữ liệu dưới dạng mảng

        // Kiểm tra xem có phòng nào không
        if (count($rooms) > 0) {
            // Trả về dữ liệu JSON
            echo json_encode($rooms);
        } else {
            // Không tìm thấy phòng
            echo json_encode(array("message" => "Không tìm thấy phòng nào ở tầng này."));
        }
    } else {
        // Nếu không có tang_id
        echo json_encode(array("message" => "Vui lòng cung cấp tang_id."));
    }
    exit; // Thoát sau khi xử lý xong
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'phong/id') !== false) {
    header('Content-Type: application/json'); // Đặt header cho JSON

    // Kiểm tra xem có tham số tang_id không
    if (isset($_GET['room_id'])) {
        $room_id = intval($_GET['room_id']); // Chuyển đổi giá trị thành số nguyên

        // Gọi hàm lấy phòng theo tang_id từ model
        $room = $phong->getRoomById($room_id);
      
        // Kiểm tra xem có phòng nào không
        if (count($room) > 0) {
            // Trả về dữ liệu JSON
            echo json_encode($room);
        } else {
            // Không tìm thấy phòng
            echo json_encode(array("message" => "Không tìm thấy phòng nào ."));
        }
    } else {
        // Nếu không có tang_id
        echo json_encode(array("message" => "Vui lòng cung cấp room_id."));
    }
    exit; // Thoát sau khi xử lý xong
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], 'phong/tra-phong') !== false) {
    header('Content-Type: application/json');

    // Lấy room_id từ query parameters
    parse_str($_SERVER['QUERY_STRING'], $queryParams);
    
    // Kiểm tra xem room_id có tồn tại trong query params không
    if (isset($queryParams['room_id'])) {
        $room_id = $queryParams['room_id']; // Lấy room_id từ query params

        // Gọi hàm trả phòng
        if ($phong->traPhong($room_id)) {
            echo json_encode(array("message" => "Trả phòng thành công!"));
        } else {
            echo json_encode(array("message" => "Có lỗi khi trả phòng."));
        }
    } else {
        // Nếu không có room_id trong query params
        echo json_encode(array("message" => "Dữ liệu không hợp lệ."));
    }
    exit; // Thoát sau khi xử lý xong
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], 'phong/dat-phong') !== false) {
    header('Content-Type: application/json');

    // Nhận dữ liệu từ body
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        // Lấy các thông tin từ body
        $ten_can_bo = $data['ten_can_bo'];
        $ngay_den = $data['ngay_den'];
        $ngay_di = $data['ngay_di'];
        $room_id = $data['room_id'];
        $selected_canbo_id = $data['selected_canbo_id'];
        // Gọi hàm thêm hoặc cập nhật cán bộ và phòng
        $response = $phong->datPhong( $ten_can_bo, $ngay_den, $ngay_di,$room_id,$selected_canbo_id);
        echo json_encode($response);
    } else {
        // Nếu không nhận được dữ liệu
        echo json_encode(array("message" => "Dữ liệu không hợp lệ."));
    }
    exit; // Thoát sau khi xử lý xong
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'phong/tim-kiem-can-bo') !== false) {
    header('Content-Type: application/json');

    // Nhận tham số tìm kiếm từ query string
    $ten_can_bo = isset($_GET['ten_can_bo']) ? $_GET['ten_can_bo'] : '';

    if ($ten_can_bo) {
        // Gọi hàm kiểm tra cán bộ tồn tại
        $can_bo_list = $phong->checkCanBoExists($ten_can_bo);

        if (!empty($can_bo_list)) {
            // Nếu có cán bộ phù hợp, trả về danh sách
            echo json_encode(array(
                "message" => "Danh sách cán bộ tìm thấy.",
                "can_bo_list" => $can_bo_list
            ));
        } else {
            // Không tìm thấy cán bộ
            echo json_encode(array(
                "message" => "Không tìm thấy cán bộ nào với tên đã cho."
            ));
        }
    } else {
        // Nếu không có tên cán bộ trong query string
        echo json_encode(array(
            "message" => "Vui lòng cung cấp tên cán bộ để tìm kiếm."
        ));
    }
    exit; // Thoát sau khi xử lý xong
}

?>
