<?php
require_once __DIR__ . '/../models/HinhAnhCanBo.php';
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$conn = $database->getConnection();
$hinhAnhCanBo = new HinhAnhCanBo($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], 'canbo/them-hinh-anh-can-bo') !== false) {
    header('Content-Type: application/json');

    if (isset($_FILES['hinh_anh']) && isset($_POST['canbo_id'])) {
        $canbo_id = $_POST['canbo_id'];
        $hinh_anh = $_FILES['hinh_anh'];

        // Tạo thư mục nếu chưa tồn tại
        $target_dir = "uploads/images/hinh-anh-can-bo/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Lấy đường dẫn hình ảnh hiện tại (nếu có)
        $existingImage = $hinhAnhCanBo->getImagePathByCanBoId($canbo_id); // Bạn cần định nghĩa hàm này trong model HinhAnhCanBo

        if ($existingImage) {
            // Nếu cán bộ đã có hình, cập nhật hình ảnh
            $fileName = "canbo_" . uniqid() . '.' . pathinfo($hinh_anh['name'], PATHINFO_EXTENSION);
            $target_file = $target_dir . $fileName;

            // Xóa hình ảnh cũ nếu tồn tại
            if (file_exists($existingImage)) {
                unlink($existingImage);
            }
        } else {
            // Nếu cán bộ chưa có hình, thêm mới
            $fileName = "canbo_" . uniqid() . '.' . pathinfo($hinh_anh['name'], PATHINFO_EXTENSION);
            $target_file = $target_dir . $fileName;
        }

        // Di chuyển tệp được tải lên
        if (move_uploaded_file($hinh_anh['tmp_name'], $target_file)) {
            // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
            if ($existingImage) {
                // Cập nhật hình ảnh
                if ($hinhAnhCanBo->updateImage($canbo_id, $target_file)) { // Định nghĩa hàm này trong model HinhAnhCanBo
                    echo json_encode(array("message" => "Cập nhật hình ảnh cán bộ thành công!", "url" => $target_file));
                } else {
                    echo json_encode(array("message" => "Có lỗi khi cập nhật hình ảnh."));
                }
            } else {
                // Thêm hình ảnh mới
                if ($hinhAnhCanBo->addImage($canbo_id, $target_file)) { // Định nghĩa hàm này trong model HinhAnhCanBo
                    echo json_encode(array("message" => "Thêm hình ảnh cán bộ thành công!", "url" => $target_file));
                } else {
                    echo json_encode(array("message" => "Có lỗi khi thêm hình ảnh."));
                }
            }
        } else {
            echo json_encode(array("message" => "Có lỗi khi tải lên hình ảnh."));
        }
    } else {
        echo json_encode(array("message" => "Thiếu dữ liệu cần thiết."));
    }
}
