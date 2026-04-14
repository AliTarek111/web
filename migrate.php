<?php
include 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_name VARCHAR(255) NOT NULL DEFAULT 'عميل' AFTER id");
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "Migration complete\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
