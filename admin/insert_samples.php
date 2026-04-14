<?php
// admin/insert_samples.php
// Script to populate the store with luxury samples
include '../includes/db.php';

$samples = [
    [
        'uid' => 'BOSS-9021',
        'name' => 'iPhone 15 Pro Max - Natural Titanium',
        'price' => 54000,
        'category_slug' => 'smartphones',
        'condition' => 'new',
        'desc' => 'أحدث إصدار من آبل، معالج A17 Pro، كاميرا بدقة 48 ميجابكسل، تيتانيوم طبيعي.',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC15JwT7AAg4nokhrhm3slCvxOezwSWxbdGOsdGJSqb42Jf3vn0LKaHcErtEJDEXRpAy0sHfo1rVlaQJ45uPkyw1T_50TlEMSUL-J6osH2P5HsxnnphaQQzjU4qBReATzshWWtKJ6tskPoGhwEqGt0oQzoNxQQ96eWNLOuz-9Ga8dNAxX1u8EsKXw-epeWgSQOTWVyrVd55R-NlS6HlqPrNOOHP2MMH2kQhmHLs2UNb906K2qjWxOw7YGaniJL5hIllG73VMXG7wwLn'
    ],
    [
        'uid' => 'BOSS-8820',
        'name' => 'Galaxy S24 Ultra - Phantom Black',
        'price' => 48500,
        'category_slug' => 'smartphones',
        'condition' => 'new',
        'desc' => 'عملاق سامسونج مع قلم S-Pen، زووم 100x، شاشة LTPO AMOLED.',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB6MC7e1ng1F-RnkxsTa5EcYOPYJnqNBWYlWRmz6inLlJteLQ6q3_Wrn2ORUtE7DORY4iY65g0UcBV2MCbSVqM-_mRGluyerPh1tW8k4XoDfNMxP3R1ksed2Ol1TAmnqpUpGqvLRazetuh4ZRsa2Ie5ZN2BWYODGjn2BGS3Ns5ls8jYox5tS7rXexmSfrDC2MI0iaO5iFTB4qGuM3Mw9xhVBR1Dx1xM4Pn-kIc8WIgVUNUAnrOu3SUSVgJK2oKVRjDANaqwe1kOpG-l'
    ],
    [
        'uid' => 'SOFT-001',
        'name' => 'iOS Sovereign Bypass v4.2',
        'price' => 1200,
        'category_slug' => 'software',
        'condition' => 'new',
        'desc' => 'أداة احترافية لفك وتخطي حسابات آيكلود للأجهزة المدعومة بشكل آمن.',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBy2x-S8b6Z-A_u-C5Y-pP7o-Y9O0uOqM-xG7H-XG3Z-Y-w-X-W-S-U-T-Y-G-N-J-T-U-R'
    ]
];

foreach ($samples as $s) {
    $cat_id = $pdo->query("SELECT id FROM categories WHERE slug = '{$s['category_slug']}'")->fetchColumn();
    $stmt = $pdo->prepare("INSERT IGNORE INTO products (uid, name, price, category_id, condition_status, description, main_image, stock_count) VALUES (?, ?, ?, ?, ?, ?, ?, 5)");
    $stmt->execute([$s['uid'], $s['name'], $s['price'], $cat_id, $s['condition'], $s['desc'], $s['image']]);
}

echo "تم إضافة العينات الملكية بنجاح!";
?>
