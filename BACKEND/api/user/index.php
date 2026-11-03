<?php

require_once "../middleware/admin.php";

$sql = "
SELECT
id,
username,
fullname,
email,
role,
created_at
FROM users
ORDER BY id DESC
";

$result = $conn->query($sql);

$users = [];

while($row = $result->fetch_assoc()){
    $users[] = $row;
}

response(
    true,
    "User list",
    $users
);