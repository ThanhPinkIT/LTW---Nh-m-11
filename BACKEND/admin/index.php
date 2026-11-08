<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'header.php';
require_once 'dashboard-data.php';
$chartData = dashboard_data($conn);

// ======================
// THỐNG KÊ
// ======================

// Tổng sản phẩm
$res = $conn->query("SELECT COUNT(*) AS total FROM products");
$total_products = $res->fetch_assoc()['total'];

// Tổng danh mục
$res = $conn->query("SELECT COUNT(*) AS total FROM categories");
$total_categories = $res->fetch_assoc()['total'];

// Tổng người dùng
$res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='customer'");
$total_users = $res->fetch_assoc()['total'];

// Tổng đơn hàng
$res = $conn->query("SELECT COUNT(*) AS total FROM orders");
$total_orders = $res->fetch_assoc()['total'];

// Doanh thu
$res = $conn->query("
SELECT IFNULL(SUM(total_money),0) AS total
FROM orders
WHERE status='Completed'
");
$total_money = $res->fetch_assoc()['total'];

// Danh sách user
$result_users = $conn->query("
SELECT id,username,fullname,email,role,created_at
FROM users
ORDER BY id DESC
");

// 5 đơn hàng mới nhất
$new_orders = $conn->query("
SELECT
orders.id,
users.fullname,
orders.total_money,
orders.status,
orders.created_at
FROM orders
JOIN users
ON users.id=orders.user_id
ORDER BY orders.created_at DESC
LIMIT 5
");
?>
<section class="admin-dashboard">
<h2 class="dashboard-title">
<span class="dashboard-mark" aria-hidden="true">▦</span> Dashboard
</h2>
<div class="row dashboard-stats">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <svg class="stat-icon" viewBox="0 0 24 26" fill="currentColor" aria-hidden="true"><path d="m12 2 10 5-10 5L2 7Z"/><path d="m2 10 9 5v9l-9-5Zm11 5 9-5v9l-9 5Z"/></svg>
                <h6 class="text-muted"> Tổng sản phẩm </h6>
                <h2 class="fw-bold text-primary"> <?= $total_products ?> </h2>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <svg class="stat-icon" viewBox="0 0 24 26" fill="currentColor" aria-hidden="true"><path d="M2 5h8l3 3h9v3H5l-3 11H0V7a2 2 0 0 1 2-2Z"/><path d="M6 12h18l-4 10H3Z"/></svg>
                <h6 class="text-muted"> Danh mục </h6>
                <h2 class="fw-bold text-info"> <?= $total_categories ?> </h2>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <svg class="stat-icon" viewBox="0 0 24 26" fill="currentColor" aria-hidden="true"><circle cx="12" cy="7" r="5"/><path d="M3 24v-5a9 9 0 0 1 18 0v5Z"/></svg>
                <h6 class="text-muted"> Khách hàng </h6>
                <h2 class="fw-bold text-success"> <?= $total_users ?> </h2>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card shadow border-0">
            <div class="card-body text-center">
                <svg class="stat-icon" viewBox="0 0 24 26" fill="currentColor" aria-hidden="true"><path d="M0 2h4l4 15h13l3-12H6v3h14l-2 6H10L7 2Z"/><circle cx="10" cy="22" r="2"/><circle cx="20" cy="22" r="2"/></svg>
                <h6 class="text-muted"> Đơn hàng </h6>
                <h2 class="fw-bold text-warning"> <?= $total_orders ?> </h2>
            </div>
        </div>
    </div>
</div>
<div class="row dashboard-stats">
    <div class="col-12 mb-4">
        <div class="card revenue-card text-white shadow border-0">
            <div class="card-body text-center">
                <h5> 💰 Tổng doanh thu </h5>
                <h2 class="fw-bold"> <?= number_format($total_money,0,",",".") ?> đ </h2>
            </div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4 dashboard-charts">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-head">
                <div>
                    <div class="chart-label">Doanh thu <span id="revRange">30 ngày gần nhất</span></div>
                    <div class="chart-value" id="revTotal">0 đ</div>
                    <div class="chart-delta" id="revDelta"></div>
                </div>
                <div class="btn-group btn-group-sm chart-mode">
                    <button type="button" class="btn btn-primary" data-mode="days">Ngày</button>
                    <button type="button" class="btn btn-light" data-mode="months">Tháng</button>
                </div>
            </div>
            <div class="chart-box"><canvas id="revenueChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-head"><div class="chart-label">Đơn hàng theo trạng thái</div></div>
            <div class="chart-box chart-box--donut"><canvas id="statusChart"></canvas></div>
            <ul class="status-list" id="statusList"></ul>
            <a class="status-alert" id="pendingLink" href="order.php" hidden></a>
        </div>
    </div>
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-head">
                <div class="chart-label">Top 5 sản phẩm bán chạy</div>
                <span class="chart-note" id="updated"></span>
            </div>
            <div class="chart-box chart-box--short"><canvas id="topChart"></canvas></div>
        </div>
    </div>
</div>
<div class="card dashboard-table shadow border-0 mb-4">
    <div class="card-header dashboard-table-heading text-white"> 🛒 5 đơn hàng mới nhất </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
            </tr>
            </thead>
            <tbody>
            <?php if($new_orders->num_rows>0): ?>
                <?php while($row=$new_orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td class="fw-bold text-danger">
                        <?= number_format($row['total_money'],0,",",".") ?> đ </td>
                    <td>
                        <?php
                        switch($row['status']){
                            case 'Pending':
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                                break;
                            case 'Processing':
                                echo '<span class="badge bg-info">Processing</span>';
                                break;
                            case 'Shipping':
                                echo '<span class="badge bg-primary">Shipping</span>';
                                break;
                            case 'Completed':
                                echo '<span class="badge bg-success">Completed</span>';
                                break;
                            default:
                                echo '<span class="badge bg-danger">Cancelled</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?= date("d/m/Y H:i",strtotime($row['created_at'])) ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center"> Chưa có đơn hàng. </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card dashboard-table shadow border-0">
    <div class="card-header dashboard-table-heading text-white"> 👥 Danh sách người dùng </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Ngày tạo</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row=$result_users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <?php
                        if($row['role']=="admin")
                            echo '<span class="badge role-admin">Admin</span>';
                        else
                            echo '<span class="badge role-user">User</span>';
                        ?>
                    </td>
                    <td> <?= date("d/m/Y",strtotime($row['created_at'])) ?> </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// dữ liệu lần đầu lấy từ PHP, sau đó cứ 10s gọi dashboard-data.php để cập nhật
let data = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE) ?>;
let mode = 'days';

const so = (n, d = 0) => Number(n).toLocaleString('vi-VN', { maximumFractionDigits: d });
const tien = n => so(n) + ' đ';
const rutGon = n => n >= 1e6 ? so(n / 1e6, 1) + ' tr' : n >= 1e3 ? so(n / 1e3) + 'k' : n;

const font = getComputedStyle(document.body).fontFamily;
Chart.defaults.font.family = font;
Chart.defaults.font.size = 12;
Chart.defaults.color = '#8a6d66';
const luoi = '#f6e4df';

const trangThai = [
    ['Pending',    'Chờ xử lý',  '#f0b429'],
    ['Processing', 'Đang xử lý', '#38b2d8'],
    ['Shipping',   'Đang giao',  '#4c7cf0'],
    ['Completed',  'Hoàn thành', '#2f9e6b'],
    ['Cancelled',  'Đã hủy',     '#d9534f']
];

// ---------- doanh thu ----------
const revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: { labels: [], datasets: [{
        data: [], borderColor: '#d3351c', borderWidth: 2, tension: 0.3, fill: true,
        pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#d3351c', pointHoverBorderColor: '#fff', pointHoverBorderWidth: 2,
        backgroundColor: ctx => {
            const a = ctx.chart.chartArea;
            if (!a) return 'rgba(211,53,28,.1)';
            const g = ctx.chart.ctx.createLinearGradient(0, a.top, 0, a.bottom);
            g.addColorStop(0, 'rgba(211,53,28,.20)');
            g.addColorStop(1, 'rgba(211,53,28,0)');
            return g;
        }
    }] },
    options: {
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#3b2a26', padding: 10, displayColors: false,
                callbacks: {
                    label: c => tien(c.parsed.y),
                    afterLabel: c => data[mode][c.dataIndex].orders + ' đơn hoàn thành'
                }
            }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
            y: { beginAtZero: true, grid: { color: luoi }, border: { display: false }, ticks: { callback: rutGon, maxTicksLimit: 5 } }
        }
    }
});

// ---------- trạng thái: số tổng nằm giữa vành khuyên ----------
const soGiua = {
    id: 'soGiua',
    afterDraw(chart) {
        const a = chart.chartArea, ctx = chart.ctx;
        const tong = chart.data.datasets[0].data.reduce((x, y) => x + y, 0);
        const x = (a.left + a.right) / 2, y = (a.top + a.bottom) / 2;
        ctx.save();
        ctx.textAlign = 'center';
        ctx.fillStyle = '#2b2b2b';
        ctx.font = '700 26px ' + font;
        ctx.fillText(so(tong), x, y + 4);
        ctx.fillStyle = '#8a6d66';
        ctx.font = '12px ' + font;
        ctx.fillText('đơn hàng', x, y + 22);
        ctx.restore();
    }
};
const statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: trangThai.map(t => t[1]), datasets: [{ data: [], backgroundColor: trangThai.map(t => t[2]), borderWidth: 3, borderColor: '#fffdfd', hoverOffset: 4 }] },
    options: { maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } },
    plugins: [soGiua]
});

// ---------- top sản phẩm: ghi số lượng cuối mỗi thanh ----------
const soCuoiThanh = {
    id: 'soCuoiThanh',
    afterDatasetsDraw(chart) {
        const ctx = chart.ctx;
        ctx.save();
        ctx.font = '600 12px ' + font;
        ctx.fillStyle = '#70483f';
        ctx.textBaseline = 'middle';
        chart.getDatasetMeta(0).data.forEach((bar, i) => ctx.fillText(chart.data.datasets[0].data[i], bar.x + 8, bar.y));
        ctx.restore();
    }
};
const topChart = new Chart(document.getElementById('topChart'), {
    type: 'bar',
    data: { labels: [], datasets: [{ data: [], backgroundColor: ['#b82612', '#d3351c', '#e5604a', '#ee8d7b', '#f4b5a8'], borderRadius: 4, barThickness: 18 }] },
    options: {
        indexAxis: 'y',
        maintainAspectRatio: false,
        layout: { padding: { right: 30 } },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#3b2a26', padding: 10, displayColors: false,
                callbacks: {
                    title: c => data.top[c[0].dataIndex].name,
                    label: c => 'Đã bán: ' + c.parsed.x + ' sản phẩm',
                    afterLabel: c => 'Doanh thu: ' + tien(data.top[c.dataIndex].revenue)
                }
            }
        },
        scales: {
            x: { display: false, beginAtZero: true },
            y: { grid: { display: false }, border: { display: false }, ticks: { color: '#4a3430' } }
        }
    },
    plugins: [soCuoiThanh]
});

// ---------- vẽ lại ----------
function veBieuDo() {
    const rows = data[mode];
    const tong = rows.reduce((t, r) => t + r.revenue, 0);
    const truoc = data.prev[mode];

    revenueChart.data.labels = rows.map(r => r.label);
    revenueChart.data.datasets[0].data = rows.map(r => r.revenue);
    revenueChart.update();
    document.getElementById('revRange').textContent = mode === 'days' ? '30 ngày gần nhất' : '12 tháng gần nhất';
    document.getElementById('revTotal').textContent = tien(tong);

    const delta = document.getElementById('revDelta');
    if (truoc > 0) {
        const pt = (tong - truoc) / truoc * 100;
        const kyTruoc = mode === 'days' ? '30 ngày trước' : '12 tháng trước';
        delta.innerHTML = '<b class="' + (pt >= 0 ? 'is-up' : 'is-down') + '">' + (pt >= 0 ? '▲ ' : '▼ ') + so(Math.abs(pt), 1) + '%</b> so với ' + kyTruoc;
    } else {
        delta.textContent = 'Chưa có dữ liệu kỳ trước để so sánh';
    }

    const dem = trangThai.map(t => data.status[t[0]] || 0);
    const tongDon = dem.reduce((x, y) => x + y, 0);
    statusChart.data.datasets[0].data = dem;
    statusChart.update();
    document.getElementById('statusList').innerHTML = trangThai.map((t, i) =>
        '<li><i style="background:' + t[2] + '"></i>' + t[1] +
        '<span class="n">' + so(dem[i]) + '</span><span class="p">' + (tongDon ? so(dem[i] / tongDon * 100) : 0) + '%</span></li>'
    ).join('');

    const cho = data.status.Pending || 0;
    const link = document.getElementById('pendingLink');
    link.hidden = cho === 0;
    link.textContent = cho + ' đơn đang chờ xử lý — xem ngay →';

    const maxChu = window.innerWidth < 768 ? 18 : 42;
    topChart.data.labels = data.top.map(t => t.name.length > maxChu ? t.name.slice(0, maxChu - 2) + '...' : t.name);
    topChart.data.datasets[0].data = data.top.map(t => t.qty);
    topChart.update();

    document.getElementById('updated').textContent = 'Cập nhật lúc ' + data.time;
}

document.querySelectorAll('.chart-mode button').forEach(btn => {
    btn.addEventListener('click', () => {
        mode = btn.dataset.mode;
        document.querySelectorAll('.chart-mode button').forEach(b => {
            b.className = 'btn ' + (b === btn ? 'btn-primary' : 'btn-light');
        });
        veBieuDo();
    });
});

setInterval(() => {
    if (document.hidden) return;
    fetch('dashboard-data.php')
        .then(res => res.json())
        .then(d => { if (!d.error) { data = d; veBieuDo(); } })
        .catch(() => {});
}, 10000);

veBieuDo();
</script>
<?php include 'footer.php'; ?>