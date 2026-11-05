<?php

require_once "../config/auth.php";
require_once "../config/database.php";
require_once "../config/response.php";
require_once "../helper/voucher.php";

// Bắt buộc mysqli ném exception để transaction rollback đúng khi có lỗi SQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if($_SERVER['REQUEST_METHOD']!="POST"){
    error("POST only");
}

if(empty($_SESSION['cart'])){
    error("Cart is empty");
}

$user_id=(int)$_SESSION['user_id'];
$payment=$_POST['payment_method'] ?? "COD";

if(!in_array($payment,["COD","Banking","Momo"],true)){
    error("Invalid payment method");
}

try{

    $conn->begin_transaction();

    // 1. Giá lấy từ DB, không tin client
    $cart=get_cart_items($conn);

    if(empty($cart['items'])){
        throw new RuntimeException("Cart is empty");
    }

    $subtotal=(float)$cart['subtotal'];
    $shipping=(float)SHIPPING_FEE;
    $discount=0;
    $voucher=null;

    // 2. Kiểm tra lại voucher (khóa dòng để tránh 2 đơn cùng lúc vượt lượt dùng)
    if(!empty($_SESSION['voucher'])){

        $voucher=find_voucher($conn,$_SESSION['voucher'],true);

        if(!$voucher){
            throw new VoucherException("Mã giảm giá không còn khả dụng");
        }

        $discount=voucher_check($conn,$voucher,$subtotal,$shipping,$user_id);
    }

    $total=max(0,$subtotal+$shipping-$discount);
    $voucherCode=$voucher ? $voucher['code'] : null;

    // 3. Tạo đơn hàng
    $stmt=$conn->prepare("
    INSERT INTO orders
    (user_id,total_money,shipping_fee,discount_amount,voucher_code,payment_method,status)
    VALUES(?,?,?,?,?,?,'Pending')
    ");

    $stmt->bind_param(
    "iddsss",
    $user_id,
    $total,
    $shipping,
    $discount,
    $voucherCode,
    $payment
    );

    $stmt->execute();

    $order_id=$conn->insert_id;

    // 4. Chi tiết đơn hàng
    $stmt=$conn->prepare("
    INSERT INTO order_details
    (order_id,product_id,quantity,price)
    VALUES(?,?,?,?)
    ");

    foreach($cart['items'] as $item){

        $pid=(int)$item['id'];
        $qty=(int)$item['quantity'];
        $price=(float)$item['price'];

        $stmt->bind_param("iiid",$order_id,$pid,$qty,$price);
        $stmt->execute();

    }

    // 5. Ghi nhận lượt dùng voucher
    if($voucher){

        $vid=(int)$voucher['id'];

        $stmt=$conn->prepare("
        INSERT INTO voucher_usages(voucher_id,user_id,order_id)
        VALUES(?,?,?)
        ");
        $stmt->bind_param("iii",$vid,$user_id,$order_id);
        $stmt->execute();

        $stmt=$conn->prepare("
        UPDATE vouchers SET used_count=used_count+1 WHERE id=?
        ");
        $stmt->bind_param("i",$vid);
        $stmt->execute();

    }

    $conn->commit();

}catch(VoucherException $e){

    $conn->rollback();
    unset($_SESSION['voucher']);   // mã không còn hợp lệ -> gỡ để lần sau đặt hàng được
    error($e->getMessage());

}catch(RuntimeException $e){

    $conn->rollback();
    error($e->getMessage());

}catch(Throwable $e){

    $conn->rollback();
    error("Create order failed",500);

}

unset($_SESSION['cart']);
unset($_SESSION['voucher']);

success([
    "order_id"=>$order_id,
    "subtotal"=>$subtotal,
    "shipping_fee"=>$shipping,
    "discount"=>$discount,
    "voucher_code"=>$voucherCode,
    "total"=>$total
],"Checkout success");
