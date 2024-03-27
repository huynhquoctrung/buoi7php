<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); // Chuyển hướng người dùng không phải là admin đến trang đăng nhập
    exit();
}

require_once 'configconnect.php';

// Xử lý thêm nhân viên
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
    $ma_nv = $_POST['ma_nv'];
    $ten_nv = $_POST['ten_nv'];
    $phai = $_POST['phai'];
    $noi_sinh = $_POST['noi_sinh'];
    $ma_phong = $_POST['ma_phong'];
    $luong = $_POST['luong'];

    // Thực hiện truy vấn để thêm nhân viên mới vào CSDL
    $sql_add_employee = "INSERT INTO NHANVIEN (Ma_NV, Ten_NV, Phai, Noi_Sinh, Ma_Phong, Luong) 
                         VALUES (?, ?, ?, ?, ?, ?)";

    // Chuẩn bị và thực thi truy vấn sử dụng prepared statement để tránh lỗi SQL injection
    $stmt = $conn->prepare($sql_add_employee);
    $stmt->bind_param("ssssss", $ma_nv, $ten_nv, $phai, $noi_sinh, $ma_phong, $luong);

    if ($stmt->execute()) {
        echo "Thêm nhân viên thành công";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    // Đóng prepared statement
    $stmt->close();
}

// Xử lý xóa nhân viên
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_employee'])) {
    // Lấy mã nhân viên cần xóa từ tham số GET
    $delete_id = $_GET['delete_employee'];
    
    // Chuẩn bị truy vấn SQL để xóa nhân viên
    $sql_delete_employee = "DELETE FROM NHANVIEN WHERE Ma_NV = ?";
    
    // Chuẩn bị và thực thi truy vấn sử dụng prepared statement để tránh lỗi SQL injection
    $stmt = $conn->prepare($sql_delete_employee);
    $stmt->bind_param("s", $delete_id);
    
    if ($stmt->execute()) {
        echo "Xóa nhân viên thành công";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    // Đóng prepared statement
    $stmt->close();
}

// Xử lý sửa nhân viên
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit_employee'])) {
    $edit_id = $_GET['edit_employee'];

    // Redirect hoặc hiển thị form sửa thông tin nhân viên
    header("Location: edit_employee.php?edit_employee=" . $edit_id);
    exit();
}

// Xác định trang hiện tại
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

// Số nhân viên mỗi trang
$records_per_page = 5;

// Tính toán offset
$offset = ($page - 1) * $records_per_page;

// Truy vấn CSDL với giới hạn và phân trang
$sql = "SELECT * FROM NHANVIEN LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_destroy(); // Hủy bỏ tất cả các session
    header("Location: login.php"); // Chuyển hướng đến trang đăng nhập
    exit();
}

// Lấy tổng số nhân viên
$total_records = $conn->query("SELECT COUNT(*) AS total FROM NHANVIEN")->fetch_assoc()['total'];

// Tính toán số trang
$total_pages = ceil($total_records / $records_per_page);

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhân Viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
            display: block;
            margin: 10px auto;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            border: 2px solid #f00;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        img {
            width: 20px;
            height: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #007bff;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
            margin: 0 5px;
        }

        .pagination a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .logout-btn {
            text-align: center;
            margin-top: 20px
        }

.logout-btn input[type="submit"] {
    background-color: #dc3545;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.logout-btn input[type="submit"]:hover {
    background-color: #c82333;
}

@media only screen and (max-width: 600px) {
    table {
        font-size: 14px;
    }
}
</style>
</head>
<body>

<!-- Hiển thị form thêm nhân viên -->
<h2>Quản lý nhân viên</h2>
<form method="post">
<label for="ma_nv">Mã Nhân Viên:</label>
<input type="text" name="ma_nv" required><br>
<label for="ten_nv">Tên Nhân Viên:</label>
<input type="text" name="ten_nv" required><br>
<label for="phai">Giới tính:</label>
<select name="phai">
<option value="Nam">Nam</option>
<option value="Nữ">Nữ</option>
</select><br>
<label for="noi_sinh">Nơi Sinh:</label>
<input type="text" name="noi_sinh" required><br>
<label for="ma_phong">Mã Phòng:</label>
<input type="text" name="ma_phong" required><br>
<label for="luong">Lương:</label>
<input type="text" name="luong" required><br>
<input type="submit" name="add_employee" value="Thêm Nhân Viên">
</form>

<!-- Hiển thị danh sách nhân viên -->
<table>
<tr>
<th>Mã Nhân Viên</th>
<th>Tên Nhân Viên</th>
<th>Giới Tính</th>
<th>Nơi Sinh</th>
<th>Mã Phòng</th>
<th>Lương</th>
<th>Hành động</th>
</tr>
<?php
if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["Ma_NV"] . "</td>";
    echo "<td>" . $row["Ten_NV"] . "</td>";
    echo "<td>";
    // Kiểm tra giới tính để chèn hình ảnh tương ứng
    if ($row["Phai"] == "NU") {
        echo "<img src='woman.jpg' alt='Woman'>";
    } else {
        echo "<img src='man.jpg' alt='Man'>";
    }
    echo "</td>";
    echo "<td>" . $row["Noi_Sinh"] . "</td>";
    echo "<td>" . $row["Ma_Phong"] . "</td>";
    echo "<td>" . $row["Luong"] . "</td>";
    echo "<td><a href='manage_employees.php?delete_employee=" . $row["Ma_NV"] . "'>Xóa</a> | <a href='edit_employee.php?edit_employee=" . $row["Ma_NV"] . "'>Sửa</a></td>";
    echo "</tr>";
}
} else {
echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
}
?>
</table>

<!-- Hiển thị phân trang -->
<div class="pagination">
<?php
// Hiển thị link phân trang
for ($i = 1; $i <= $total_pages; $i++) {
echo "<a href='?page=$i'>$i</a> ";
}
?>
</div>

<!-- Nút đăng xuất -->
<div class="logout-btn">
<form method="post">
<input type="submit" name="logout" value="Đăng xuất">
</form>
</div>

</body>
</html>
