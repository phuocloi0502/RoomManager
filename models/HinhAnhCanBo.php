<?php
 class HinhAnhCanBo {
    private $conn;
    private $table_name = "hinh_anh_can_bo";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy đường dẫn hình ảnh hiện tại của cán bộ theo ID
    public function getImagePathByCanBoId($canbo_id) {
        $query = "SELECT url FROM " . $this->table_name . " WHERE canbo_id = :canbo_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':canbo_id', $canbo_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['url']; // Trả về đường dẫn hình ảnh
        }
        return null; // Không tìm thấy hình ảnh
    }

    // Cập nhật đường dẫn hình ảnh của cán bộ
    public function updateImage($canbo_id, $url) {
        $query = "UPDATE " . $this->table_name . " SET url = :url WHERE canbo_id = :canbo_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':canbo_id', $canbo_id);

        if ($stmt->execute()) {
            return true; // Cập nhật thành công
        }
        return false; // Cập nhật thất bại
    }

    // Thêm hình ảnh mới cho cán bộ
    public function addImage($canbo_id, $url) {
        $query = "INSERT INTO " . $this->table_name . " (canbo_id, url) VALUES (:canbo_id, :url)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':canbo_id', $canbo_id);
        $stmt->bindParam(':url', $url);

        if ($stmt->execute()) {
            return true; // Thêm thành công
        }
        return false; // Thêm thất bại
    }
}
