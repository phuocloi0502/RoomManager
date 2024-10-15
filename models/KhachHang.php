<?php
class KhachHang {
    private $conn;
    private $table_name = "khach_hang";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy tất cả khách hàng
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Hàm thêm khách hàng mới
    public function create($ten, $sdt) {
        $query = "INSERT INTO " . $this->table_name . " (ten, sdt) VALUES (:ten, :sdt)";
        $stmt = $this->conn->prepare($query);
        
        // Binds
        $stmt->bindParam(':ten', $ten);
        $stmt->bindParam(':sdt', $sdt);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Hàm cập nhật thông tin khách hàng
    public function update($id, $ten, $sdt) {
        $query = "UPDATE " . $this->table_name . " SET ten = :ten, sdt = :sdt WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Binds
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ten', $ten);
        $stmt->bindParam(':sdt', $sdt);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Hàm xóa khách hàng
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
