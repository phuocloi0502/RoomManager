<?php
class Phong {
    private $conn;
    private $table_name = "phong";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy tất cả phòng
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Các hàm CRUD khác như create, update, delete sẽ tương tự như model KhachHang
}
?>
