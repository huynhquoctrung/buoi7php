<?php
session_start();

require_once 'configconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'admin') {
            header("Location: manage_employees.php"); // Chuyển hướng đến trang quản lý nhân viên
            exit();
        } elseif ($row['role'] == 'user') {
            header("Location: nhanvien.php"); // Chuyển hướng đến trang nhân viên
            exit();
        } else {
            // Xử lý cho các vai trò khác (nếu có)
        }
    } else {
        // Hiển thị thông báo lỗi nếu tên đăng nhập hoặc mật khẩu không đúng
    }
}
?>
