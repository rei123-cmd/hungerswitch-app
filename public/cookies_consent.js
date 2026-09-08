// File: cookies_consent.js

/**
 * Hungerswitch Cookie Consent Manager
 * Mengelola persetujuan pengguna terhadap penggunaan cookies
 */

class CookieConsent {
    constructor() {
        this.consentGiven = this.checkConsent();
        this.init();
    }

    // Cek apakah user sudah memberikan consent
    checkConsent() {
        return localStorage.getItem('hs_cookie_consent') === 'accepted';
    }

    // Inisialisasi banner consent
    init() {
        if (!this.consentGiven) {
            this.showBanner();
        } else {
            this.loadAnalytics();
        }
    }

    // Tampilkan banner consent
    showBanner() {
        // Cek apakah banner sudah ada
        if (document.getElementById('cookieConsentBanner')) return;

        const banner = document.createElement('div');
        banner.id = 'cookieConsentBanner';
        banner.innerHTML = `
            <div class="cookie-consent-overlay">
                <div class="cookie-consent-banner">
                    <div class="cookie-consent-content">
                        <div class="cookie-icon">
                            <i class="bi bi-cookie"></i>
                        </div>
                        <div class="cookie-text">
                            <h4>🍪 Kami Menggunakan Cookies</h4>
                            <p>Hungerswitch menggunakan cookies untuk meningkatkan pengalaman Anda, menganalisis traffic, dan menampilkan konten yang relevan. Dengan melanjutkan, Anda menyetujui penggunaan cookies kami.</p>
                            <a href="cookies_policy.php" target="_blank" class="cookie-learn-more">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="cookie-actions">
                        <button id="cookieAccept" class="btn-cookie btn-cookie-accept">
                            <i class="bi bi-check-circle me-2"></i>Terima Semua
                        </button>
                        <button id="cookieCustomize" class="btn-cookie btn-cookie-customize">
                            <i class="bi bi-gear me-2"></i>Kelola Preferensi
                        </button>
                        <button id="cookieReject" class="btn-cookie btn-cookie-reject">
                            <i class="bi bi-x-circle me-2"></i>Tolak
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(banner);
        this.addStyles();
        this.attachEvents();
    }

    // Tambahkan CSS untuk banner
    addStyles() {
        if (document.getElementById('cookieConsentStyles')) return;

        const style = document.createElement('style');
        style.id = 'cookieConsentStyles';
        style.textContent = `
            .cookie-consent-overlay {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(5px);
                z-index: 99999;
                padding: 1rem;
                animation: slideUp 0.4s ease;
            }

            @keyframes slideUp {
                from {
                    transform: translateY(100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .cookie-consent-banner {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                border-radius: 20px;
                padding: 2rem;
                box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
            }

            .cookie-consent-content {
                display: flex;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
                align-items: flex-start;
            }

            .cookie-icon {
                font-size: 3rem;
                color: #FF9800;
                flex-shrink: 0;
            }

            .cookie-text h4 {
                font-family: 'Poppins', sans-serif;
                font-weight: 700;
                margin-bottom: 0.5rem;
                color: #333;
            }

            .cookie-text p {
                font-family: 'Poppins', sans-serif;
                color: #666;
                line-height: 1.6;
                margin-bottom: 0.5rem;
            }

            .cookie-learn-more {
                color: #2F5233;
                font-weight: 600;
                text-decoration: none;
                border-bottom: 2px solid #2F5233;
                transition: all 0.3s ease;
            }

            .cookie-learn-more:hover {
                color: #1a2e1d;
                border-bottom-color: #1a2e1d;
            }

            .cookie-actions {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .btn-cookie {
                font-family: 'Poppins', sans-serif;
                padding: 0.75rem 1.5rem;
                border-radius: 12px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                font-size: 0.95rem;
            }

            .btn-cookie-accept {
                background: linear-gradient(135deg, #2F5233, #3d6b42);
                color: white;
                box-shadow: 0 4px 15px rgba(47, 82, 51, 0.3);
            }

            .btn-cookie-accept:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(47, 82, 51, 0.4);
            }

            .btn-cookie-customize {
                background: white;
                color: #2F5233;
                border: 2px solid #2F5233;
            }

            .btn-cookie-customize:hover {
                background: #2F5233;
                color: white;
            }

            .btn-cookie-reject {
                background: #f5f5f5;
                color: #666;
            }

            .btn-cookie-reject:hover {
                background: #e0e0e0;
                color: #333;
            }

            @media (max-width: 768px) {
                .cookie-consent-banner {
                    padding: 1.5rem;
                }

                .cookie-consent-content {
                    flex-direction: column;
                    text-align: center;
                }

                .cookie-icon {
                    font-size: 2.5rem;
                }

                .cookie-actions {
                    flex-direction: column;
                }

                .btn-cookie {
                    width: 100%;
                    justify-content: center;
                }
            }
        `;

        document.head.appendChild(style);
    }

    // Attach event listeners
    attachEvents() {
        document.getElementById('cookieAccept')?.addEventListener('click', () => {
            this.acceptAll();
        });

        document.getElementById('cookieReject')?.addEventListener('click', () => {
            this.rejectAll();
        });

        document.getElementById('cookieCustomize')?.addEventListener('click', () => {
            this.showCustomizeModal();
        });
    }

    // Accept all cookies
    acceptAll() {
        localStorage.setItem('hs_cookie_consent', 'accepted');
        localStorage.setItem('hs_cookie_preferences', JSON.stringify({
            necessary: true,
            analytics: true,
            marketing: true,
            functional: true
        }));
        
        this.loadAnalytics();
        this.removeBanner();
        this.showNotification('Preferensi cookies berhasil disimpan!', 'success');
    }

    // Reject non-essential cookies
    rejectAll() {
        localStorage.setItem('hs_cookie_consent', 'rejected');
        localStorage.setItem('hs_cookie_preferences', JSON.stringify({
            necessary: true,
            analytics: false,
            marketing: false,
            functional: false
        }));
        
        this.removeBanner();
        this.showNotification('Hanya cookies penting yang akan digunakan', 'info');
    }

    // Show customize modal
    showCustomizeModal() {
        const modal = document.createElement('div');
        modal.id = 'cookieCustomizeModal';
        modal.innerHTML = `
            <div class="cookie-modal-overlay">
                <div class="cookie-modal">
                    <div class="cookie-modal-header">
                        <h3>Kelola Preferensi Cookies</h3>
                        <button class="cookie-modal-close">&times;</button>
                    </div>
                    <div class="cookie-modal-body">
                        <div class="cookie-category">
                            <div class="cookie-category-header">
                                <label>
                                    <input type="checkbox" checked disabled>
                                    <strong>Cookies yang Diperlukan</strong>
                                </label>
                            </div>
                            <p class="cookie-category-desc">Cookies ini sangat penting untuk fungsi website dan tidak dapat dinonaktifkan.</p>
                        </div>

                        <div class="cookie-category">
                            <div class="cookie-category-header">
                                <label>
                                    <input type="checkbox" id="analyticsToggle" checked>
                                    <strong>Cookies Analitik</strong>
                                </label>
                            </div>
                            <p class="cookie-category-desc">Membantu kami memahami bagaimana pengunjung berinteraksi dengan website.</p>
                        </div>

                        <div class="cookie-category">
                            <div class="cookie-category-header">
                                <label>
                                    <input type="checkbox" id="marketingToggle">
                                    <strong>Cookies Marketing</strong>
                                </label>
                            </div>
                            <p class="cookie-category-desc">Digunakan untuk menampilkan iklan yang relevan dengan minat Anda.</p>
                        </div>

                        <div class="cookie-category">
                            <div class="cookie-category-header">
                                <label>
                                    <input type="checkbox" id="functionalToggle" checked>
                                    <strong>Cookies Fungsional</strong>
                                </label>
                            </div>
                            <p class="cookie-category-desc">Menyimpan preferensi Anda untuk pengalaman yang lebih personal.</p>
                        </div>
                    </div>
                    <div class="cookie-modal-footer">
                        <button id="savePreferences" class="btn-cookie btn-cookie-accept">Simpan Preferensi</button>
                        <button id="cancelPreferences" class="btn-cookie btn-cookie-reject">Batal</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.addModalStyles();

        // Event listeners untuk modal
        document.querySelector('.cookie-modal-close')?.addEventListener('click', () => {
            document.getElementById('cookieCustomizeModal')?.remove();
        });

        document.getElementById('cancelPreferences')?.addEventListener('click', () => {
            document.getElementById('cookieCustomizeModal')?.remove();
        });

        document.getElementById('savePreferences')?.addEventListener('click', () => {
            this.saveCustomPreferences();
        });
    }

    // Add modal styles
    addModalStyles() {
        if (document.getElementById('cookieModalStyles')) return;

        const style = document.createElement('style');
        style.id = 'cookieModalStyles';
        style.textContent = `
            .cookie-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 100000;
                padding: 1rem;
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .cookie-modal {
                background: white;
                border-radius: 20px;
                max-width: 600px;
                width: 100%;
                max-height: 90vh;
                overflow-y: auto;
                animation: scaleIn 0.3s ease;
            }

            @keyframes scaleIn {
                from {
                    transform: scale(0.9);
                    opacity: 0;
                }
                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .cookie-modal-header {
                padding: 1.5rem;
                border-bottom: 2px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .cookie-modal-header h3 {
                font-family: 'Poppins', sans-serif;
                font-weight: 700;
                margin: 0;
                color: #333;
            }

            .cookie-modal-close {
                background: none;
                border: none;
                font-size: 2rem;
                cursor: pointer;
                color: #999;
                line-height: 1;
                transition: color 0.3s ease;
            }

            .cookie-modal-close:hover {
                color: #333;
            }

            .cookie-modal-body {
                padding: 1.5rem;
            }

            .cookie-category {
                margin-bottom: 1.5rem;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 12px;
            }

            .cookie-category-header {
                margin-bottom: 0.5rem;
            }

            .cookie-category-header label {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                cursor: pointer;
                font-family: 'Poppins', sans-serif;
            }

            .cookie-category-header input[type="checkbox"] {
                width: 20px;
                height: 20px;
                cursor: pointer;
            }

            .cookie-category-desc {
                font-family: 'Poppins', sans-serif;
                color: #666;
                font-size: 0.9rem;
                margin: 0;
                padding-left: 2rem;
            }

            .cookie-modal-footer {
                padding: 1.5rem;
                border-top: 2px solid #f0f0f0;
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
            }
        `;

        document.head.appendChild(style);
    }

    // Save custom preferences
    saveCustomPreferences() {
        const preferences = {
            necessary: true,
            analytics: document.getElementById('analyticsToggle')?.checked || false,
            marketing: document.getElementById('marketingToggle')?.checked || false,
            functional: document.getElementById('functionalToggle')?.checked || false
        };

        localStorage.setItem('hs_cookie_consent', 'custom');
        localStorage.setItem('hs_cookie_preferences', JSON.stringify(preferences));

        if (preferences.analytics) {
            this.loadAnalytics();
        }

        this.removeBanner();
        document.getElementById('cookieCustomizeModal')?.remove();
        this.showNotification('Preferensi cookies berhasil disimpan!', 'success');
    }

    // Load analytics scripts (Google Analytics, Facebook Pixel, etc.)
    loadAnalytics() {
        // Google Analytics
        if (!document.getElementById('ga-script')) {
            const gaScript = document.createElement('script');
            gaScript.id = 'ga-script';
            gaScript.async = true;
            gaScript.src = 'https://www.googletagmanager.com/gtag/js?id=YOUR_GA_ID';
            document.head.appendChild(gaScript);

            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'YOUR_GA_ID');
        }

        console.log('Analytics loaded');
    }

    // Remove banner
    removeBanner() {
        const banner = document.getElementById('cookieConsentBanner');
        if (banner) {
            banner.style.animation = 'slideDown 0.4s ease';
            setTimeout(() => banner.remove(), 400);
        }
    }

    // Show notification
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `cookie-notification cookie-notification-${type}`;
        notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
        `;

        const style = document.createElement('style');
        style.textContent = `
            .cookie-notification {
                position: fixed;
                top: 100px;
                right: -400px;
                background: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                z-index: 100001;
                display: flex;
                align-items: center;
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                max-width: 350px;
            }

            .cookie-notification-success {
                border-left: 4px solid #2F5233;
                color: #2F5233;
            }

            .cookie-notification-info {
                border-left: 4px solid #2196F3;
                color: #2196F3;
            }

            .cookie-notification.show {
                right: 20px;
            }

            @keyframes slideDown {
                to {
                    transform: translateY(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 400);
        }, 3000);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new CookieConsent();
    });
} else {
    new CookieConsent();
}

// Export untuk digunakan di tempat lain jika diperlukan
window.CookieConsent = CookieConsent;