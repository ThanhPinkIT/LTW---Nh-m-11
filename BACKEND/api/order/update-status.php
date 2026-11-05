<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/voucher.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if($_SESSION['role']!="admin"){
    error("Permission denied",403);
}

$id=intval($_POST['order_id'] ?? 0);

$status=$_POST['status'] ?? '';

$valid=['Pending','Processing','Shipping','Completed','Cancelled'];

if(!in_array($status,$valid,true)){
    error("Invalid status");
}

try{

    $conn->begin_transaction();

    $stmt=$conn->prepare("
    UPDATE orders
    SET status=?
    WHERE id=?
    ");

    $stmt->bind_param(
    "si",
    $status,
    $id
    );

    $stmt->execute();

    // Đơn bị hủy -> hoàn lại lượt dùng voucher
    if($status==="Cancelled"){
        voucher_release_usage($conn,$id);
    }

    $conn->commit();

}catch(Throwable $e){

    $conn->rollback();
    error("Update failed",500);

}

success([],"Status updated");
