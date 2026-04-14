<?php
// admin/orders.php
// Ahmed Koshary Store - Order Management
require_once 'includes/auth_middleware.php';
include '../includes/db.php';
require_once 'includes/activity_logger.php';

// Handle Actions (Delete / Change Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $order_id = $_POST['order_id'] ?? 0;

    if ($action === 'delete' && $order_id) {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        log_activity($pdo, 'delete_order', "تم حذف الطلب رقم #ORD-{$order_id}", $order_id);
    } elseif ($action === 'status' && $order_id) {
        $new_status = $_POST['status'] ?? 'pending';
        // Enforce valid enum
        if (in_array($new_status, ['pending', 'completed', 'processing', 'cancelled'])) {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            log_activity($pdo, 'order_status', "تم تغيير حالة الطلب #ORD-{$order_id} إلى: {$new_status}", $order_id);
        }
    }
    header("Location: orders.php");
    exit;
}

// Fetch Orders
$orders = [];
if (!empty($pdo)) {
    // We group concat items using a subquery for display
    $orders = $pdo->query("
        SELECT o.*, 
        (SELECT GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ') ') SEPARATOR '<br>') 
         FROM order_items oi 
         JOIN products p ON oi.product_id = p.id 
         WHERE oi.order_id = o.id) as products_list
        FROM orders o 
        ORDER BY o.order_date DESC
    ")->fetchAll();
}

$pageTitle = "إدارة طلبات العملاء";
include 'includes/admin_header.php';
?>

<header class="mb-16 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 relative z-10">
    <div>
        <span class="inline-block text-primary font-bold tracking-[0.3em] text-[10px] uppercase border-r-2 border-primary pr-4 mb-4">سجل الحركات</span>
        <h1 class="text-5xl font-black font-headline text-on-surface tracking-tighter uppercase leading-tight">الطلبات <br/><span class="bg-gradient-to-l from-primary to-primary-container bg-clip-text text-transparent">الواردة للمتجر</span></h1>
    </div>
</header>

<!-- Orders Table -->
<section class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-3xl relative z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-white/5 text-on-surface-variant/40 text-[10px] uppercase font-black tracking-widest border-b border-white/5">
                    <th class="px-6 py-6">رقم الطلب</th>
                    <th class="px-6 py-6">العميل (الاسم / واتساب)</th>
                    <th class="px-6 py-6">العنوان</th>
                    <th class="px-6 py-6">المنتجات (الكمية)</th>
                    <th class="px-6 py-6 text-center">التاريخ</th>
                    <th class="px-6 py-6 text-center">الإجمالي</th>
                    <th class="px-6 py-6 text-center">الحالة</th>
                    <th class="px-6 py-6 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm font-medium">
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="8" class="px-10 py-24 text-center text-on-surface-variant/20 italic">لا توجد طلبات جارية في سجل النظام حالياً.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-6 py-6 text-[10px] font-bold text-primary tracking-widest font-mono uppercase">#ORD-<?php echo $order['id']; ?></td>
                    <td class="px-6 py-6">
                        <div class="space-y-1">
                            <h3 class="font-bold text-on-surface font-headline"><?php echo htmlspecialchars($order['customer_name'] ?? 'عميل (سريع)'); ?></h3>
                            <div class="flex items-center gap-1 opacity-70">
                                <span class="material-symbols-outlined text-[10px]">call</span>
                                <span class="text-[10px] tracking-widest font-mono"><?php echo $order['whatsapp_number']; ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6 max-w-[150px]">
                        <?php if (!empty($order['customer_address'])): ?>
                        <div class="flex items-start gap-1">
                            <span class="material-symbols-outlined text-primary/60 text-[10px] mt-0.5 flex-shrink-0">location_on</span>
                            <span class="text-[10px] text-on-surface/70 leading-relaxed"><?php echo htmlspecialchars($order['customer_address']); ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-[9px] text-on-surface-variant/20 italic">لم يحدد</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-6 text-[10px] text-on-surface/80 leading-loose">
                        <?php echo !empty($order['products_list']) ? $order['products_list'] : '<span class="text-on-surface-variant/40">قديم/مباشر</span>'; ?>
                    </td>
                    <td class="px-6 py-6 text-center text-[10px] text-on-surface-variant/60 font-bold uppercase tracking-widest leading-relaxed">
                        <?php echo date('d M, Y', strtotime($order['order_date'])); ?><br>
                        <?php echo date('H:i', strtotime($order['order_date'])); ?>
                    </td>
                    <td class="px-6 py-6 text-center font-headline font-black text-primary tracking-tight text-lg min-w-[100px]"><?php echo number_format($order['total_amount']); ?> ج.م</td>
                    <td class="px-6 py-6 text-center">
                        <form action="orders.php" method="POST">
                            <input type="hidden" name="action" value="status">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <?php 
                            $status_color = $order['status'] == 'completed' ? 'text-green-500 bg-green-500/10 border-green-500/20' : 
                                           ($order['status'] == 'pending' ? 'text-primary bg-primary/10 border-primary/20' : 
                                           'text-on-surface bg-white/5 border-white/10');
                            ?>
                            <select name="status" onchange="this.form.submit()" class="px-3 py-1 cursor-pointer text-[9px] font-black uppercase rounded-full border tracking-widest !pr-8 focus:ring-0 <?php echo $status_color; ?>" style="background-color: transparent;">
                                <option value="pending" class="bg-[#0a0e18] text-white" <?php if($order['status']=='pending') echo 'selected'; ?>>PENDING</option>
                                <option value="completed" class="bg-[#0a0e18] text-white" <?php if($order['status']=='completed') echo 'selected'; ?>>DONE</option>
                                <option value="cancelled" class="bg-[#0a0e18] text-white" <?php if($order['status']=='cancelled') echo 'selected'; ?>>CANCELED</option>
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <form action="orders.php" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟');" class="inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" class="w-8 h-8 rounded-lg bg-white/5 border border-white/5 flex items-center justify-center text-on-surface/40 hover:bg-error/20 hover:text-error transition-all mx-auto"><span class="material-symbols-outlined text-sm">delete</span></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/admin_footer.php'; ?>
