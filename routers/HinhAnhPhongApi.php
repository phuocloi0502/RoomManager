<?php
require_once __DIR__ . '/../models/HinhAnhPhong.php';
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$conn = $database->getConnection();
$hinhAnhPhong = new HinhAnhPhong($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], 'phong/them-hinh-anh-phong') !== false) {
    header('Content-Type: application/json');

    if (isset($_FILES['hinh_anh']) && isset($_POST['phong_id'])) {
        $phong_id = $_POST['phong_id'];
        $hinh_anh = $_FILES['hinh_anh'];

        // Kiểm tra nếu đây là mảng nhiều tệp hoặc chỉ một tệp
        $fileCount = is_array($hinh_anh['name']) ? count($hinh_anh['name']) : 1;

        $uploadedImages = [];

        // Tạo thư mục nếu chưa tồn tại
        $target_dir = "uploads/images/hinh-anh-phong/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = is_array($hinh_anh['name']) ? $hinh_anh['name'][$i] : $hinh_anh['name'];
            $fileTmpName = is_array($hinh_anh['tmp_name']) ? $hinh_anh['tmp_name'][$i] : $hinh_anh['tmp_name'];

            // Tạo tên file mới với timestamp
            $timestamp = time(); // Lấy timestamp hiện tại
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Lấy phần mở rộng
            $newFileName = "hinh_anh_" . $timestamp . "_" . uniqid() . "." . $fileExtension; // Tạo tên file mới
            $target_file = $target_dir . basename($newFileName); // Đường dẫn file mới

            // Kiểm tra định dạng tệp
            $validExtensions = array("jpg", "jpeg", "png", "gif");
            if (in_array($fileExtension, $validExtensions)) {
                if (move_uploaded_file($fileTmpName, $target_file)) {
                    if ($hinhAnhPhong->themHinhAnhPhong($phong_id, $target_file)) {
                        $uploadedImages[] = $target_file;
                    } else {
                        echo json_encode(array("message" => "Có lỗi khi lưu thông tin hình ảnh vào cơ sở dữ liệu."));
                        exit;
                    }
                } else {
                    echo json_encode(array("message" => "Có lỗi khi tải lên tệp."));
                    exit;
                }
            } else {
                echo json_encode(array("message" => "Định dạng tệp không hợp lệ."));
                exit;
            }
        }

        echo json_encode(array("message" => "Tải lên hình ảnh thành công!", "uploaded_images" => $uploadedImages));
    } else {
        echo json_encode(array("message" => "Thiếu dữ liệu cần thiết."));
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && strpos($_SERVER['REQUEST_URI'], 'phong/xoa-hinh-anh-phong') !== false) {
    header('Content-Type: application/json');


    if (isset($_GET['image_id'])) {
        $image_id = intval($_GET['image_id']); // Lấy ID ảnh từ URL
        
        // Lấy đường dẫn hình ảnh từ cơ sở dữ liệu
        $imagePath = $hinhAnhPhong->getImagePathById($image_id); // Định nghĩa hàm này trong model HinhAnhPhong

        if ($imagePath) {
            // Xóa hình ảnh từ hệ thống tệp
            if (file_exists($imagePath)) {
                if (unlink($imagePath)) {
                    // Xóa hình ảnh khỏi cơ sở dữ liệu
                    if ($hinhAnhPhong->deleteImagePhong($image_id)) { // Định nghĩa hàm này trong model HinhAnhPhong
                        echo json_encode(array("message" => "Xóa hình ảnh thành công!"));
                    } else {
                        echo json_encode(array("message" => "Có lỗi khi xóa hình ảnh khỏi cơ sở dữ liệu."));
                    }
                } else {
                    echo json_encode(array("message" => "Có lỗi khi xóa hình ảnh từ hệ thống tệp."));
                }
            } else {
                echo json_encode(array("message" => "Không tìm thấy hình ảnh trên hệ thống tệp."));
            }
        } else {
            echo json_encode(array("message" => "Không tìm thấy hình ảnh với ID này."));
        }
    } else {
        echo json_encode(array("message" => "Dữ liệu không hợp lệ."));
    }
    exit; // Thoát sau khi xử lý xong
}

?>
