<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'نظام إدارة الحضور' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .code-display {
            font-family: 'Courier New', monospace;
            font-size: 4rem;
            font-weight: bold;
            letter-spacing: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .countdown {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .refresh-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        {{ $slot }}
    </div>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Auto-refresh Script -->
    <script>
        // تحديث تلقائي كل 5 ثواني
        setInterval(function() {
            Livewire.dispatch('refresh-code');
        }, 5000);
        
        // عداد تنازلي
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            if (!countdownElement) return;
            
            let seconds = parseInt(countdownElement.textContent);
            
            const timer = setInterval(function() {
                seconds--;
                if (countdownElement) {
                    countdownElement.textContent = seconds;
                }
                
                if (seconds <= 0) {
                    clearInterval(timer);
                    // إعادة تعيين العداد
                    setTimeout(startCountdown, 1000);
                }
            }, 1000);
        }
        
        // بدء العداد عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', startCountdown);
        
        // إعادة بدء العداد عند تحديث الكود
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('code-refreshed', (event) => {
                console.log('تم تحديث الكود:', event.newCode, 'في الوقت:', event.timestamp);
                startCountdown();
            });
        });
    </script>
</body>
</html>