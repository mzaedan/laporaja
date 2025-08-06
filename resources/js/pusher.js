import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Enable pusher logging
Pusher.logToConsole = true;

console.log('Pusher script loaded');

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Function to show a notification
function showNotification(data) {
    // Check if the browser supports notifications
    if (!('Notification' in window)) {
        console.log('This browser does not support desktop notification');
        return;
    }

    // Check if notification permissions have already been granted
    if (Notification.permission === 'granted') {
        createNotification(data);
    } 
    // Otherwise, ask the user for permission
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                createNotification(data);
            }
        });
    }
}

// Function to create and show a notification
function createNotification(data) {
    const title = `Status Laporan Diperbarui: ${data.report.title}`;
    const body = `Status: ${data.status}\n${data.description}`;
    
    const notification = new Notification(title, {
        body: body,
        icon: '/images/logo.png', // Update this path to your logo
        tag: `report-${data.report_id}-${data.created_at}`
    });

    // Handle notification click
    notification.onclick = function() {
        window.focus();
        // You can customize this URL based on your routes
        window.open(`/report/${data.report.code}`, '_blank');
        notification.close();
    };
}

// Listen for the authenticated user's broadcast channel
const userId = document.head.querySelector('meta[name="user-id"]');
console.log('User ID from meta tag:', userId ? userId.content : 'Not found');

if (userId && userId.content) {
    const channelName = `report-status.${userId.content}`;
    console.log('Attempting to subscribe to private channel:', channelName);
    
    try {
        const channel = window.Echo.private(channelName);
        console.log('Echo private channel created:', channel);
        
        channel.listen('.report.status.updated', (data) => {
            console.log('Report status updated event received:', data);
            showNotification(data);
        });
        
        console.log('Successfully subscribed to channel');
    } catch (error) {
        console.error('Error subscribing to channel:', error);
    }
} else {
    console.warn('No user ID found, not subscribing to any channel');
}
