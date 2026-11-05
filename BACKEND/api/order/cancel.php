<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/voucher.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$id=intval($_POST['order_id'] ?? 0);
$user_id=(int)$_SESSION['user_id'];

try{

    $conn->begin_transaction();

    $stmt=$conn->prepare("
    UPDATE orders
    SET status='Cancelled'
    WHERE id=?
    AND user_id=?
    AND status='Pending'
    ");

    $stmt->bind_param("ii",$id,$user_id);
    $stmt->execute();

    if($stmt->affected_rows==0){
        $conn->rollback();
        error("Order not found or cannot be canceled");
    }

    // Hoàn lại lượt dùng voucher (nếu đơn có dùng)
    voucher_release_usage($conn,$id);

    $conn->commit();

}catch(Throwable $e){

    $conn->rollback();
    error("Cancel failed",500);

}

success([],"Order canceled");
