<?php
class Database {
    
    private $host = '127.0.0.1';
    private $db_name = 'Hotel';
    private $username = 'root';
    private $password = '123456';
    private $port = '3307'; 
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Tạo kết nối PDO
            $this->conn = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->db_name}", $this->username, $this->password);
            // Thiết lập chế độ báo lỗi
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Nếu có lỗi xảy ra, thông báo lỗi
            echo "Kết nối thất bại: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
