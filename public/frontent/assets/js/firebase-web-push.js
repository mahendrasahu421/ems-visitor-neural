/**
 * HRMS Firebase Web Push Notification Integration
 */
(function () {
    const firebaseConfig = {
        apiKey: "AIzaSyC24ALS3EZuiX2XkRaAFV6AjvG60n7fRag",
        authDomain: "vprotect-3e20f.firebaseapp.com",
        projectId: "vprotect-3e20f",
        storageBucket: "vprotect-3e20f.firebasestorage.app",
        messagingSenderId: "996502919692",
        appId: "1:996502919692:web:22f8ffa3a34e4dc6a92c29",
        measurementId: "G-595N30P7Y0"
    };

    // Check Service Worker and Notification support
    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
        console.warn('[FCM] Push notifications are not supported by this browser.');
        return;
    }

    // Function to load external scripts dynamically if needed
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${src}"]`)) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async function initFirebaseWebPush() {
        try {
            // Load Firebase compat scripts if not already present
            if (typeof firebase === 'undefined') {
                await loadScript('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
                await loadScript('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');
            }

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }

            const messaging = firebase.messaging();

            // Register Service Worker
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('[FCM] Service Worker registered successfully:', registration.scope);

            // Foreground Message Handler
            messaging.onMessage((payload) => {
                console.log('[FCM] Foreground message received:', payload);
                const title = payload.notification?.title || payload.data?.title || 'Notification';
                const body = payload.notification?.body || payload.data?.body || '';
                const url = payload.data?.action_url || payload.fcmOptions?.link || '#';

                showInAppNotification(title, body, url);
            });

            // If permission is already granted, refresh token and send to server
            if (Notification.permission === 'granted') {
                registerTokenWithServer(messaging, registration);
            } else if (Notification.permission === 'default') {
                showNotificationPermissionPrompt(messaging, registration);
            }

            // Expose globally for manual trigger buttons
            window.requestHrmsNotificationPermission = () => {
                requestPermissionAndRegister(messaging, registration);
            };

        } catch (error) {
            console.error('[FCM] Error initializing Firebase Web Push:', error);
        }
    }

    async function requestPermissionAndRegister(messaging, registration) {
        try {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                console.log('[FCM] Notification permission granted.');
                await registerTokenWithServer(messaging, registration);
                const promptBox = document.getElementById('hrms-fcm-prompt-banner');
                if (promptBox) promptBox.remove();
                showInAppNotification('Notifications Enabled!', 'You will now receive real-time push alerts on this browser.', '#');
            } else {
                console.warn('[FCM] Notification permission denied or closed.');
            }
        } catch (e) {
            console.error('[FCM] Error requesting permission:', e);
        }
    }

    async function registerTokenWithServer(messaging, registration) {
        try {
            const token = await messaging.getToken({
                serviceWorkerRegistration: registration
            });

            if (token) {
                console.log('[FCM] Device Token obtained:', token.substring(0, 20) + '...');
                sendTokenToServer(token);
            } else {
                console.warn('[FCM] No registration token available. Request permission to generate one.');
            }
        } catch (err) {
            console.error('[FCM] An error occurred while retrieving token:', err);
        }
    }

    function sendTokenToServer(token) {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        fetch('/admin/fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                fcm_token: token,
                device_name: navigator.userAgent.substring(0, 150)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('[FCM] Web Push token synced with server.');
            }
        })
        .catch(err => {
            console.warn('[FCM] Could not sync token with server:', err);
        });
    }

    function showNotificationPermissionPrompt(messaging, registration) {
        if (sessionStorage.getItem('hrms_fcm_prompt_dismissed')) return;

        const banner = document.createElement('div');
        banner.id = 'hrms-fcm-prompt-banner';
        banner.style.position = 'fixed';
        banner.style.bottom = '20px';
        banner.style.right = '20px';
        banner.style.zIndex = '999999';
        banner.style.maxWidth = '380px';
        banner.style.background = '#ffffff';
        banner.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
        banner.style.borderRadius = '12px';
        banner.style.border = '1px solid #e2e8f0';
        banner.style.padding = '16px';
        banner.style.fontFamily = 'inherit';
        banner.innerHTML = `
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="width:40px; height:40px; border-radius:50%; background:#e0f2fe; color:#0284c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="ti ti-bell-ringing" style="font-size:22px;"></i>
                </div>
                <div style="flex:1;">
                    <h5 style="margin:0 0 4px 0; font-size:15px; font-weight:600; color:#1e293b;">Enable Desktop Alerts</h5>
                    <p style="margin:0 0 12px 0; font-size:13px; color:#64748b; line-height:1.4;">Get instant push notifications for attendance, leaves, and team updates.</p>
                    <div style="display:flex; gap:8px;">
                        <button id="hrms-fcm-enable-btn" style="background:#0d6efd; color:#fff; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">Enable</button>
                        <button id="hrms-fcm-dismiss-btn" style="background:#f1f5f9; color:#475569; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer;">Later</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(banner);

        document.getElementById('hrms-fcm-enable-btn').addEventListener('click', () => {
            requestPermissionAndRegister(messaging, registration);
        });

        document.getElementById('hrms-fcm-dismiss-btn').addEventListener('click', () => {
            sessionStorage.setItem('hrms_fcm_prompt_dismissed', '1');
            banner.remove();
        });
    }

    function showInAppNotification(title, body, url) {
        // Create floating toast
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.top = '25px';
        toast.style.right = '25px';
        toast.style.zIndex = '999999';
        toast.style.maxWidth = '360px';
        toast.style.background = '#1e293b';
        toast.style.color = '#ffffff';
        toast.style.borderRadius = '10px';
        toast.style.padding = '14px 16px';
        toast.style.boxShadow = '0 12px 30px rgba(0,0,0,0.25)';
        toast.style.cursor = url && url !== '#' ? 'pointer' : 'default';
        toast.style.transition = 'all 0.3s ease';

        toast.innerHTML = `
            <div style="display:flex; align-items:flex-start; gap:10px;">
                <span style="color:#38bdf8; font-size:20px; line-height:1;">🔔</span>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:14px; margin-bottom:2px;">${escapeHtml(title)}</div>
                    <div style="font-size:12px; color:#cbd5e1; line-height:1.3;">${escapeHtml(body)}</div>
                </div>
                <button type="button" style="background:transparent; border:none; color:#94a3b8; font-size:16px; cursor:pointer; line-height:1;">&times;</button>
            </div>
        `;

        if (url && url !== '#') {
            toast.addEventListener('click', (e) => {
                if (e.target.tagName !== 'BUTTON') {
                    window.location.href = url;
                }
            });
        }

        toast.querySelector('button').addEventListener('click', (e) => {
            e.stopPropagation();
            toast.remove();
        });

        document.body.appendChild(toast);

        // Auto remove after 6 seconds
        setTimeout(() => {
            if (document.body.contains(toast)) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }
        }, 6000);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Start on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFirebaseWebPush);
    } else {
        initFirebaseWebPush();
    }
})();
