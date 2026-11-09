<?php
require_once __DIR__ . '/../config/db.php';

function dashboard_data(mysqli $conn): array
{
    // doanh thu 30 ngày gần nhất (đơn Completed)
    $days = [];
    for ($i = 29; $i >= 0; $i--) {
        $k = date('Y-m-d', strtotime("-$i day"));
        $days[$k] = ['label' => date('d/m', strtotime($k)), 'revenue' => 0, 'orders' => 0];
    }
    $rs = $conn->query("SELECT DATE(created_at) d, SUM(total_money) r, COUNT(*) c
        FROM orders WHERE status='Completed' AND created_at >= CURDATE() - INTERVAL 29 DAY
        GROUP BY DATE(created_at)");
    while ($r = $rs->fetch_assoc()) {
        if (isset($days[$r['d']])) {
            $days[$r['d']]['revenue'] = (float)$r['r'];
            $days[$r['d']]['orders']  = (int)$r['c'];
        }
    }

    // doanh thu 12 tháng gần nhất
    $months = [];
    for ($i = 11; $i >= 0; $i--) {
        $k = date('Y-m', strtotime(date('Y-m-01') . " -$i month"));
        $months[$k] = ['label' => date('m/Y', strtotime($k . '-01')), 'revenue' => 0, 'orders' => 0];
    }
    $rs = $conn->query("SELECT DATE_FORMAT(created_at,'%Y-%m') m, SUM(total_money) r, COUNT(*) c
        FROM orders WHERE status='Completed'
        AND created_at >= DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH,'%Y-%m-01')
        GROUP BY m");
    while ($r = $rs->fetch_assoc()) {
        if (isset($months[$r['m']])) {
            $months[$r['m']]['revenue'] = (float)$r['r'];
            $months[$r['m']]['orders']  = (int)$r['c'];
        }
    }

    // doanh thu kỳ trước (để tính % tăng giảm)
    $m0 = date('Y-m-01');
    $prevDays = (float)$conn->query("SELECT IFNULL(SUM(total_money),0) t FROM orders
        WHERE status='Completed' AND created_at >= CURDATE() - INTERVAL 59 DAY
        AND created_at < CURDATE() - INTERVAL 29 DAY")->fetch_assoc()['t'];
    $from = date('Y-m-01', strtotime("$m0 -23 month"));
    $to   = date('Y-m-01', strtotime("$m0 -11 month"));
    $prevMonths = (float)$conn->query("SELECT IFNULL(SUM(total_money),0) t FROM orders
        WHERE status='Completed' AND created_at >= '$from' AND created_at < '$to'")->fetch_assoc()['t'];

    // top 5 sản phẩm bán chạy (bỏ đơn đã hủy)
    $top = [];
    $rs = $conn->query("SELECT p.name, SUM(od.quantity) qty, SUM(od.quantity*od.price) rev
        FROM order_details od
        JOIN orders o ON o.id=od.order_id
        JOIN products p ON p.id=od.product_id
        WHERE o.status<>'Cancelled'
        GROUP BY p.id, p.name ORDER BY qty DESC, rev DESC LIMIT 5");
    while ($r = $rs->fetch_assoc()) {
        $top[] = ['name' => $r['name'], 'qty' => (int)$r['qty'], 'revenue' => (float)$r['rev']];
    }

    // số đơn theo trạng thái
    $status = ['Pending' => 0, 'Processing' => 0, 'Shipping' => 0, 'Completed' => 0, 'Cancelled' => 0];
    $rs = $conn->query("SELECT status, COUNT(*) c FROM orders GROUP BY status");
    while ($r = $rs->fetch_assoc()) {
        $status[$r['status']] = (int)$r['c'];
    }

    return [
        'days'   => array_values($days),
        'months' => array_values($months),
        'top'    => $top,
        'status' => $status,
        'prev'   => ['days' => $prevDays, 'months' => $prevMonths],
        'time'   => date('H:i:s'),
    ];
}

// gọi trực tiếp thì trả JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    header('Content-Type: application/json; charset=utf-8');
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
    echo json_encode(dashboard_data($conn), JSON_UNESCAPED_UNICODE);
}
