// Khởi tạo giỏ hàng từ localStorage
let cart = JSON.parse(localStorage.getItem('vaccineCart')) || [];

// Kiểm tra xem vắc xin đã có trong giỏ hàng chưa
function isVaccineInCart(vaccineId) {
    return cart.some(item => item.id === vaccineId);
}

// Thêm hoặc xóa vắc xin khỏi giỏ hàng
function toggleVaccineSelection(vaccineId, vaccineName, vaccinePrice) {
    const index = cart.findIndex(item => item.id === vaccineId);
    
    if (index === -1) {
        // Thêm vắc xin vào giỏ hàng
        cart.push({
            id: vaccineId,
            name: vaccineName,
            price: vaccinePrice,
            quantity: 1
        });
        
        // Cập nhật nút
        const button = document.querySelector(`.vaccine-card[data-id="${vaccineId}"] .vaccine-btn`);
        if (button) {
            button.classList.add('selected');
            button.textContent = 'ĐÃ CHỌN';
        }
    } else {
        // Xóa vắc xin khỏi giỏ hàng
        cart.splice(index, 1);
        
        // Cập nhật nút
        const button = document.querySelector(`.vaccine-card[data-id="${vaccineId}"] .vaccine-btn`);
        if (button) {
            button.classList.remove('selected');
            button.textContent = 'CHỌN';
        }
    }
    
    // Lưu giỏ hàng vào localStorage
    localStorage.setItem('vaccineCart', JSON.stringify(cart));
    
    // Cập nhật hiển thị giỏ hàng
    updateCartDisplay();
    
    // Hiển thị thông báo
    showNotification(index === -1 ? 'Đã thêm vắc xin vào giỏ hàng' : 'Đã xóa vắc xin khỏi giỏ hàng');
}

// Cập nhật hiển thị giỏ hàng
function updateCartDisplay() {
    const cartItems = document.getElementById('cart-items');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    
    if (!cartItems || !cartTotalAmount) return;
    
    // Xóa nội dung hiện tại
    cartItems.innerHTML = '';
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="empty-cart">Giỏ hàng của bạn đang trống</p>';
        cartTotalAmount.textContent = '0 đ';
        return;
    }
    
    // Tính tổng tiền
    let total = 0;
    
    // Thêm từng mục vào giỏ hàng
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">${formatCurrency(item.price)}</div>
            </div>
            <div class="cart-item-remove" onclick="removeFromCart(${item.id})">
                <i class="fas fa-trash"></i>
            </div>
        `;
        
        cartItems.appendChild(cartItem);
    });
    
    // Cập nhật tổng tiền
    cartTotalAmount.textContent = formatCurrency(total);
}

// Xóa vắc xin khỏi giỏ hàng
function removeFromCart(vaccineId) {
    const index = cart.findIndex(item => item.id === vaccineId);
    
    if (index !== -1) {
        // Lấy tên vắc xin trước khi xóa
        const vaccineName = cart[index].name;
        
        // Xóa vắc xin khỏi giỏ hàng
        cart.splice(index, 1);
        
        // Lưu giỏ hàng vào localStorage
        localStorage.setItem('vaccineCart', JSON.stringify(cart));
        
        // Cập nhật hiển thị giỏ hàng
        updateCartDisplay();
        
        // Cập nhật nút nếu vắc xin đang hiển thị trên trang
        const button = document.querySelector(`.vaccine-card[data-id="${vaccineId}"] .vaccine-btn`);
        if (button) {
            button.classList.remove('selected');
            button.textContent = 'CHỌN';
        }
        
        // Hiển thị thông báo
        showNotification(`Đã xóa ${vaccineName} khỏi giỏ hàng`);
    }
}

// Hiển thị/ẩn giỏ hàng
function toggleCart() {
    const cartPopup = document.getElementById('cart-popup');
    cartPopup.classList.toggle('active');
    
    // Cập nhật hiển thị giỏ hàng khi mở
    if (cartPopup.classList.contains('active')) {
        updateCartDisplay();
    }
}

// Thanh toán
function checkout() {
    if (cart.length === 0) {
        showNotification('Giỏ hàng của bạn đang trống');
        return;
    }
    
    // Kiểm tra đăng nhập
    const isLoggedIn = document.querySelector('.user-menu') !== null;
    
    if (!isLoggedIn) {
        showNotification('Vui lòng đăng nhập để đặt lịch tiêm', 'error');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
        return;
    }
    
    // Chuyển hướng đến trang đặt lịch
    window.location.href = 'giohang.php';
}

// Hiển thị thông báo
function showNotification(message, type = 'success') {
    // Kiểm tra xem đã có thông báo nào chưa
    let notification = document.querySelector('.notification');
    
    if (!notification) {
        // Tạo phần tử thông báo
        notification = document.createElement('div');
        notification.className = 'notification';
        document.body.appendChild(notification);
        
        // Thêm CSS cho thông báo
        const style = document.createElement('style');
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                font-weight: bold;
                z-index: 1000;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
                max-width: 300px;
            }
            
            .notification.success {
                background-color: #27ae60;
            }
            
            .notification.error {
                background-color: #e74c3c;
            }
            
            .notification.show {
                opacity: 1;
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);
    }
    
    // Cập nhật nội dung và kiểu thông báo
    notification.textContent = message;
    notification.className = `notification ${type}`;
    
    // Hiển thị thông báo
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Ẩn thông báo sau 3 giây
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Thêm nút giỏ hàng vào menu
function addCartButton() {
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenu) {
        // Thêm nút giỏ hàng vào menu người dùng
        const cartButton = document.createElement('li');
        cartButton.innerHTML = `<a href="#" onclick="toggleCart(); return false;"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>`;
        userMenu.parentNode.insertBefore(cartButton, userMenu);
    } else {
        // Thêm nút giỏ hàng vào menu chính
        const mainMenu = document.getElementById('main-menu');
        const loginItem = document.querySelector('#main-menu li a[href="login.php"]').parentNode;
        
        if (mainMenu && loginItem) {
            const cartButton = document.createElement('li');
            cartButton.innerHTML = `<a href="#" onclick="toggleCart(); return false;"><i class="fas fa-shopping-cart"></i> Giỏ hàng</a>`;
            mainMenu.insertBefore(cartButton, loginItem);
        }
    }
}

// Khởi tạo khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Thêm nút giỏ hàng vào menu
    addCartButton();
    
    // Cập nhật hiển thị giỏ hàng
    updateCartDisplay();
    
    // Thêm sự kiện đóng giỏ hàng khi nhấp vào bên ngoài
    document.addEventListener('click', function(event) {
        const cartPopup = document.getElementById('cart-popup');
        const cartButton = document.querySelector('a[onclick*="toggleCart"]');
        
        if (cartPopup && cartPopup.classList.contains('active') && 
            !cartPopup.contains(event.target) && 
            (!cartButton || !cartButton.contains(event.target))) {
            cartPopup.classList.remove('active');
        }
    });
});