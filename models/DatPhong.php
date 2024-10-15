<?php
class DatPhong {
    private $conn;
    private $table_name = "dat_phong";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy tất cả đặt phòng
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Các hàm CRUD khác sẽ tương tự như model KhachHang
}
?>
