<?php include 'includes/header.php'; ?>

<div class="sidebar">
    <div class="p-4 text-center">
        <h4 style="color: var(--gold); font-weight: 800;">THE BOSS</h4>
        <small class="text-muted">نظام إدارة السوفت وير</small>
    </div>
    <nav class="mt-3">
        <a href="index" class="nav-link active"><i class="fa-solid fa-chart-line"></i> الإحصائيات</a>
        <a href="products" class="nav-link"><i class="fa-solid fa-mobile-screen"></i> المنتجات</a>
        <a href="categories" class="nav-link"><i class="fa-solid fa-layer-group"></i> الأقسام</a>
        <a href="users" class="nav-link"><i class="fa-solid fa-users"></i> المستخدمين</a>
        <a href="settings" class="nav-link"><i class="fa-solid fa-gear"></i> الإعدادات</a>
    </nav>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">لوحة التحكم - نظرة عامة</h2>
            <div class="badge bg-dark p-2 border border-secondary">المسؤول: م. علي طارق</div>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="boss-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted small">إجمالي الأرباح</h6>
                            <h3 class="fw-bold mb-0">12,500 <small style="font-size: 0.8rem;">ج.م</small></h3>
                        </div>
                        <i class="fa-solid fa-wallet text-success fs-4"></i>
                    </div>
                </div>
            </div>
            </div>

        <div class="row mt-5">
            <div class="col-md-8">
                <div class="boss-card">
                    <h5 class="mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i> آخر العمليات</h5>
                    <table class="table table-dark table-hover border-0">
                        <thead>
                            <tr>
                                <th class="text-muted">العميل</th>
                                <th class="text-muted">الخدمة</th>
                                <th class="text-muted">المبلغ</th>
                                <th class="text-muted">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>أحمد محمد</td>
                                <td>تفعيل شيميرا</td>
                                <td>450 ج.م</td>
                                <td><span class="badge bg-success-subtle text-success">مكتمل</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>