<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Thay username bằng tên đăng nhập của bạn
$password = ""; // Thay password bằng mật khẩu của bạn
$dbname = "ql_nhansu"; // Thay tên cơ sở dữ liệu nếu cần

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

?>
