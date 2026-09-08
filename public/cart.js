class CartEncryption {
    static encode(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, (match, p1) => {
            return String.fromCharCode('0x' + p1);
        }));
    }

    static decode(str) {
        try {
            return decodeURIComponent(atob(str).split('').map(c => {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
        } catch(e) {
            console.error('Decryption error:', e);
            return null;
        }
    }
}

class ShoppingCart {
    constructor() {
        this.items = [];
        this.cookieName = 'hs_cart';
        this.loadCart();
        this.updateUI();
    }

    // Save cart to encrypted cookie
    saveCart() {
        const cartData = JSON.stringify(this.items);
        const encrypted = CartEncryption.encode(cartData);
        const expiryDays = 7;
        const d = new Date();
        d.setTime(d.getTime() + (expiryDays * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = this.cookieName + "=" + encrypted + ";" + expires + ";path=/;SameSite=Lax";
        
        console.log('Cart saved (encrypted):', encrypted.substring(0, 50) + '...');
        this.updateUI();
    }

    // Load cart from encrypted cookie
    loadCart() {
        const name = this.cookieName + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                const encrypted = c.substring(name.length, c.length);
                const decrypted = CartEncryption.decode(encrypted);
                
                if(decrypted) {
                    try {
                        this.items = JSON.parse(decrypted);
                        console.log('Cart loaded (decrypted):', this.items.length + ' items');
                    } catch(e) {
                        console.error('Error parsing cart:', e);
                        this.items = [];
                    }
                }
                return;
            }
        }
        this.items = [];
    }

    // Add item to cart
    addItem(item) {
        const existingItem = this.items.find(i => i.id === item.id);
        
        if(existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                ...item,
                quantity: 1
            });
        }
        
        this.saveCart();
        this.showNotification('Item ditambahkan ke keranjang!', 'success');
        this.animateCartIcon();
    }

    // Remove item from cart
    removeItem(itemId) {
        this.items = this.items.filter(item => item.id !== itemId);
        this.saveCart();
        this.showNotification('Item dihapus dari keranjang', 'info');
    }

    // Update item quantity
    updateQuantity(itemId, quantity) {
        const item = this.items.find(i => i.id === itemId);
        if(item) {
            if(quantity <= 0) {
                this.removeItem(itemId);
            } else {
                item.quantity = quantity;
                this.saveCart();
            }
        }
    }

    // Clear cart
    clearCart() {
        this.items = [];
        this.saveCart();
    }

    // Get cart total
    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    // Get cart count
    getCount() {
        return this.items.reduce((count, item) => count + item.quantity, 0);
    }

    // Update UI elements
    updateUI() {
        // Update cart badge
        const badge = document.getElementById('cartBadge');
        if(badge) {
            const count = this.getCount();
            badge.textContent = count;
            if(count > 0) {
                badge.classList.add('show');
            } else {
                badge.classList.remove('show');
            }
        }

        // Update cart modal if open
        if(typeof updateCartModal === 'function') {
            updateCartModal();
        }
    }

    // Animate cart icon
    animateCartIcon() {
        const cartIcon = document.querySelector('.cart-icon-wrapper');
        if(cartIcon) {
            cartIcon.style.animation = 'none';
            setTimeout(() => {
                cartIcon.style.animation = 'cartBounce 0.5s ease';
            }, 10);
        }
    }

    // Show notification
    showNotification(message, type = 'success') {
        // Remove existing notifications
        const existing = document.querySelectorAll('.cart-notification');
        existing.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `cart-notification cart-notification-${type}`;
        
        const icon = type === 'success' ? 'check-circle-fill' : 
                     type === 'error' ? 'exclamation-circle-fill' : 
                     'info-circle-fill';
        
        notification.innerHTML = `
            <i class="bi bi-${icon} me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Initialize cart
const cart = new ShoppingCart();

// Add to cart function (called from HTML)
function addToCart(item) {
    cart.addItem(item);
}

// Open cart modal
function openCartModal() {
    const modal = new bootstrap.Modal(document.getElementById('cartModal'));
    updateCartModal();
    modal.show();
}

// Update cart modal content
function updateCartModal() {
    const cartItems = document.getElementById('cartItems');
    const cartEmpty = document.getElementById('cartEmpty');
    const cartContent = document.getElementById('cartContent');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartTotal = document.getElementById('cartTotal');
    const cartSavings = document.getElementById('cartSavings');

    if(cart.items.length === 0) {
        if(cartEmpty) cartEmpty.style.display = 'block';
        if(cartContent) cartContent.style.display = 'none';
        return;
    }

    if(cartEmpty) cartEmpty.style.display = 'none';
    if(cartContent) cartContent.style.display = 'block';

    // Render cart items
    if(cartItems) {
        cartItems.innerHTML = cart.items.map(item => `
            <div class="cart-item" data-id="${item.id}">
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-restaurant">${item.restaurant}</div>
                    <div class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')}</div>
                </div>
                <div class="cart-item-actions">
                    <div class="quantity-control">
                        <button class="qty-btn" onclick="updateCartQuantity('${item.id}', ${item.quantity - 1})">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="qty-display">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateCartQuantity('${item.id}', ${item.quantity + 1})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <button class="btn-remove" onclick="removeFromCart('${item.id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Calculate totals
    const subtotal = cart.getTotal();
    const originalTotal = cart.items.reduce((total, item) => {
        return total + ((item.originalPrice || item.price) * item.quantity);
    }, 0);
    const savings = originalTotal - subtotal;

    if(cartSubtotal) cartSubtotal.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    if(cartTotal) cartTotal.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    if(cartSavings) cartSavings.textContent = 'Rp ' + savings.toLocaleString('id-ID');
}

// Update cart quantity
function updateCartQuantity(itemId, quantity) {
    cart.updateQuantity(itemId, quantity);
    updateCartModal();
}

// Remove from cart
function removeFromCart(itemId) {
    if(confirm('Hapus item ini dari keranjang?')) {
        cart.removeItem(itemId);
        updateCartModal();
    }
}

// Proceed to checkout
function proceedToCheckout() {
    if(cart.items.length === 0) {
        alert('Keranjang Anda kosong!');
        return;
    }

    // Create checkout data
    const checkoutData = {
        items: cart.items,
        total: cart.getTotal(),
        savings: cart.items.reduce((total, item) => {
            return total + ((item.originalPrice || item.price) - item.price) * item.quantity;
        }, 0),
        timestamp: new Date().getTime()
    };

    // Encrypt and encode for URL
    const encrypted = CartEncryption.encode(JSON.stringify(checkoutData));
    
    // Redirect to checkout
    window.location.href = 'checkout.php?data=' + encodeURIComponent(encrypted);
}

// Add cart bounce animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes cartBounce {
        0%, 100% { transform: scale(1); }
        25% { transform: scale(1.2); }
        50% { transform: scale(0.9); }
        75% { transform: scale(1.1); }
    }

    .cart-notification {
        position: fixed;
        top: 100px;
        right: -400px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        font-weight: 600;
        transition: right 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        min-width: 300px;
    }

    .cart-notification.show {
        right: 20px;
    }

    .cart-notification-success {
        border-left: 4px solid #2F5233;
        color: #2F5233;
    }

    .cart-notification-error {
        border-left: 4px solid #D9232D;
        color: #D9232D;
    }

    .cart-notification-info {
        border-left: 4px solid #2196F3;
        color: #2196F3;
    }

    .cart-notification i {
        font-size: 1.2rem;
    }
`;
document.head.appendChild(style);

// Export for use in other files
window.CartEncryption = CartEncryption;
window.cart = cart;