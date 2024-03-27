<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Chuyển hướng người dùng không phải là admin đến trang đăng nhập
    exit();
}

// Kiểm tra nếu không có tham số 'edit_employee' được truyền qua URL
if (!isset($_GET['edit_employee'])) {
    header("Location: manage_employees.php"); // Chuyển hướng người dùng trở lại trang quản lý nhân viên nếu không có tham số 'edit_employee'
    exit();
}

require_once 'configconnect.php';

// Lấy mã nhân viên cần chỉnh sửa từ tham số 'edit_employee'
$edit_id = $_GET['edit_employee'];

// Kiểm tra xem có dữ liệu về nhân viên này trong CSDL không
$sql_select_employee = "SELECT * FROM NHANVIEN WHERE Ma_NV = '$edit_id'";
$result_select_employee = $conn->query($sql_select_employee);

if ($result_select_employee->num_rows == 0) {
    echo "Không tìm thấy nhân viên có mã: $edit_id"; // Hiển thị thông báo nếu không tìm thấy nhân viên
    exit();
}

// Lấy thông tin của nhân viên cần chỉnh sửa
$row = $result_select_employee->fetch_assoc();
$ma_nv = $row['Ma_NV'];
$ten_nv = $row['Ten_NV'];
$phai = $row['Phai'];
$noi_sinh = $row['Noi_Sinh'];
$ma_phong = $row['Ma_Phong'];
$luong = $row['Luong'];

// Xử lý yêu cầu chỉnh sửa thông tin nhân viên
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_employee'])) {
    // Lấy dữ liệu mới từ form
    $ten_nv_moi = $_POST['ten_nv'];
    $phai_moi = $_POST['phai'];
    $noi_sinh_moi = $_POST['noi_sinh'];
    $ma_phong_moi = $_POST['ma_phong'];
    $luong_moi = $_POST['luong'];

    // Cập nhật thông tin của nhân viên trong CSDL
    $sql_update_employee = "UPDATE NHANVIEN SET Ten_NV='$ten_nv_moi', Phai='$phai_moi', Noi_Sinh='$noi_sinh_moi', Ma_Phong='$ma_phong_moi', Luong='$luong_moi' WHERE Ma_NV='$edit_id'";
    if ($conn->query($sql_update_employee) === TRUE) {
        echo "Cập nhật thông tin nhân viên thành công";
    } else {
        echo "Lỗi khi cập nhật thông tin nhân viên: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin nhân viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Chỉnh sửa thông tin nhân viên</h2>
<form method="post">
    <label for="ma_nv">Mã Nhân Viên:</label>
    <input type="text" name="ma_nv" value="<?php echo $ma_nv; ?>" readonly>
    <label for="ten_nv">Tên Nhân Viên:</label>
    <input type="text" name="ten_nv" value="<?php echo $ten_nv; ?>" required>
    <label for="phai">Giới tính:</label>
    <select name="phai">
        <option value="Nam" <?php if ($phai == 'Nam') echo 'selected'; ?>>Nam</option>
        <option value="Nữ" <?php if ($phai == 'Nữ') echo 'selected'; ?>>Nữ</option>
    </select>
    <label for="noi_sinh">Nơi Sinh:</label>
    <input type="text" name="noi_sinh" value="<?php echo $noi_sinh; ?>" required>
    <label for="ma_phong">Mã Phòng:</label>
    <input type="text" name="ma_phong" value="<?php echo $ma_phong; ?>" required>
    <label for="luong">Lương:</label>
    <input type="text" name="luong" value="<?php echo $luong; ?>" required>
    <input type="submit" name="edit_employee" value="Lưu">
</form>

</body>
</html>
