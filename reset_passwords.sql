-- ملف SQL لإعادة تعيين كل كلمات السر إلى 12345678

-- كلمة المرور: 12345678
-- الـ Hash (bcrypt): $2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm

-- الطريقة 1: تحديث جميع المستخدمين
UPDATE users 
SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
WHERE 1=1;

-- الطريقة 2: تحديث الطلاب فقط
-- UPDATE users 
-- SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
-- WHERE type = 'student';

-- الطريقة 3: تحديث المعلمين فقط
-- UPDATE users 
-- SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
-- WHERE type = 'teacher';

-- الطريقة 4: تحديث الإداريين فقط
-- UPDATE users 
-- SET password = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36ajxjQm'
-- WHERE type = 'admin';

-- التحقق: عرض عدد المستخدمين المحدثين
-- SELECT COUNT(*) as updated_count FROM users;
