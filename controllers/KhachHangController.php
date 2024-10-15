<?php
require_once '../models/KhachHang.php';
require_once '../config/Database.php';

class KhachHangController {
    private $khachHang;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->khachHang = new KhachHang($db);
    }

    // Lấy danh sách tất cả khách hàng
    public function getAll() {
        $result = $this->khachHang->getAll();
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }

    // Tạo mới khách hàng
    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        $this->khachHang->ten = $data->ten;
        $this->khachHang->sdt = $data->sdt;

        if ($this->khachHang->create()) {
            echo json_encode(["message" => "Tạo khách hàng thành công"]);
        } else {
            echo json_encode(["message" => "Tạo khách hàng thất bại"]);
        }
    }

    // Cập nhật khách hàng
    public function update() {
        $data = json_decode(file_get_contents("php://input"));

        $this->khachHang->id = $data->id;
        $this->khachHang->ten = $data->ten;
        $this->khachHang->sdt = $data->sdt;

        if ($this->khachHang->update()) {
            echo json_encode(["message" => "Cập nhật khách hàng thành công"]);
        } else {
            echo json_encode(["message" => "Cập nhật khách hàng thất bại"]);
        }
    }

    // Xóa khách hàng
    public function delete() {
        $data = json_decode(file_get_contents("php://input"));

        $this->khachHang->id = $data->id;

        if ($this->khachHang->delete()) {
            echo json_encode(["message" => "Xóa khách hàng thành công"]);
        } else {
            echo json_encode(["message" => "Xóa khách hàng thất bại"]);
        }
    }
}
?>
