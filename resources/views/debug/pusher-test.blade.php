@extends('layouts.app')

@section('title', 'Pusher Debug Test')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Pusher Notification Debug Test</h1>
        
        <!-- Status Display -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Connection Status</h3>
                <div id="connection-status" class="text-sm">
                    <span class="text-yellow-600">⏳ Checking...</span>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-2">User Info</h3>
                <div class="text-sm">
                    <p><strong>User ID:</strong> <span id="user-id">{{ Auth::id() ?? 'Not logged in' }}</span></p>
                    <p><strong>Channel:</strong> <span id="channel-name">report-status.{{ Auth::id() ?? 'N/A' }}</span></p>
                </div>
            </div>
        </div>
        
        <!-- Test Controls -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Test Controls</h3>
            
            <div class="flex flex-wrap gap-3">
                <button onclick="testNotificationPermission()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Test Browser Notification
                </button>
                
                <button onclick="testPusherEvent()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Send Test Pusher Event
                </button>
                
                <button onclick="checkPusherConfig()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
                    Check Pusher Config
                </button>
                
                <button onclick="clearLogs()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Clear Logs
                </button>
            </div>
        </div>
        
        <!-- Logs Display -->
        <div class="bg-gray-900 text-green-400 p-4 rounded-lg h-96 overflow-y-auto font-mono text-sm">
            <div id="debug-logs">
                <div class="text-yellow-400">[INFO] Debug console initialized</div>
            </div>
        </div>
    </div>
</div>

<script>
let logContainer = null;

document.addEventListener('DOMContentLoaded', function() {
    logContainer = document.getElementById('debug-logs');
    
    // Override console methods to show in our debug area
    const originalLog = console.log;
    const originalError = console.error;
    const originalWarn = console.warn;
    
    console.log = function(...args) {
        originalLog.apply(console, args);
        addLog('INFO', args.join(' '), 'text-green-400');
    };
    
    console.error = function(...args) {
        originalError.apply(console, args);
        addLog('ERROR', args.join(' '), 'text-red-400');
    };
    
    console.warn = function(...args) {
        originalWarn.apply(console, args);
        addLog('WARN', args.join(' '), 'text-yellow-400');
    };
    
    // Check Echo initialization
    setTimeout(() => {
        if (window.Echo) {
            addLog('SUCCESS', 'Echo is initialized', 'text-green-400');
            updateConnectionStatus('✅ Echo Initialized', 'text-green-600');
            
            // Test channel subscription
            testChannelSubscription();
        } else {
            addLog('ERROR', 'Echo is not initialized', 'text-red-400');
            updateConnectionStatus('❌ Echo Not Found', 'text-red-600');
        }
    }, 1000);
});

function addLog(type, message, colorClass) {
    if (!logContainer) return;
    
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = document.createElement('div');
    logEntry.className = colorClass;
    logEntry.innerHTML = `[${timestamp}] [${type}] ${message}`;
    
    logContainer.appendChild(logEntry);
    logContainer.scrollTop = logContainer.scrollHeight;
}

function updateConnectionStatus(message, className) {
    const statusEl = document.getElementById('connection-status');
    if (statusEl) {
        statusEl.innerHTML = `<span class="${className}">${message}</span>`;
    }
}

function testNotificationPermission() {
    addLog('INFO', 'Testing browser notification permission...', 'text-blue-400');
    
    if (!('Notification' in window)) {
        addLog('ERROR', 'Browser does not support notifications', 'text-red-400');
        return;
    }
    
    if (Notification.permission === 'granted') {
        addLog('SUCCESS', 'Notification permission already granted', 'text-green-400');
        showTestNotification();
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                addLog('SUCCESS', 'Notification permission granted', 'text-green-400');
                showTestNotification();
            } else {
                addLog('ERROR', 'Notification permission denied', 'text-red-400');
            }
        });
    } else {
        addLog('ERROR', 'Notification permission is denied', 'text-red-400');
    }
}

function showTestNotification() {
    const notification = new Notification('Test Notification', {
        body: 'This is a test notification from Laporaja',
        icon: '/favicon.ico'
    });
    
    setTimeout(() => notification.close(), 3000);
}

function testPusherEvent() {
    addLog('INFO', 'Sending test Pusher event...', 'text-blue-400');
    
    fetch('/test-pusher')
        .then(response => response.text())
        .then(data => {
            addLog('SUCCESS', 'Test event response: ' + data, 'text-green-400');
        })
        .catch(error => {
            addLog('ERROR', 'Failed to send test event: ' + error.message, 'text-red-400');
        });
}

function checkPusherConfig() {
    addLog('INFO', 'Checking Pusher configuration...', 'text-blue-400');
    
    const config = {
        key: window.PUSHER_APP_KEY || 'undefined',
        cluster: window.PUSHER_APP_CLUSTER || 'undefined',
        echo: typeof window.Echo !== 'undefined' ? 'initialized' : 'not found'
    };
    
    addLog('CONFIG', `Key: ${config.key.substring(0, 8)}...`, 'text-cyan-400');
    addLog('CONFIG', `Cluster: ${config.cluster}`, 'text-cyan-400');
    addLog('CONFIG', `Echo: ${config.echo}`, 'text-cyan-400');
}

function testChannelSubscription() {
    const userId = '{{ Auth::id() }}';
    if (!userId) {
        addLog('ERROR', 'No user ID found for channel subscription', 'text-red-400');
        return;
    }
    
    const channelName = `report-status.${userId}`;
    addLog('INFO', `Testing channel subscription: ${channelName}`, 'text-blue-400');
    
    try {
        if (window.Echo) {
            const channel = window.Echo.private(channelName);
            addLog('SUCCESS', 'Successfully created private channel', 'text-green-400');
            
            channel.listen('.report.status.updated', (data) => {
                addLog('EVENT', 'Received report status update: ' + JSON.stringify(data), 'text-yellow-400');
            });
        }
    } catch (error) {
        addLog('ERROR', 'Failed to subscribe to channel: ' + error.message, 'text-red-400');
    }
}

function clearLogs() {
    if (logContainer) {
        logContainer.innerHTML = '<div class="text-yellow-400">[INFO] Logs cleared</div>';
    }
}
</script>
@endsection