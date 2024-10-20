<?php

 class HinhAnhPhong {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function themHinhAnhPhong($phong_id, $url) {
        $sql = "INSERT INTO hinh_anh_phong (phong_id, url) VALUES (:phong_id, :url)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':phong_id', $phong_id);
        $stmt->bindParam(':url', $url);
        return $stmt->execute();
    }
    // Hàm lấy đường dẫn hình ảnh theo ID
    public function getImagePathById($id) {
        $query = "SELECT url FROM hinh_anh_phong WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['url'];
        }
        return null; // Nếu không tìm thấy
    }
     // Hàm xóa hình ảnh khỏi cơ sở dữ liệu
     public function deleteImagePhong($id) {
        $query = "DELETE FROM hinh_anh_phong WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
