@extends('admin.layouts.sidebar')

@section('title', 'Dashboard')

@section('main-content')
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted">Tổng sản phẩm</h5>
                        <h2 class="mb-0">45</h2>
                    </div>
                    <div class="bg-primary text-white p-3 rounded">
                        <i class="fas fa-coffee fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted">Đơn hàng</h5>
                        <h2 class="mb-0">28</h2>
                    </div>
                    <div class="bg-success text-white p-3 rounded">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted">Khách hàng</h5>
                        <h2 class="mb-0">120</h2>
                    </div>
                    <div class="bg-info text-white p-3 rounded">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-muted">Doanh thu</h5>
                        <h2 class="mb-0">15.2M</h2>
                    </div>
                    <div class="bg-warning text-white p-3 rounded">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Doanh thu theo tháng</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Sản phẩm bán chạy</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cà phê hạt Tam Giao
                        <span class="badge bg-primary rounded-pill">124</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cà phê Arabica
                        <span class="badge bg-primary rounded-pill">98</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cà phê Robusta
                        <span class="badge bg-primary rounded-pill">76</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cà phê Espresso
                        <span class="badge bg-primary rounded-pill">62</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cà phê Cappuccino
                        <span class="badge bg-primary rounded-pill">54</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Đơn hàng gần đây</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-1234</td>
                                <td>Nguyễn Văn A</td>
                                <td>04/04/2025</td>
                                <td>320,000đ</td>
                                <td><span class="badge bg-success">Hoàn thành</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-1233</td>
                                <td>Trần Thị B</td>
                                <td>03/04/2025</td>
                                <td>180,000đ</td>
                                <td><span class="badge bg-warning">Đang giao</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-1232</td>
                                <td>Lê Văn C</td>
                                <td>03/04/2025</td>
                                <td>450,000đ</td>
                                <td><span class="badge bg-primary">Đang xử lý</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-1231</td>
                                <td>Phạm Thị D</td>
                                <td>02/04/2025</td>
                                <td>275,000đ</td>
                                <td><span class="badge bg-danger">Đã hủy</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>#ORD-1230</td>
                                <td>Hoàng Văn E</td>
                                <td>02/04/2025</td>
                                <td>520,000đ</td>
                                <td><span class="badge bg-success">Hoàn thành</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        if (document.getElementById('revenueChart')) {
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Doanh thu (triệu đồng)',
                        data: [8.2, 10.5, 9.3, 12.8, 14.5, 13.2, 15.7, 16.2, 14.8, 16.5, 18.2, 20.1],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
@endpush