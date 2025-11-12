# 📝 شرح تشغيل SQL Query لإعادة تعيين كلمات السر

---

## 🔐 معلومات مهمة:

```
كلمة المرور: 12345678
الـ Hash (bcrypt): $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm
```

---

## 📌 الطرق المختلفة:

### الطريقة 1: تحديث **جميع المستخدمين**

```sql
UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
WHERE 1=1;
```

---

### الطريقة 2: تحديث **الطلاب فقط**

```sql
UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
WHERE type = 'student';
```

---

### الطريقة 3: تحديث **المعلمين فقط**

```sql
UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
WHERE type = 'teacher';
```

---

### الطريقة 4: تحديث **الإداريين فقط**

```sql
UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
WHERE type = 'admin';
```

---

## 🚀 كيفية التشغيل:

### 1️⃣ عبر phpMyAdmin:

1. افتح phpMyAdmin
2. اختر قاعدة البيانات
3. اذهب إلى Tab "SQL"
4. انسخ الـ query المناسب
5. اضغط "Go" أو "Execute"

### 2️⃣ عبر MySQL Command Line:

```bash
mysql -u username -p database_name < reset_passwords.sql
```

أو مباشرة:

```bash
mysql -u username -p database_name -e "UPDATE users SET password = '\$2y\$10\$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm' WHERE type = 'student';"
```

### 3️⃣ عبر Laravel Tinker:

```bash
php artisan tinker
```

ثم:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// تحديث جميع المستخدمين
User::query()->update(['password' => '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm']);

// أو الطلاب فقط
User::where('type', 'student')->update(['password' => '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm']);
```

---

## ✅ بعد التحديث:

جميع المستخدمين يمكنهم الدخول بـ:
```
كلمة المرور: 12345678
```

---

## ⚠️ تنبيهات مهمة:

1. **تأكد من Backup:** قبل تشغيل الـ query، احفظ نسخة من البيانات
2. **لا تنسَ تغيير الكلمات:** بعد الاختبار، غيّر كلمات السر للأصلية
3. **أمان:** لا تترك كلمة السر نفسها لجميع المستخدمين في الإنتاج

---

## 🔍 التحقق:

بعد التحديث، شغّل هذا الـ query للتحقق:

```sql
SELECT COUNT(*) as total_users, 
       COUNT(CASE WHEN password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm' THEN 1 END) as updated_count
FROM users;
```

يجب أن يكون `updated_count = total_users`

---

## 💡 بديل: إذا أردت كلمة مرور مختلفة:

استخدم موقع مثل: https://bcrypt-generator.com

1. ادخل كلمة المرور التي تريد
2. اضغط "Hash"
3. انسخ الـ Hash
4. ضعها في الـ query

---

**الملف `reset_passwords.sql` موجود في المجلد الرئيسي للتطبيق** 📁
