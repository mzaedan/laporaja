import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Enable pusher logging
Pusher.logToConsole = true;

console.log('🚀 Pusher script loaded');

// Connection state tracking
window.pusherState = {
    connected: false,
    subscribed: false,
    lastEventTime: null,
    connectionAttempts: 0,
    maxRetries: 5,
    retryDelay: 2000
};

// Use window configuration if available (for production), otherwise use Vite env
const pusherKey = window.PUSHER_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = window.PUSHER_APP_CLUSTER || import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1';

console.log('🔧 Pusher Configuration:', {
    key: pusherKey ? pusherKey.substring(0, 8) + '...' : 'undefined',
    cluster: pusherCluster,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ? 'Found' : 'Missing'
});

// Initialize Echo with enhanced connection monitoring
function initializeEcho() {
    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster,
            wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${pusherCluster}.pusher.com`,
            wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
            wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            }
        });
        
        // Set up connection event listeners
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Pusher connected successfully');
            window.pusherState.connected = true;
            window.pusherState.connectionAttempts = 0;
            updateConnectionStatus('connected');
        });
        
        window.Echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('❌ Pusher disconnected');
            window.pusherState.connected = false;
            window.pusherState.subscribed = false;
            updateConnectionStatus('disconnected');
        });
        
        window.Echo.connector.pusher.connection.bind('error', (error) => {
            console.error('❌ Pusher connection error:', error);
            window.pusherState.connected = false;
            updateConnectionStatus('error');
            
            // Implement retry logic
            if (window.pusherState.connectionAttempts < window.pusherState.maxRetries) {
                window.pusherState.connectionAttempts++;
                console.log(`🔄 Retrying connection (${window.pusherState.connectionAttempts}/${window.pusherState.maxRetries})...`);
                setTimeout(() => {
                    if (!window.pusherState.connected) {
                        window.Echo.connector.pusher.connect();
                    }
                }, window.pusherState.retryDelay * window.pusherState.connectionAttempts);
            }
        });
        
        console.log('🎯 Echo initialized successfully');
        return true;
    } catch (error) {
        console.error('❌ Failed to initialize Echo:', error);
        return false;
    }
}

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

// Function to update connection status indicator
function updateConnectionStatus(status) {
    const statusElement = document.getElementById('pusher-status');
    if (statusElement) {
        const statusText = {
            'connected': '✅ Connected',
            'disconnected': '❌ Disconnected', 
            'error': '⚠️ Error',
            'connecting': '🔄 Connecting...'
        };
        statusElement.textContent = statusText[status] || status;
        statusElement.className = `pusher-status pusher-status-${status}`;
    }
    
    // Also log to our custom debug console
    addToDebugConsole(`Connection status: ${status}`);
}

// Enhanced notification setup with retry logic
function setupNotificationChannel() {
    const userId = document.head.querySelector('meta[name="user-id"]');
    console.log('🔍 User ID from meta tag:', userId ? userId.content : 'Not found');

    if (!userId || !userId.content) {
        console.warn('⚠️ No user ID found, not subscribing to any channel');
        addToDebugConsole('No user ID found - cannot subscribe to notifications');
        return;
    }
    
    if (!window.Echo) {
        console.error('❌ Echo not initialized, cannot setup notifications');
        addToDebugConsole('Echo not initialized - retrying in 2 seconds');
        setTimeout(setupNotificationChannel, 2000);
        return;
    }

    const channelName = `report-status.${userId.content}`;
    console.log('🔗 Attempting to subscribe to private channel:', channelName);
    addToDebugConsole(`Subscribing to channel: ${channelName}`);
    
    try {
        const channel = window.Echo.private(channelName);
        console.log('📡 Echo private channel created:', channel);
        
        // Add subscription success callback
        channel.subscribed(() => {
            console.log('✅ Successfully subscribed to channel:', channelName);
            window.pusherState.subscribed = true;
            addToDebugConsole(`✅ Successfully subscribed to ${channelName}`);
            updateConnectionStatus('connected');
        });
        
        // Add subscription error callback
        channel.error((error) => {
            console.error('❌ Channel subscription error:', error);
            window.pusherState.subscribed = false;
            addToDebugConsole(`❌ Subscription error: ${JSON.stringify(error)}`);
            updateConnectionStatus('error');
            
            // Retry subscription after delay
            setTimeout(() => {
                console.log('🔄 Retrying channel subscription...');
                setupNotificationChannel();
            }, 3000);
        });
        
        // Listen for the specific event (with dot prefix)
        channel.listen('.report.status.updated', (data) => {
            console.log('🔔 Report status updated event received (with dot):', data);
            console.log('📊 Event data structure:', JSON.stringify(data, null, 2));
            window.pusherState.lastEventTime = new Date();
            addToDebugConsole(`🔔 Event received: ${data.report?.title || 'Unknown'} - ${data.status}`);
            
            // Test browser notification permission first
            console.log('🔒 Notification permission:', Notification.permission);
            
            showNotification(data);
            showInPageNotification(data);
        });
        
        // Also try listening without the dot prefix (fallback)
        channel.listen('report.status.updated', (data) => {
            console.log('🔔 Report status updated event received (without dot):', data);
            console.log('📊 Event data structure:', JSON.stringify(data, null, 2));
            window.pusherState.lastEventTime = new Date();
            addToDebugConsole(`🔔 Event received (no dot): ${data.report?.title || 'Unknown'} - ${data.status}`);
            
            showNotification(data);
            showInPageNotification(data);
        });
        
        // Listen for any event on this channel for debugging
        channel.notification((notification) => {
            console.log('📢 Any notification received:', notification);
            addToDebugConsole(`📢 Raw notification: ${JSON.stringify(notification)}`);
        });
        
        console.log('✅ Event listeners attached successfully');
        addToDebugConsole('✅ Event listeners attached successfully');
        
        // Store channel reference for debugging
        window.notificationChannel = channel;
        
    } catch (error) {
        console.error('❌ Error setting up channel:', error);
        addToDebugConsole(`❌ Channel setup error: ${error.message}`);
        updateConnectionStatus('error');
    }
}

// Function to show in-page notification
function showInPageNotification(data) {
    // Create notification banner
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm';
    notification.style.cssText = `
        position: fixed;
        top: 16px;
        right: 16px;
        background-color: #2563eb;
        color: white;
        padding: 16px;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 50;
        max-width: 384px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-1">
                <h4 class="font-semibold mb-1">Status Laporan Diperbarui</h4>
                <p class="text-sm mb-1">${data.report.title}</p>
                <p class="text-xs opacity-90">Status: ${data.status}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                ×
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS for animation
if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}

// Global test functions for debugging
window.testNotifications = function() {
    console.log('🧪 Testing notifications...');
    
    const testData = {
        report: {
            title: 'Test Report',
            code: 'TEST-001'
        },
        status: 'Test Status',
        description: 'This is a test notification',
        report_id: 1,
        created_at: new Date().toISOString()
    };
    
    console.log('🔔 Testing browser notification...');
    showNotification(testData);
    
    console.log('📱 Testing in-page notification...');
    showInPageNotification(testData);
};

// Enhanced debugging functions with health monitoring
window.debugPusher = function() {
    console.log('🔧 Pusher Debug Information:');
    console.log('Echo instance:', window.Echo);
    console.log('Pusher connection state:', window.Echo?.connector?.pusher?.connection?.state);
    console.log('Socket ID:', window.Echo?.connector?.pusher?.connection?.socket_id);
    console.log('User ID:', document.querySelector('meta[name="user-id"]')?.getAttribute('content'));
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ? 'Present' : 'Missing');
    console.log('Notification permission:', Notification.permission);
    console.log('Connection state:', window.pusherState);
    
    addToDebugConsole('Debug info logged to console');
};

window.testPusherConnection = function() {
    const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    if (!userId) {
        console.error('❌ No user ID found for testing');
        addToDebugConsole('❌ No user ID found for testing');
        return;
    }
    
    const channelName = `report-status.${userId}`;
    console.log('🔗 Testing connection to channel:', channelName);
    addToDebugConsole(`Testing connection to: ${channelName}`);
    
    try {
        const testChannel = window.Echo.private(channelName);
        testChannel.subscribed(() => {
            console.log('✅ Test channel subscription successful');
            addToDebugConsole('✅ Test channel subscription successful');
        });
        testChannel.error((error) => {
            console.error('❌ Test channel subscription failed:', error);
            addToDebugConsole(`❌ Test channel failed: ${JSON.stringify(error)}`);
        });
    } catch (error) {
        console.error('❌ Error creating test channel:', error);
        addToDebugConsole(`❌ Test channel error: ${error.message}`);
    }
};

// Health check function
window.checkConnectionHealth = function() {
    const state = window.Echo?.connector?.pusher?.connection?.state;
    const isConnected = state === 'connected';
    const lastEventAge = window.pusherState.lastEventTime ? 
        (new Date() - window.pusherState.lastEventTime) / 1000 : null;
    
    console.log('💖 Connection Health Check:');
    console.log('- Connection state:', state);
    console.log('- Is connected:', isConnected);
    console.log('- Is subscribed:', window.pusherState.subscribed);
    console.log('- Connection attempts:', window.pusherState.connectionAttempts);
    console.log('- Last event:', lastEventAge ? `${lastEventAge.toFixed(1)}s ago` : 'Never');
    
    addToDebugConsole(`Health: ${state} | Subscribed: ${window.pusherState.subscribed} | Last event: ${lastEventAge ? lastEventAge.toFixed(1) + 's ago' : 'Never'}`);
    
    // Auto-reconnect if needed
    if (!isConnected && window.pusherState.connectionAttempts < window.pusherState.maxRetries) {
        console.log('🔄 Auto-reconnecting...');
        window.Echo.connector.pusher.connect();
    }
    
    return {
        connected: isConnected,
        subscribed: window.pusherState.subscribed,
        state: state,
        lastEventAge: lastEventAge
    };
};

// Heartbeat mechanism
function startHeartbeat() {
    setInterval(() => {
        if (window.Echo) {
            const health = window.checkConnectionHealth();
            
            // If disconnected for more than 10 seconds, try to reconnect
            if (!health.connected) {
                console.log('💔 Heartbeat detected disconnection, attempting reconnect...');
                addToDebugConsole('💔 Heartbeat: Attempting reconnect');
                
                if (window.pusherState.connectionAttempts < window.pusherState.maxRetries) {
                    window.Echo.connector.pusher.connect();
                }
            }
            
            // If connected but not subscribed, retry subscription
            if (health.connected && !health.subscribed) {
                console.log('🔄 Heartbeat: Re-establishing channel subscription...');
                addToDebugConsole('🔄 Heartbeat: Re-subscribing to channel');
                setupNotificationChannel();
            }
        }
    }, 10000); // Check every 10 seconds
}

// Make functions globally accessible for debugging
window.showNotification = showNotification;
window.showInPageNotification = showInPageNotification;

// Global error handler for better debugging
window.addEventListener('error', function(event) {
    console.error('Global JavaScript Error:', {
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        error: event.error
    });
});

// Unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled Promise Rejection:', event.reason);
});

// Function to add messages to debug console (will be defined by test pages)
function addToDebugConsole(message) {
    // If debug console exists, add message
    const debugConsole = document.getElementById('debug-console');
    if (debugConsole) {
        const timestamp = new Date().toLocaleTimeString();
        const div = document.createElement('div');
        div.textContent = `[${timestamp}] ${message}`;
        debugConsole.appendChild(div);
        debugConsole.scrollTop = debugConsole.scrollHeight;
    }
    
    // Also log to browser console
    console.log(`📝 ${message}`);
}

// Setup channel subscription when DOM is ready with enhanced initialization
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('📝 DOM loaded, initializing Pusher system...');
        addToDebugConsole('DOM loaded, initializing Pusher system...');
        
        updateConnectionStatus('connecting');
        
        // Initialize Echo with retry logic
        if (initializeEcho()) {
            setTimeout(() => {
                setupNotificationChannel();
                startHeartbeat();
            }, 1000);
        } else {
            console.error('❌ Failed to initialize Echo');
            addToDebugConsole('❌ Failed to initialize Echo');
            updateConnectionStatus('error');
        }
    });
} else {
    console.log('📝 DOM already loaded, initializing Pusher system...');
    addToDebugConsole('DOM already loaded, initializing Pusher system...');
    
    updateConnectionStatus('connecting');
    
    if (initializeEcho()) {
        setTimeout(() => {
            setupNotificationChannel();
            startHeartbeat();
        }, 1000);
    } else {
        console.error('❌ Failed to initialize Echo');
        addToDebugConsole('❌ Failed to initialize Echo');
        updateConnectionStatus('error');
    }
}

// Add additional debug information
console.log('🔧 Pusher debugging info:', {
    'Echo available': typeof window.Echo !== 'undefined',
    'User authenticated': !!document.querySelector('meta[name="user-id"]')?.getAttribute('content'),
    'CSRF token available': !!document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    'Notification permission': typeof Notification !== 'undefined' ? Notification.permission : 'Not supported'
});
