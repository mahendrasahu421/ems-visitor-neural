// Firebase Messaging Service Worker
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyC24ALS3EZuiX2XkRaAFV6AjvG60n7fRag",
  authDomain: "vprotect-3e20f.firebaseapp.com",
  projectId: "vprotect-3e20f",
  storageBucket: "vprotect-3e20f.firebasestorage.app",
  messagingSenderId: "996502919692",
  appId: "1:996502919692:web:22f8ffa3a34e4dc6a92c29",
  measurementId: "G-595N30P7Y0"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message: ', payload);

  const title = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'HRMS Notification';
  const body = (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || 'You have a new update.';
  const icon = (payload.notification && payload.notification.icon) || '/frontent/assets/img/favicon.png';
  const actionUrl = (payload.data && payload.data.action_url) || (payload.fcmOptions && payload.fcmOptions.link) || '/';

  const notificationOptions = {
    body: body,
    icon: icon,
    badge: '/frontent/assets/img/favicon.png',
    data: {
      url: actionUrl
    },
    requireInteraction: false
  };

  return self.registration.showNotification(title, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  const targetUrl = (event.notification.data && event.notification.data.url) || '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
      for (var i = 0; i < clientList.length; i++) {
        var client = clientList[i];
        if (client.url === targetUrl && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
