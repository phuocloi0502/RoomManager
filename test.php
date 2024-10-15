<?php
require_once 'config/Database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "Kết nối đến cơ sở dữ liệu thành công!";
} else {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
}
?>
