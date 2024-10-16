<?php
class CanBo {
    private $conn;
    private $table_name = "can_bo";

    public function __construct($db) {
        $this->conn = $db;
    }



  // Hàm lấy cán bộ theo id
public function getCanBoById($canbo_id) {
    $sql = "SELECT c.*, p.ten_phong, p.loai_phong, p.tang_id, p.section, p.building_id
            FROM can_bo c
            LEFT JOIN phong_can_bo p ON c.id = p.canbo_id
            WHERE c.id = :canbo_id"; // Sử dụng placeholder

    $stmt = $this->conn->prepare($sql); // Chuẩn bị câu truy vấn
    $stmt->bindParam(':canbo_id', $canbo_id, PDO::PARAM_INT); // Ràng buộc biến
    $stmt->execute(); // Thực thi câu truy vấn
    $canBo = $stmt->fetch(PDO::FETCH_ASSOC); // Lấy thông tin can bo
     // Nếu can bo không tồn tại, trả về null
     if (!$canBo) {
        return null;
    }

    // Lấy hình ảnh của can bo
    $canBo['hinh_anh_can_bo'] = $this->getHinhAnhCanBo($canBo['id']); // Gọi hàm lấy hình ảnh

    return $canBo; // Trả về đối tượng statement
}
// Hàm lấy hình ảnh của cán bộ theo canbo_id
public function getHinhAnhCanBo($canbo_id) {
    $sql = "SELECT url FROM hinh_anh_can_bo WHERE canbo_id = :canbo_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':canbo_id', $canbo_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về tất cả hình ảnh
}


     
}
?>
