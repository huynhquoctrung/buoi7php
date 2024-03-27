<?php
session_start();

// Nếu người dùng đã đăng nhập, chuyển hướng đến trang tương ứng với vai trò
if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: manage_employees.php");
    } elseif ($_SESSION['role'] == 'user') {
        header("Location: nhanvien.php");
    }
    exit();
}

require_once 'configconnect.php';

// Khởi tạo biến lưu trữ thông báo lỗi
$error_message = '';

// Xử lý dữ liệu khi người dùng nhấn nút Đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Lấy dữ liệu từ form đăng ký
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = 'user'; // Mặc định cho vai trò là người dùng

    // Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $sql_check = "SELECT * FROM user WHERE username='$username'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        $error_message = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
    } else {
        // Chèn dữ liệu vào cơ sở dữ liệu
        $sql_insert = "INSERT INTO user (username, password, fullname, email, role) VALUES ('$username', '$password', '$fullname', '$email', '$role')";
        if ($conn->query($sql_insert) === TRUE) {
            // Đăng ký thành công, chuyển hướng đến trang đăng nhập
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại sau.";
        }
    }
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
</head>
<body>

<h2>Đăng ký tài khoản</h2>
<?php
if (!empty($error_message)) {
    echo "<p style='color: red;'>$error_message</p>";
}
?>
<form method="post">
    <label for="username">Tên đăng nhập:</label>
    <input type="text" name="username" required><br>
    <label for="password">Mật khẩu:</label>
    <input type="password" name="password" required><br>
    <label for="fullname">Họ và tên:</label>
    <input type="text" name="fullname" required><br>
    <label for="email">Email:</label>
    <input type="email" name="email" required><br>
    <input type="submit" name="register" value="Đăng ký">
</form>

<p>Nếu bạn đã có tài khoản, vui lòng <a href="login.php">đăng nhập</a>.</p>

</body>
</html>
