# دليل تأمين الموقع في مركز الدروس - منع الوصول الخارجي
## Complete Security Guide for Training Center Website

هذا الدليل الشامل يوضح كيفية تأمين موقع نظام إدارة الدروس والحضور في مركز الدروس بحيث يكون متاحاً فقط للأشخاص المتصلين بشبكة المركز الداخلية.

---

## 📋 فهرس المحتويات

1. [فهم البنية التحتية للشبكة](#network-infrastructure)
2. [طبقات الحماية المتعددة](#security-layers)
3. [التطبيق العملي - Laravel Middleware](#laravel-implementation)
4. [إعدادات الخادم والشبكة](#server-network-config)
5. [إعدادات جدار الحماية](#firewall-configuration)
6. [مراقبة ومتابعة الأمان](#monitoring-security)
7. [خطة الطوارئ والنسخ الاحتياطي](#emergency-backup)
8. [اختبار شامل للنظام](#comprehensive-testing)

---

## 🌐 فهم البنية التحتية للشبكة {#network-infrastructure}

### هيكل الشبكة في مركز الدروس

```
الإنترنت العام
    ↓
راوتر المركز الرئيسي (Gateway)
    ↓
سويتش الشبكة الداخلية
    ↓
┌─────────────────────────────────────┐
│  الشبكة الداخلية للمركز              │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐ │
│  │ خادم    │  │ أجهزة   │  │ أجهزة   │ │
│  │ الموقع  │  │ الطلاب  │  │ الإدارة │ │
│  └─────────┘  └─────────┘  └─────────┘ │
└─────────────────────────────────────┘
```

### نطاقات IP الشائعة في المراكز:
- **Class C**: `192.168.1.0/24` (192.168.1.1 - 192.168.1.254)
- **Class B**: `172.16.0.0/16` (172.16.0.1 - 172.16.255.254)
- **Class A**: `10.0.0.0/8` (10.0.0.1 - 10.255.255.255)

---

## 🛡️ طبقات الحماية المتعددة {#security-layers}

### الطبقة الأولى: حماية الشبكة (Network Level)
- **جدار الحماية على الراوتر**
- **إعدادات NAT وPort Forwarding**
- **فلترة MAC Address**

### الطبقة الثانية: حماية الخادم (Server Level)
- **جدار حماية Windows/Linux**
- **إعدادات Apache/Nginx**
- **SSL/TLS Certificates**

### الطبقة الثالثة: حماية التطبيق (Application Level)
- **Laravel Middleware**
- **IP Whitelisting**
- **Session Management**

### الطبقة الرابعة: مراقبة ومتابعة (Monitoring Level)
- **تسجيل محاولات الوصول**
- **تنبيهات الأمان**
- **تحليل السلوك**

---

## 💻 التطبيق العملي - Laravel Middleware {#laravel-implementation}

### 1. إنشاء Middleware متقدم للحماية

```bash
php artisan make:middleware AdvancedNetworkSecurityMiddleware
```

### 2. كود الـ Middleware المتقدم

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AdvancedNetworkSecurityMiddleware
{
    /**
     * قائمة الشبكات المسموحة
     */
    private $allowedNetworks = [
        '192.168.1.0/24',    // الشبكة الرئيسية للمركز
        '192.168.2.0/24',    // شبكة الإدارة
        '10.0.0.0/8',        // شبكة احتياطية
        '127.0.0.1',         // localhost
        '::1'                // IPv6 localhost
    ];

    /**
     * معالجة الطلب الوارد
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $this->getRealClientIP($request);
        
        // تسجيل محاولة الوصول
        $this->logAccessAttempt($request, $clientIp);
        
        // فحص القائمة السوداء
        if ($this->isBlacklisted($clientIp)) {
            return $this->denyAccess($request, $clientIp, 'IP في القائمة السوداء');
        }
        
        // فحص معدل الطلبات (Rate Limiting)
        if ($this->isRateLimited($clientIp)) {
            return $this->denyAccess($request, $clientIp, 'تجاوز الحد المسموح من الطلبات');
        }
        
        // فحص الشبكات المسموحة
        if (!$this->isAllowedNetwork($clientIp)) {
            return $this->denyAccess($request, $clientIp, 'شبكة غير مصرح بها');
        }
        
        // فحص إضافي للأمان
        if (!$this->additionalSecurityChecks($request)) {
            return $this->denyAccess($request, $clientIp, 'فشل في فحوصات الأمان الإضافية');
        }
        
        // تسجيل الوصول الناجح
        $this->logSuccessfulAccess($request, $clientIp);
        
        return $next($request);
    }
    
    /**
     * الحصول على IP الحقيقي للعميل
     */
    private function getRealClientIP(Request $request): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load Balancer/Proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($headers as $header) {
            if ($request->server($header)) {
                $ips = explode(',', $request->server($header));
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $request->ip();
    }
    
    /**
     * فحص ما إذا كان IP في الشبكات المسموحة
     */
    private function isAllowedNetwork(string $ip): bool
    {
        // السماح للـ localhost دائماً
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }
        
        // فحص الشبكات المكونة في البيئة
        $configuredNetworks = config('app.allowed_networks', []);
        $allNetworks = array_merge($this->allowedNetworks, $configuredNetworks);
        
        foreach ($allNetworks as $network) {
            if ($this->ipInRange($ip, $network)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * فحص ما إذا كان IP ضمن النطاق المحدد
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }
        
        list($subnet, $bits) = explode('/', $range);
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // IPv6 handling
            return $this->ipv6InRange($ip, $range);
        }
        
        // IPv4 handling
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        
        return ($ip & $mask) == $subnet;
    }
    
    /**
     * فحص IPv6
     */
    private function ipv6InRange(string $ip, string $range): bool
    {
        list($subnet, $bits) = explode('/', $range);
        $subnet = inet_pton($subnet);
        $ip = inet_pton($ip);
        $binaryMask = str_repeat('f', $bits / 4);
        
        switch ($bits % 4) {
            case 0: break;
            case 1: $binaryMask .= '8'; break;
            case 2: $binaryMask .= 'c'; break;
            case 3: $binaryMask .= 'e'; break;
        }
        
        $binaryMask = str_pad($binaryMask, 32, '0');
        $binaryMask = pack('H*', $binaryMask);
        
        return ($ip & $binaryMask) === ($subnet & $binaryMask);
    }
    
    /**
     * فحص القائمة السوداء
     */
    private function isBlacklisted(string $ip): bool
    {
        return Cache::has("blacklist:$ip");
    }
    
    /**
     * فحص معدل الطلبات
     */
    private function isRateLimited(string $ip): bool
    {
        $key = "rate_limit:$ip";
        $attempts = Cache::get($key, 0);
        $maxAttempts = config('app.max_requests_per_minute', 60);
        
        if ($attempts >= $maxAttempts) {
            return true;
        }
        
        Cache::put($key, $attempts + 1, 60); // 60 ثانية
        return false;
    }
    
    /**
     * فحوصات أمان إضافية
     */
    private function additionalSecurityChecks(Request $request): bool
    {
        // فحص User Agent
        $userAgent = $request->userAgent();
        if (empty($userAgent) || $this->isSuspiciousUserAgent($userAgent)) {
            return false;
        }
        
        // فحص الـ Referrer
        if ($this->isSuspiciousReferrer($request)) {
            return false;
        }
        
        // فحص Headers مشبوهة
        if ($this->hasSuspiciousHeaders($request)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * فحص User Agent مشبوه
     */
    private function isSuspiciousUserAgent(string $userAgent): bool
    {
        $suspiciousPatterns = [
            'bot', 'crawler', 'spider', 'scraper',
            'curl', 'wget', 'python', 'java',
            'scanner', 'exploit', 'hack'
        ];
        
        $userAgent = strtolower($userAgent);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * فحص Referrer مشبوه
     */
    private function isSuspiciousReferrer(Request $request): bool
    {
        $referrer = $request->header('referer');
        
        if (empty($referrer)) {
            return false; // قد يكون طبيعياً
        }
        
        // فحص نطاقات مشبوهة
        $suspiciousDomains = [
            'malicious-site.com',
            'spam-domain.net',
            // أضف المزيد حسب الحاجة
        ];
        
        foreach ($suspiciousDomains as $domain) {
            if (strpos($referrer, $domain) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * فحص Headers مشبوهة
     */
    private function hasSuspiciousHeaders(Request $request): bool
    {
        $suspiciousHeaders = [
            'X-Forwarded-For' => ['unknown', 'localhost'],
            'X-Real-IP' => ['0.0.0.0', '127.0.0.1'],
        ];
        
        foreach ($suspiciousHeaders as $header => $suspiciousValues) {
            $value = $request->header($header);
            if ($value && in_array(strtolower($value), $suspiciousValues)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * رفض الوصول وتسجيل المحاولة
     */
    private function denyAccess(Request $request, string $ip, string $reason): Response
    {
        // تسجيل المحاولة المرفوضة
        Log::warning('محاولة وصول مرفوضة', [
            'ip' => $ip,
            'reason' => $reason,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'timestamp' => now()->toDateTimeString()
        ]);
        
        // إضافة إلى القائمة السوداء المؤقتة
        $this->addToTemporaryBlacklist($ip);
        
        // إرسال تنبيه للإدارة
        $this->sendSecurityAlert($ip, $reason, $request);
        
        return response()->view('errors.403', [
            'message' => 'غير مصرح لك بالوصول إلى هذا النظام من موقعك الحالي.'
        ], 403);
    }
    
    /**
     * إضافة IP إلى القائمة السوداء المؤقتة
     */
    private function addToTemporaryBlacklist(string $ip): void
    {
        $attempts = Cache::get("failed_attempts:$ip", 0) + 1;
        Cache::put("failed_attempts:$ip", $attempts, 3600); // ساعة واحدة
        
        // إذا تجاوز 5 محاولات، أضفه للقائمة السوداء لمدة 24 ساعة
        if ($attempts >= 5) {
            Cache::put("blacklist:$ip", true, 86400); // 24 ساعة
        }
    }
    
    /**
     * إرسال تنبيه أمني
     */
    private function sendSecurityAlert(string $ip, string $reason, Request $request): void
    {
        // يمكن إرسال إيميل أو إشعار للإدارة
        // أو حفظ في قاعدة البيانات للمراجعة
        
        $alertData = [
            'type' => 'security_breach_attempt',
            'ip' => $ip,
            'reason' => $reason,
            'url' => $request->fullUrl(),
            'timestamp' => now(),
            'severity' => 'high'
        ];
        
        // حفظ في ملف منفصل للتنبيهات
        Log::channel('security')->critical('تنبيه أمني', $alertData);
    }
    
    /**
     * تسجيل محاولة الوصول
     */
    private function logAccessAttempt(Request $request, string $ip): void
    {
        Log::info('محاولة وصول', [
            'ip' => $ip,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }
    
    /**
     * تسجيل الوصول الناجح
     */
    private function logSuccessfulAccess(Request $request, string $ip): void
    {
        Log::info('وصول ناجح', [
            'ip' => $ip,
            'url' => $request->fullUrl(),
            'timestamp' => now()->toDateTimeString()
        ]);
        
        // إزالة من عداد المحاولات الفاشلة
        Cache::forget("failed_attempts:$ip");
    }
}
```

### 3. تسجيل الـ Middleware في النظام

#### في ملف `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // تسجيل الـ middleware
        $middleware->alias([
            'network.security' => \App\Http\Middleware\AdvancedNetworkSecurityMiddleware::class,
        ]);
        
        // تطبيق على جميع المسارات
        $middleware->web(append: [
            \App\Http\Middleware\AdvancedNetworkSecurityMiddleware::class,
        ]);
        
        // تطبيق على API أيضاً
        $middleware->api(append: [
            \App\Http\Middleware\AdvancedNetworkSecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 4. إعدادات البيئة المتقدمة

#### في ملف `.env`:

```env
# إعدادات الشبكة والأمان
APP_ENV=production
APP_DEBUG=false

# الشبكات المسموحة (مفصولة بفاصلة)
ALLOWED_NETWORKS="192.168.1.0/24,192.168.2.0/24,10.0.0.0/8"

# إعدادات الأمان
MAX_REQUESTS_PER_MINUTE=60
SECURITY_LOG_LEVEL=info
ENABLE_IP_BLACKLIST=true
BLACKLIST_DURATION=86400

# إعدادات الخادم
SERVER_HOST=0.0.0.0
SERVER_PORT=8000

# إعدادات SSL (إذا كان متوفراً)
FORCE_HTTPS=true
SSL_CERT_PATH=/path/to/certificate.crt
SSL_KEY_PATH=/path/to/private.key

# إعدادات قاعدة البيانات الآمنة
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=training_center_db
DB_USERNAME=secure_user
DB_PASSWORD=very_strong_password_123!@#

# إعدادات الجلسات الآمنة
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# إعدادات الكوكيز الآمنة
COOKIE_SECURE=true
COOKIE_HTTP_ONLY=true
COOKIE_SAME_SITE=strict
```

#### في ملف `config/app.php`:

```php
// إضافة هذه الإعدادات
'allowed_networks' => array_filter(explode(',', env('ALLOWED_NETWORKS', ''))),
'max_requests_per_minute' => env('MAX_REQUESTS_PER_MINUTE', 60),
'enable_ip_blacklist' => env('ENABLE_IP_BLACKLIST', true),
'blacklist_duration' => env('BLACKLIST_DURATION', 86400),
'force_https' => env('FORCE_HTTPS', false),
```

---

## 🖥️ إعدادات الخادم والشبكة {#server-network-config}

### إعدادات Apache (إذا كنت تستخدم Apache)

#### ملف `.htaccess` في المجلد العام:

```apache
# حماية متقدمة للخادم
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # منع الوصول من IPs محددة (قائمة سوداء)
    # RewriteCond %{REMOTE_ADDR} ^123\.456\.789\.
    # RewriteRule ^(.*)$ - [F,L]
    
    # السماح فقط للشبكات المحلية
    RewriteCond %{REMOTE_ADDR} !^192\.168\.1\.
    RewriteCond %{REMOTE_ADDR} !^192\.168\.2\.
    RewriteCond %{REMOTE_ADDR} !^10\.
    RewriteCond %{REMOTE_ADDR} !^127\.0\.0\.1$
    RewriteCond %{REMOTE_ADDR} !^::1$
    RewriteRule ^(.*)$ - [F,L]
    
    # إعادة توجيه للـ Laravel
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# حماية الملفات الحساسة
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

# منع عرض محتويات المجلدات
Options -Indexes

# حماية من XSS
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set Content-Security-Policy "default-src 'self'"
</IfModule>

# ضغط الملفات لتحسين الأداء
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

### إعدادات Nginx (إذا كنت تستخدم Nginx)

#### ملف التكوين `/etc/nginx/sites-available/training-center`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name training-center.local;
    
    # إعادة توجيه إلى HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name training-center.local;
    
    root /path/to/your/laravel/public;
    index index.php index.html index.htm;
    
    # إعدادات SSL
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
    
    # تقييد الوصول للشبكات المحلية فقط
    allow 192.168.1.0/24;
    allow 192.168.2.0/24;
    allow 10.0.0.0/8;
    allow 127.0.0.1;
    allow ::1;
    deny all;
    
    # حماية الأمان
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # تحديد معدل الطلبات
    limit_req_zone $binary_remote_addr zone=login:10m rate=10r/m;
    limit_req zone=login burst=5 nodelay;
    
    # إخفاء معلومات الخادم
    server_tokens off;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # حماية إضافية لـ PHP
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # منع الوصول للملفات الحساسة
    location ~ /\.(env|git|svn) {
        deny all;
        return 404;
    }
    
    location ~ /(composer\.(json|lock)|package\.json|yarn\.lock) {
        deny all;
        return 404;
    }
}
```

---

## 🔥 إعدادات جدار الحماية {#firewall-configuration}

### جدار الحماية في Windows Server

#### PowerShell Commands:

```powershell
# إنشاء قاعدة جديدة للسماح بالوصول للشبكة المحلية فقط
New-NetFirewallRule -DisplayName "Training Center Web Access" `
    -Direction Inbound `
    -Protocol TCP `
    -LocalPort 80,443 `
    -RemoteAddress 192.168.1.0/24,192.168.2.0/24,10.0.0.0/8 `
    -Action Allow

# منع جميع الاتصالات الأخرى على المنافذ 80 و 443
New-NetFirewallRule -DisplayName "Block External Web Access" `
    -Direction Inbound `
    -Protocol TCP `
    -LocalPort 80,443 `
    -Action Block

# السماح بـ SSH للإدارة (منفذ 22)
New-NetFirewallRule -DisplayName "SSH Admin Access" `
    -Direction Inbound `
    -Protocol TCP `
    -LocalPort 22 `
    -RemoteAddress 192.168.1.100 `
    -Action Allow

# عرض القواعد المطبقة
Get-NetFirewallRule | Where-Object {$_.DisplayName -like "*Training Center*"}
```

### جدار الحماية في Linux (UFW)

```bash
# تفعيل جدار الحماية
sudo ufw enable

# السماح بـ SSH للإدارة
sudo ufw allow from 192.168.1.100 to any port 22

# السماح بالوصول للويب من الشبكة المحلية فقط
sudo ufw allow from 192.168.1.0/24 to any port 80
sudo ufw allow from 192.168.1.0/24 to any port 443
sudo ufw allow from 192.168.2.0/24 to any port 80
sudo ufw allow from 192.168.2.0/24 to any port 443
sudo ufw allow from 10.0.0.0/8 to any port 80
sudo ufw allow from 10.0.0.0/8 to any port 443

# منع جميع الاتصالات الأخرى
sudo ufw deny 80
sudo ufw deny 443

# عرض حالة جدار الحماية
sudo ufw status verbose
```

### إعدادات الراوتر

#### تكوين الراوتر لحماية إضافية:

1. **تعطيل WPS**
2. **تغيير كلمة مرور الإدارة**
3. **تفعيل MAC Address Filtering**
4. **إعداد Guest Network منفصلة**
5. **تعطيل Remote Management**

```bash
# مثال على إعدادات الراوتر (يختلف حسب النوع)
# تسجيل الدخول للراوتر عادة عبر:
# http://192.168.1.1 أو http://192.168.0.1

# إعدادات الأمان المطلوبة:
# - WPA3 أو WPA2 للواي فاي
# - إخفاء SSID
# - تحديد عدد الأجهزة المتصلة
# - جدولة الوصول (إذا كان متوفراً)
```

---

## 📊 مراقبة ومتابعة الأمان {#monitoring-security}

### 1. إعداد نظام المراقبة

#### إنشاء ملف تكوين للتسجيل `config/logging.php`:

```php
'channels' => [
    // القنوات الموجودة...
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 30,
        'replace_placeholders' => true,
    ],
    
    'access' => [
        'driver' => 'daily',
        'path' => storage_path('logs/access.log'),
        'level' => 'info',
        'days' => 90,
        'replace_placeholders' => true,
    ],
    
    'failed_attempts' => [
        'driver' => 'daily',
        'path' => storage_path('logs/failed_attempts.log'),
        'level' => 'warning',
        'days' => 60,
        'replace_placeholders' => true,
    ],
],
```

### 2. إنشاء نظام التنبيهات

#### Command للمراقبة `app/Console/Commands/SecurityMonitor.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SecurityMonitor extends Command
{
    protected $signature = 'security:monitor';
    protected $description = 'مراقبة الأمان وإرسال التنبيهات';

    public function handle()
    {
        $this->info('بدء مراقبة الأمان...');
        
        // فحص محاولات الوصول المشبوهة
        $this->checkSuspiciousActivity();
        
        // فحص القائمة السوداء
        $this->checkBlacklistedIPs();
        
        // فحص استخدام الموارد
        $this->checkResourceUsage();
        
        // إنشاء تقرير يومي
        $this->generateDailyReport();
        
        $this->info('انتهت مراقبة الأمان.');
    }
    
    private function checkSuspiciousActivity()
    {
        $logFile = storage_path('logs/security.log');
        
        if (!file_exists($logFile)) {
            return;
        }
        
        $recentLogs = $this->getRecentLogs($logFile, 60); // آخر 60 دقيقة
        $suspiciousCount = 0;
        
        foreach ($recentLogs as $log) {
            if (strpos($log, 'محاولة وصول مرفوضة') !== false) {
                $suspiciousCount++;
            }
        }
        
        if ($suspiciousCount > 10) {
            $this->sendAlert("تم رصد {$suspiciousCount} محاولة وصول مشبوهة في آخر ساعة");
        }
    }
    
    private function checkBlacklistedIPs()
    {
        $blacklistedIPs = [];
        $cacheKeys = Cache::getRedis()->keys('blacklist:*');
        
        foreach ($cacheKeys as $key) {
            $ip = str_replace('blacklist:', '', $key);
            $blacklistedIPs[] = $ip;
        }
        
        if (count($blacklistedIPs) > 0) {
            Log::info("IPs في القائمة السوداء: " . implode(', ', $blacklistedIPs));
        }
    }
    
    private function checkResourceUsage()
    {
        $cpuUsage = sys_getloadavg()[0];
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        
        if ($cpuUsage > 80) {
            $this->sendAlert("استخدام CPU مرتفع: {$cpuUsage}%");
        }
        
        if ($memoryUsage > 512) {
            $this->sendAlert("استخدام الذاكرة مرتفع: {$memoryUsage} MB");
        }
    }
    
    private function generateDailyReport()
    {
        $today = now()->format('Y-m-d');
        $logFile = storage_path("logs/access-{$today}.log");
        
        if (!file_exists($logFile)) {
            return;
        }
        
        $totalRequests = 0;
        $uniqueIPs = [];
        $failedAttempts = 0;
        
        $handle = fopen($logFile, 'r');
        while (($line = fgets($handle)) !== false) {
            $totalRequests++;
            
            if (preg_match('/ip":"([^"]+)"/', $line, $matches)) {
                $uniqueIPs[$matches[1]] = true;
            }
            
            if (strpos($line, 'محاولة وصول مرفوضة') !== false) {
                $failedAttempts++;
            }
        }
        fclose($handle);
        
        $report = [
            'date' => $today,
            'total_requests' => $totalRequests,
            'unique_ips' => count($uniqueIPs),
            'failed_attempts' => $failedAttempts,
            'success_rate' => round((($totalRequests - $failedAttempts) / $totalRequests) * 100, 2)
        ];
        
        Log::info('تقرير يومي', $report);
    }
    
    private function getRecentLogs($file, $minutes)
    {
        $cutoff = now()->subMinutes($minutes);
        $logs = [];
        
        $handle = fopen($file, 'r');
        while (($line = fgets($handle)) !== false) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                $logTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $matches[1]);
                if ($logTime->gte($cutoff)) {
                    $logs[] = $line;
                }
            }
        }
        fclose($handle);
        
        return $logs;
    }
    
    private function sendAlert($message)
    {
        Log::critical("تنبيه أمني: {$message}");
        
        // يمكن إرسال إيميل أو SMS هنا
        // Mail::to('admin@training-center.com')->send(new SecurityAlert($message));
    }
}
```

### 3. جدولة المراقبة

#### في ملف `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // مراقبة كل 5 دقائق
    $schedule->command('security:monitor')
             ->everyFiveMinutes()
             ->withoutOverlapping();
    
    // تنظيف السجلات القديمة يومياً
    $schedule->command('log:clear')
             ->daily()
             ->at('02:00');
    
    // نسخ احتياطي للسجلات أسبوعياً
    $schedule->command('backup:logs')
             ->weekly()
             ->sundays()
             ->at('03:00');
}
```

---

## 🚨 خطة الطوارئ والنسخ الاحتياطي {#emergency-backup}

### 1. خطة الاستجابة للطوارئ

#### سيناريوهات الطوارئ:

1. **هجوم DDoS**
   - تفعيل حماية إضافية
   - حجب IPs المهاجمة
   - تقليل معدل الطلبات المسموح

2. **محاولة اختراق**
   - تسجيل جميع التفاصيل
   - إشعار الإدارة فوراً
   - تعطيل الحسابات المشبوهة

3. **عطل في الخادم**
   - التبديل للخادم الاحتياطي
   - استعادة النسخة الاحتياطية
   - إشعار المستخدمين

### 2. نظام النسخ الاحتياطي

#### Command للنسخ الاحتياطي `app/Console/Commands/BackupSystem.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupSystem extends Command
{
    protected $signature = 'backup:create {--type=full}';
    protected $description = 'إنشاء نسخة احتياطية من النظام';

    public function handle()
    {
        $type = $this->option('type');
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        $this->info("بدء إنشاء النسخة الاحتياطية ({$type})...");
        
        switch ($type) {
            case 'database':
                $this->backupDatabase($timestamp);
                break;
            case 'files':
                $this->backupFiles($timestamp);
                break;
            case 'logs':
                $this->backupLogs($timestamp);
                break;
            case 'full':
            default:
                $this->backupDatabase($timestamp);
                $this->backupFiles($timestamp);
                $this->backupLogs($timestamp);
                break;
        }
        
        $this->info('تم إنشاء النسخة الاحتياطية بنجاح.');
    }
    
    private function backupDatabase($timestamp)
    {
        $filename = "database_backup_{$timestamp}.sql";
        $path = storage_path("backups/{$filename}");
        
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $path
        );
        
        exec($command);
        $this->info("تم حفظ النسخة الاحتياطية لقاعدة البيانات: {$filename}");
    }
    
    private function backupFiles($timestamp)
    {
        $filename = "files_backup_{$timestamp}.tar.gz";
        $path = storage_path("backups/{$filename}");
        
        $command = sprintf(
            'tar -czf %s %s --exclude=%s/backups',
            $path,
            base_path(),
            storage_path()
        );
        
        exec($command);
        $this->info("تم حفظ النسخة الاحتياطية للملفات: {$filename}");
    }
    
    private function backupLogs($timestamp)
    {
        $filename = "logs_backup_{$timestamp}.tar.gz";
        $path = storage_path("backups/{$filename}");
        
        $command = sprintf(
            'tar -czf %s %s',
            $path,
            storage_path('logs')
        );
        
        exec($command);
        $this->info("تم حفظ النسخة الاحتياطية للسجلات: {$filename}");
    }
}
```

---

## 🧪 اختبار شامل للنظام {#comprehensive-testing}

### 1. اختبارات الأمان

#### ملف الاختبار `tests/Feature/SecurityTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    public function test_blocks_external_ip_access()
    {
        // محاكاة IP خارجي
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '8.8.8.8'
        ])->get('/');
        
        $response->assertStatus(403);
    }
    
    public function test_allows_local_network_access()
    {
        // محاكاة IP محلي
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '192.168.1.100'
        ])->get('/');
        
        $response->assertStatus(200);
    }
    
    public function test_rate_limiting_works()
    {
        $ip = '192.168.1.100';
        
        // إرسال طلبات متعددة
        for ($i = 0; $i < 70; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])->get('/');
        }
        
        // الطلب التالي يجب أن يُرفض
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->get('/');
        $response->assertStatus(403);
    }
    
    public function test_blacklist_functionality()
    {
        $ip = '192.168.1.100';
        
        // إضافة IP للقائمة السوداء
        \Cache::put("blacklist:{$ip}", true, 3600);
        
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->get('/');
        $response->assertStatus(403);
    }
}
```

### 2. اختبارات الأداء

```bash
# اختبار الحمولة باستخدام Apache Bench
ab -n 1000 -c 10 http://192.168.1.100:8000/

# اختبار من خارج الشبكة (يجب أن يفشل)
ab -n 100 -c 5 http://external-ip:8000/
```

### 3. اختبارات الاختراق

```bash
# فحص المنافذ المفتوحة
nmap -sS 192.168.1.100

# فحص نقاط الضعف
nikto -h http://192.168.1.100:8000

# اختبار SQL Injection (يجب أن يفشل)
sqlmap -u "http://192.168.1.100:8000/login" --forms
```

---

## 📋 قائمة التحقق النهائية

### ✅ قبل التطبيق:

- [ ] تحديد نطاقات IP للشبكة المحلية
- [ ] إعداد النسخ الاحتياطية
- [ ] اختبار النظام في بيئة التطوير
- [ ] تدريب فريق الإدارة
- [ ] إعداد خطة الطوارئ

### ✅ أثناء التطبيق:

- [ ] تطبيق Middleware
- [ ] تكوين إعدادات الخادم
- [ ] إعداد جدار الحماية
- [ ] تفعيل نظام المراقبة
- [ ] اختبار الوصول من داخل وخارج الشبكة

### ✅ بعد التطبيق:

- [ ] مراقبة السجلات يومياً
- [ ] مراجعة التنبيهات الأمنية
- [ ] تحديث كلمات المرور دورياً
- [ ] مراجعة وتحديث قواعد الأمان
- [ ] إجراء نسخ احتياطية منتظمة

---

## 🔧 أوامر مفيدة للإدارة

```bash
# تشغيل الخادم للشبكة المحلية
php artisan serve --host=0.0.0.0 --port=8000

# مراقبة السجلات في الوقت الفعلي
tail -f storage/logs/security.log

# فحص الاتصالات النشطة
netstat -an | grep :8000

# عرض IPs المتصلة حالياً
ss -tuln | grep :8000

# تنظيف الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# إنشاء نسخة احتياطية
php artisan backup:create --type=full

# مراقبة الأمان
php artisan security:monitor

# اختبار الأمان
php artisan test --filter=SecurityTest
```

---

## ⚠️ تحذيرات مهمة

1. **لا تعطل الحماية أبداً** حتى لو كانت تسبب مشاكل مؤقتة
2. **احتفظ بنسخ احتياطية** من جميع الإعدادات قبل التغيير
3. **اختبر دائماً** في بيئة التطوير قبل التطبيق
4. **راقب السجلات** بانتظام للتأكد من عدم وجود مشاكل
5. **حدث كلمات المرور** دورياً لجميع الحسابات
6. **درب الفريق** على إجراءات الأمان والطوارئ

---

هذا الدليل يوفر حماية شاملة ومتعددة الطبقات لضمان أن موقع مركز الدروس آمن ومتاح فقط للمستخدمين المصرح لهم داخل الشبكة المحلية.