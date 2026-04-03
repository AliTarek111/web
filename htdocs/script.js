// العقل المدبر للمتجر - متجر أحمد كشري

let cart = JSON.parse(localStorage.getItem('koshary_cart')) || [];

// 1. وظيفة التبديل لفتح وإغلاق السلة
function toggleCart() {
    const sidebar = document.getElementById('cart-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

// 2. إضافة منتج للسلة
function addToCart(id, name, price, image) {
    if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
    }

    const item = { id, name, price, image };
    cart.push(item);
    updateCartUI();
    saveCart();
    
    // أنيميشن خفيف عند الإضافة
    const btn = event.target;
    btn.innerText = 'تمت الإضافة ✅';
    setTimeout(() => { btn.innerText = 'إضافة للسلة 🛒'; }, 1500);
}

// 3. تحديث واجهة السلة
function updateCartUI() {
    const cartItems = document.getElementById('cart-items');
    const cartBadge = document.getElementById('cart-badge');
    const cartTotal = document.getElementById('cart-total');

    if (!cartItems) return;

    if (cart.length === 0) {
        cartItems.innerHTML = '<p style="text-align:center; margin-top:50px; color:var(--text-muted);">السلة فارغة حالياً</p>';
        if (cartBadge) cartBadge.innerText = '0';
        if (cartTotal) cartTotal.innerText = '0 ج.م';
        return;
    }

    cartBadge.innerText = cart.length;
    cartItems.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
        total += parseFloat(item.price);
        cartItems.innerHTML += `
            <div class="cart-item">
                <img src="uploads/${item.image}" alt="${item.name}">
                <div style="flex:1">
                    <h4 style="font-size:0.9rem">${item.name}</h4>
                    <p style="color:var(--gold); font-weight:800">${item.price} ج.م</p>
                </div>
                <button onclick="removeFromCart(${index})" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:1.2rem">✕</button>
            </div>
        `;
    });

    cartTotal.innerText = total.toLocaleString() + ' ج.م';
}

// 4. حذف منتج من السلة
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartUI();
    saveCart();
}

// 5. حفظ السلة في المتصفح
function saveCart() {
    localStorage.setItem('koshary_cart', JSON.stringify(cart));
}

// 6. فلترة المنتجات حسب القسم
function filterCategory(category) {
    const products = document.querySelectorAll('.product-card');
    const title = document.getElementById('products-title');
    
    title.innerText = category === 'all' ? 'جميع المنتجات ✨' : `قسم الـ ${category.toUpperCase()} 📱`;

    products.forEach(product => {
        if (category === 'all' || product.getAttribute('data-category') === category) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });

    // سكرول لمكان المنتجات
    document.getElementById('products').scrollIntoView();
}

// 7. إتمام الطلب والتحويل للواتساب
function checkout() {
    if (cart.length === 0) {
        alert('سلتك فارغة يا هندسة!');
        return;
    }

    let itemsText = cart.map(i => `- ${i.name} (${i.price} ج.م)`).join('%0A');
    let total = cart.reduce((sum, i) => sum + parseFloat(i.price), 0);
    
    // إرسال البيانات للسيرفر للأرشيف أولاً
    const formData = new FormData();
    formData.append('items', cart.map(i => i.name).join(', '));
    formData.append('total', total);

    fetch('process_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            // مسح السلة بعد النجاح
            cart = [];
            saveCart();
            updateCartUI();
            
            // فتح واتساب
            const whatsappNumber = "201141493535"; // م. علي طارق
            const message = `أهلاً م. علي، أريد طلب الآتي:%0A${itemsText}%0A%0Aالإجمالي: ${total} ج.م`;
            window.open(`https://wa.me/${whatsappNumber}?text=${message}`, '_blank');
        } else {
            alert('حدث خطأ أثناء تسجيل الطلب، يرجى المحاولة مرة أخرى.');
        }
    });
}

// تهيئة السلة عند التحميل
document.addEventListener('DOMContentLoaded', updateCartUI);