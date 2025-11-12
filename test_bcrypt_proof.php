<?php

/**
 * اختبار عملي: bcrypt hash مع نفس كلمة المرور
 * يوضح أن كل hash مختلف حتى لو كانت الكلمة نفسها
 */

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

echo "\n=== اختبار bcrypt مع نفس كلمة المرور ===\n\n";

$password = '12345678';

// توليد 3 hashes مختلفة لنفس الكلمة
echo "كلمة المرور: $password\n\n";

echo "Hash رقم 1:\n";
$hash1 = bcrypt($password);
echo $hash1 . "\n\n";

echo "Hash رقم 2:\n";
$hash2 = bcrypt($password);
echo $hash2 . "\n\n";

echo "Hash رقم 3:\n";
$hash3 = bcrypt($password);
echo $hash3 . "\n\n";

echo "=== المقارنة ===\n\n";

// هل الـ hashes مختلفة؟
echo "هل Hash1 == Hash2؟ " . ($hash1 === $hash2 ? 'نعم ❌' : 'لا ✅ مختلفة') . "\n";
echo "هل Hash1 == Hash3؟ " . ($hash1 === $hash3 ? 'نعم ❌' : 'لا ✅ مختلفة') . "\n";
echo "هل Hash2 == Hash3؟ " . ($hash2 === $hash3 ? 'نعم ❌' : 'لا ✅ مختلفة') . "\n\n";

// لكن كل واحد يتحقق من نفس الكلمة
echo "=== التحقق (Verification) ===\n\n";

echo "هل Hash1 يطابق الكلمة '12345678'؟ " . (Hash::check($password, $hash1) ? 'نعم ✅' : 'لا ❌') . "\n";
echo "هل Hash2 يطابق الكلمة '12345678'؟ " . (Hash::check($password, $hash2) ? 'نعم ✅' : 'لا ❌') . "\n";
echo "هل Hash3 يطابق الكلمة '12345678'؟ " . (Hash::check($password, $hash3) ? 'نعم ✅' : 'لا ❌') . "\n\n";

echo "الخلاصة:\n";
echo "✅ كل hash مختلف حتى لو كانت الكلمة نفسها\n";
echo "✅ لكن Hash::check() يعمل مع جميعهم\n";
echo "✅ هذا هو السر الأمني في bcrypt!\n\n";
?>
