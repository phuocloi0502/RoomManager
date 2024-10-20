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
public function checkCanBoExists($ten_can_bo) {
    // Tìm kiếm cán bộ theo tên
    $sql = "
        SELECT cb.*, ha.url AS avatar_url 
        FROM can_bo cb
        LEFT JOIN hinh_anh_can_bo ha ON cb.id = ha.canbo_id
        WHERE cb.ten_can_bo LIKE :ten_can_bo
    ";
    $stmt = $this->conn->prepare($sql);
    $ten_can_bo = "%$ten_can_bo%";
    $stmt->bindParam(':ten_can_bo', $ten_can_bo, PDO::PARAM_STR);
    $stmt->execute();
    
    // Trả về danh sách cán bộ trùng tên
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function CreateCanBo($ten_can_bo, $ngay_den, $ngay_di) {

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

public function updateCanBo($id, $ten_can_bo, $ngay_den, $ngay_di) {
    // Kiểm tra ID cán bộ hợp lệ
    if (empty($id) || !is_numeric($id)) {
        return array("message" => "ID cán bộ không hợp lệ.");
    }

    // Câu lệnh SQL để cập nhật thông tin cán bộ
    $sql = "UPDATE can_bo SET ten_can_bo = :ten_can_bo, ngay_den = :ngay_den, ngay_di = :ngay_di WHERE id = :id";
    $stmt = $this->conn->prepare($sql);

    // Gán giá trị cho các tham số
    $stmt->bindParam(':ten_can_bo', $ten_can_bo, PDO::PARAM_STR);
    $stmt->bindParam(':ngay_den', $ngay_den, PDO::PARAM_STR);
    $stmt->bindParam(':ngay_di', $ngay_di, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        return array("message" => "Cập nhật thông tin cán bộ thành công.");
    } else {
        return array("message" => "Có lỗi khi cập nhật thông tin cán bộ.");
    }
}


public function datPhong($ten_can_bo, $ngay_den, $ngay_di, $room_id,$selected_canbo_id) {
    // Kiểm tra xem có cán bộ nào trùng tên trong CSDL không
    $can_bo_list = $this->checkCanBoExists($ten_can_bo);

    if (empty($can_bo_list)) {
        // Trường hợp 1: Không có cán bộ trong CSDL
        // Thêm mới cán bộ vào CSDL
        $canbo_id = $this->CreateCanBo($ten_can_bo, $ngay_den, $ngay_di);
        $this->updatePhong($room_id, $canbo_id);
        
        return array(
            "message" => "Thêm mới cán bộ và cập nhật phòng thành công.",
            "canbo_id" => $canbo_id
        );
    } else {
     // Trường hợp 3: Có nhiều cán bộ trùng tên
if ($selected_canbo_id !== null) {
    // Nếu người dùng đã chọn cán bộ từ danh sách
    $canbo_id = $selected_canbo_id;

    // Cập nhật thông tin cho cán bộ đã chọn
    $this->updateCanBo($canbo_id, $ten_can_bo, $ngay_den, $ngay_di);
    $this->updatePhong($room_id, $canbo_id);

    return array(
        "message" => "Thông tin cán bộ đã được cập nhật và đã cập nhật phòng.",
        "canbo_id" => $canbo_id
    );
} else {

            
            // Thêm mới cán bộ vào CSDL
            $canbo_id = $this->CreateCanBo($ten_can_bo, $ngay_den, $ngay_di);
            $this->updatePhong($room_id, $canbo_id);

            return array(
                "message" => "Cán bộ không đúng, đã thêm mới cán bộ và cập nhật phòng thành công.",
                "canbo_id" => $canbo_id
            );
        }
    } 
}






public function traPhong($room_id) {
    // Cập nhật trạng thái phòng và xóa can_bo_id
    $sql = "UPDATE phong_can_bo SET canbo_id = NULL, status = 'VACANT' WHERE id = :room_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':room_id', $room_id);
    
    // Kiểm tra xem quá trình cập nhật có thành công hay không
    if ($stmt->execute()) {
        return array(
            "message" => "Phòng đã được trả thành công.",
            "room_id" => $room_id
        );
    } else {
        return array(
            "message" => "Có lỗi xảy ra khi trả phòng.",
            "room_id" => $room_id
        );
    }
}


}
?>
