<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictNetworkAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // الحصول على عنوان IP الخاص بالمستخدم
        $userIP = $request->ip();
        
        // الشبكات المسموحة (يمكنك تعديلها حسب شبكتك)
        $allowedNetworks = [
            '192.168.1.0/24',    // شبكة محلية نموذجية
            '192.168.0.0/24',    // شبكة محلية أخرى
            '10.0.0.0/24',       // شبكة محلية
            '127.0.0.1',         // localhost للتطوير
        ];
        
        // فحص إذا كان IP المستخدم ضمن الشبكات المسموحة
        if ($this->isIPAllowed($userIP, $allowedNetworks)) {
            return $next($request);
        }
        
        // إذا لم يكن مسموحاً، إرجاع رسالة خطأ
        abort(403, 'الوصول مقيد على الشبكة المحلية فقط');
    }
    
    /**
     * فحص إذا كان IP مسموحاً
     */
    private function isIPAllowed($ip, $allowedNetworks)
    {
        foreach ($allowedNetworks as $network) {
            if ($this->ipInRange($ip, $network)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * فحص إذا كان IP ضمن نطاق الشبكة
     */
    private function ipInRange($ip, $range)
    {
        // إذا كان IP محدد (مثل localhost)
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }
        
        // تحليل نطاق الشبكة
        list($subnet, $mask) = explode('/', $range);
        
        // تحويل IP إلى رقم
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - $mask);
        
        // فحص إذا كان IP ضمن النطاق
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}