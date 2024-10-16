<?php
class Phong {
    private $conn;
    private $table_name = "phong_can_bo";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy tất cả phòng
    public function getAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Hàm lấy phòng theo tang_id
    public function getRoomByFloorId($tang_id) {
        $sql = "SELECT p.*, c.ten_can_bo, c.ngay_den, c.ngay_di 
        FROM phong_can_bo p
        LEFT JOIN can_bo c ON p.canbo_id = c.id 
        WHERE p.tang_id = :tang_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tang_id', $tang_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
    public function getRoomById($room_id) {
        // Lấy thông tin phòng và cán bộ
        $sql = "SELECT p.*, c.ten_can_bo, c.ngay_den, c.ngay_di 
                FROM phong_can_bo p
                LEFT JOIN can_bo c ON p.canbo_id = c.id 
                WHERE p.id = :room_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC); // Lấy thông tin phòng
    
        // Nếu phòng không tồn tại, trả về null
        if (!$room) {
            return null;
        }
    
        // Lấy hình ảnh của phòng
        $room['hinh_anh_phong'] = $this->getHinhAnhPhong($room['id']); // Gọi hàm lấy hình ảnh
    
        return $room; // Trả về thông tin phòng kèm hình ảnh
    }
    
    


// Hàm lấy hình ảnh phòng theo phong_id
public function getHinhAnhPhong($phong_id) {
    $sql = "SELECT url FROM hinh_anh_phong WHERE phong_id = :room_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':room_id', $phong_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Lấy tất cả hình ảnh dưới dạng mảng
}
public function getOrCreateCanBo($ten_can_bo, $ngay_den, $ngay_di) {
    // Kiểm tra xem cán bộ đã tồn tại không
    $sql = "SELECT * FROM can_bo WHERE ten_can_bo = :ten_can_bo LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':ten_can_bo', $ten_can_bo);
    $stmt->execute();
    
    $canbo = $stmt->fetch(PDO::FETCH_ASSOC); // Lấy thông tin cán bộ

    // Nếu cán bộ đã tồn tại, trả về id của cán bộ
    if ($canbo) {
        return $canbo['id'];
    }

    // Nếu không, thêm mới cán bộ
    $sql = "INSERT INTO can_bo (ten_can_bo, ngay_den, ngay_di) VALUES (:ten_can_bo, :ngay_den, :ngay_di)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':ten_can_bo', $ten_can_bo);
    $stmt->bindParam(':ngay_den', $ngay_den);
    $stmt->bindParam(':ngay_di', $ngay_di);
    $stmt->execute();

    // Trả về id của cán bộ mới được thêm
    return $this->conn->lastInsertId();
}
public function updatePhong($room_id, $canbo_id) {
    $sql = "UPDATE phong_can_bo SET canbo_id = :canbo_id, status = 'OCCUPIED' WHERE id = :room_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':canbo_id', $canbo_id);
    $stmt->bindParam(':room_id', $room_id);
    return $stmt->execute(); // Trả về true nếu thành công
}
public function themHoacCapNhatCanBoVaPhong($room_id, $ten_can_bo, $ngay_den, $ngay_di) {
    // Lấy hoặc thêm mới cán bộ
    $canbo_id = $this->getOrCreateCanBo($ten_can_bo, $ngay_den, $ngay_di);
    
    // Cập nhật thông tin phòng
if ($this->updatePhong($room_id, $canbo_id)) {
    return array(
        "message" => "Cập nhật thông tin phòng thành công!",
        "canbo_id" => $canbo_id // Trả về ID cán bộ
    );
} else {
    return array("message" => "Có lỗi khi cập nhật thông tin phòng.");
}
}

public function traPhong($room_id) {
    // Cập nhật thông tin phòng khi trả phòng
    $sql = "UPDATE phong_can_bo SET canbo_id = NULL, status = 'VACANT' WHERE id = :room_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':room_id', $room_id);
    
    // Trả về true nếu thành công
    return $stmt->execute();
}

}
?>
