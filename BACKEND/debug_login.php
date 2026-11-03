<?php
session_start();
echo "<h2>🔍 Debug Login & Session</h2>";

echo "<h3>1. Session hiện tại:</h3><pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>2. Test kết nối DB (config/db.php):</h3>";
require_once 'config/db.php';
if ($conn->connect_error) {
    echo "<span style='color:red'>❌ Lỗi DB: " . $conn->connect_error . "</span>";
} else {
    echo "<span style='color:green'>✅ DB kết nối OK</span>";
}

echo "<h3>3. Thông tin user admin trong DB:</h3><pre>";
$result = $conn->query("SELECT id, username, password, role FROM users WHERE role='admin' LIMIT 3");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Username: " . $row['username'] . "\n";
        echo "Hash: " . $row['password'] . "\n";
        echo "Role: " . $row['role'] . "\n";
        echo "Verify 'admin123': ";
        var_dump(password_verify('admin123', $row['password']));
        echo "Verify 'password': ";
        var_dump(password_verify('password', $row['password']));
        echo "---\n";
    }
} else {
    echo "Không tìm thấy admin!\n";
}
echo "</pre>";

echo "<h3>4. Test kết nối DB (api/config/database.php):</h3>";
// Không include database.php vì nó gọi header() - kiểm tra thủ công
$host2 = "localhost"; $user2 = "root"; $pass2 = ""; $db2 = "web_ban_hang";
$conn2 = new mysqli($host2, $user2, $pass2, $db2);
if ($conn2->connect_error) {
    echo "<span style='color:red'>❌ Lỗi: " . $conn2->connect_error . "</span>";
} else {
    echo "<span style='color:green'>✅ OK (localhost, root, rỗng, port 3306)</span>";
}

echo "<h3>5. PHP Session save path:</h3>";
echo session_save_path();
?>

