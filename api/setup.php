<?php
/**
 * شغّل الملف ده مرة واحدة فقط لإنشاء الجداول وإدخال البيانات
 * لإعادة التهيئة بالكامل: افتح setup.php?reset=1
 * ⚠️ امسح الملف من السيرفر بعد الانتهاء
 */

require_once 'config.php';

$force = isset($_GET['reset']) && $_GET['reset'] == '1';

if ($force) {
    $pdo->exec("DROP TABLE IF EXISTS settings");
    $pdo->exec("DROP TABLE IF EXISTS services");
    $pdo->exec("DROP TABLE IF EXISTS gallery");
    $pdo->exec("DROP TABLE IF EXISTS testimonials");
    $pdo->exec("DROP TABLE IF EXISTS stats");
    $pdo->exec("DROP TABLE IF EXISTS messages");
}

$results = [];

/* ========== 1) إنشاء الجداول ========== */
$tables = [
    "CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        site_name VARCHAR(255) DEFAULT 'الابتكار',
        tagline VARCHAR(255) DEFAULT 'لأعمال النجارة والديكور الحديث',
        phone VARCHAR(50) DEFAULT '0540794678',
        whatsapp VARCHAR(50) DEFAULT '966540794678',
        email VARCHAR(255) DEFAULT 'info@alibtikar.com',
        address TEXT,
        hours VARCHAR(255) DEFAULT 'متاحون لخدمتكم 24 ساعة'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS services (
        id INT PRIMARY KEY AUTO_INCREMENT,
        icon VARCHAR(100) DEFAULT 'fas fa-tools',
        title VARCHAR(255) NOT NULL,
        description TEXT,
        sort_order INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS gallery (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        label VARCHAR(255) DEFAULT '',
        type VARCHAR(20) DEFAULT 'image',
        src TEXT NOT NULL,
        size VARCHAR(20) DEFAULT 'normal',
        sort_order INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS testimonials (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        text TEXT NOT NULL,
        stars INT DEFAULT 5,
        sort_order INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS stats (
        id INT PRIMARY KEY AUTO_INCREMENT,
        label VARCHAR(255) NOT NULL,
        value INT DEFAULT 0,
        sort_order INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        text TEXT NOT NULL,
        date DATE NOT NULL,
        is_read TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

foreach ($tables as $q) {
    try { $pdo->exec($q); $results[] = "✅ جدول تم إنشاؤه"; }
    catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }
}

/* ========== 2) الإعدادات ========== */
try {
    if ($pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO settings 
            (site_name, tagline, phone, whatsapp, email, address, hours) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'الابتكار',
            'لأعمال النجارة والديكور الحديث',
            '0540794678',
            '966540794678',
            'info@alibtikar.com',
            'الرياض - المملكة العربية السعودية',
            'متاحون لخدمتكم 24 ساعة'
        ]);
        $results[] = "✅ تم إدخال الإعدادات";
    } else {
        $results[] = "ℹ️ الإعدادات موجودة مسبقاً";
    }
} catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }

/* ========== 3) الخدمات (9 خدمات من الصفحة) ========== */
try {
    if ($pdo->query("SELECT COUNT(*) FROM services")->fetchColumn() == 0) {
        $services = [
            ['fas fa-bed',            'صيانة غرف نوم',           'إصلاح شامل لغرف النوم، تبديل المفصلات التالفة، ومعالجة ترهل الدواليب.'],
            ['fas fa-columns',        'تفصيل دواليب وخزائن',     'تصميم وتفصيل دواليب الملابس وخزائن الحائط باستغلال ذكي للمساحات.'],
            ['fas fa-door-closed',    'تفصيل أبواب سحاب',        'صناعة أبواب خشبية سحاب (Sliding) مودرن، مثالية لتوفير المساحة والأناقة.'],
            ['fas fa-door-open',      'تفصيل أبواب خشب',         'تصنيع أبواب المداخل والغرف من أجود أخشاب الزان والسويدي الطبيعي.'],
            ['fas fa-tools',          'صيانة أبواب خشب',          'معالجة هبوط الأبواب، صنفرة وتجديد الدهان، وتركيب المقابض الحديثة.'],
            ['fas fa-utensils',       'صيانة مطابخ',              'تجديد دواليب المطبخ، تغيير الرخام، وإصلاح الأدراج والمفصلات الصدئة.'],
            ['fas fa-couch',          'تركيب ايكيا',              'فنيون محترفون في تركيب كافة قطع أثاث ايكيا وجميع الماركات العالمية.'],
            ['fas fa-window-maximize','تركيب ستائر وبراويز',       'تركيب جميع أنواع الستائر واللوحات الجدارية بدقة متناهية وتوازن تام.'],
            ['fas fa-key',            'تركيب أقفال وكوالين',      'تغيير وتركيب الأقفال (الكوالين) للأبواب الخشبية لضمان أعلى مستويات الأمان.'],
        ];
        $stmt = $pdo->prepare("INSERT INTO services (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($services as $i => $s) $stmt->execute([$s[0], $s[1], $s[2], $i]);
        $results[] = "✅ تم إدخال " . count($services) . " خدمة";
    } else {
        $results[] = "ℹ️ الخدمات موجودة مسبقاً";
    }
} catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }

/* ========== 4) المعرض (13 عنصر من الصفحة) ========== */
try {
    if ($pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn() == 0) {
        $gallery = [
            ['دقة التنفيذ في ورشتنا',              'فيديو العمل',      'video', 'images/work-process.mp4',      'wide'],
            ['تفصيل دواليب مبتكرة',                  '',                 'image', 'images/img1.jpeg',             'tall'],
            ['تركيب غرف نوم ايكيا',                  '',                 'image', 'images/img10.jpeg',            'normal'],
            ['لمساتنا الأخيرة',                      'لمساتنا الأخيرة',  'video', 'images/finishing.mp4',         'normal'],
            ['صيانة وترميم الأثاث',                  '',                 'image', 'images/img3.jpeg',             'normal'],
            ['تركيب باب سحاب خشب فاخر',              'فيديو التنفيذ',    'video', 'images/sliding-door-work.mp4', 'tall'],
            ['مطابخ عصرية بنظام استغلال المساحات',   'تصميم مطابخ',      'image', 'images/img11.jpeg',            'wide'],
            ['أبواب خشب سويدي وزان فاخر',            'أبواب داخلية',     'image', 'images/img12.jpeg',            'tall'],
            ['تسريحات مودرن بإضاءة LED',             'ركن الأناقة',      'image', 'images/img13.jpeg',            'normal'],
            ['غرف أطفال مبهجة وآمنة',                'عالم الأطفال',     'image', 'images/img14.jpeg',            'normal'],
            ['خزائن حائط بتصميمات إيطالية',          'دواليب ملابس',     'image', 'images/img15.jpeg',            'tall'],
            ['دهانات حرارية ومقاومة للرطوبة',        'جودة التشطيب',     'image', 'images/img17.jpeg',            'wide'],
            ['غرف نوم رئيسية ملكية',                  '',                 'image', 'images/img16.jpeg',            'normal'],
        ];
        $stmt = $pdo->prepare("INSERT INTO gallery (title, label, type, src, size, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($gallery as $i => $g) $stmt->execute([$g[0], $g[1], $g[2], $g[3], $g[4], $i]);
        $results[] = "✅ تم إدخال " . count($gallery) . " عنصر في المعرض";
    } else {
        $results[] = "ℹ️ المعرض موجود مسبقاً";
    }
} catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }

/* ========== 5) آراء العملاء (2 من الصفحة) ========== */
try {
    if ($pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn() == 0) {
        $testi = [
            ['أبو فهد (الرياض)', 'بصراحة نجارين محترفين جداً، ركبوا لي غرفة النوم في وقت قياسي وبدقة متناهية. أنصح بهم بشدة.', 5],
            ['م. عبدالله',        'فصلت عندهم دولاب ملابس، التصميم كان عبقري واستغلوا المساحة بشكل ممتاز والشغل نظيف.',        5],
        ];
        $stmt = $pdo->prepare("INSERT INTO testimonials (name, text, stars, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($testi as $i => $t) $stmt->execute([$t[0], $t[1], $t[2], $i]);
        $results[] = "✅ تم إدخال " . count($testi) . " رأي";
    } else {
        $results[] = "ℹ️ الآراء موجودة مسبقاً";
    }
} catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }

/* ========== 6) الإحصاءات (3 من الصفحة) ========== */
try {
    if ($pdo->query("SELECT COUNT(*) FROM stats")->fetchColumn() == 0) {
        $stats = [
            ['سنة خبرة',    15],
            ['مشروع منجز', 1200],
            ['عميل سعيد',   950],
        ];
        $stmt = $pdo->prepare("INSERT INTO stats (label, value, sort_order) VALUES (?, ?, ?)");
        foreach ($stats as $i => $s) $stmt->execute([$s[0], $s[1], $i]);
        $results[] = "✅ تم إدخال " . count($stats) . " إحصاءة";
    } else {
        $results[] = "ℹ️ الإحصاءات موجودة مسبقاً";
    }
} catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }

/* ========== 7) رسائل تجريبية ========== */
try {
    if ($pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn() == 0) {
        $msgs = [
            ['أحمد الشمري',   '0551234567', 'أرغب في تفصيل دولاب ملابس بغرفة رئيسية، كم التكلفة التقريبية؟',  '2024-06-12', 0],
            ['سارة العتيبي',  '0509876543', 'هل توفرون خدمة تركيب غرف نوم ايكيا يوم الجمعة؟',              '2024-06-13', 1],
        ];
        $stmt = $pdo->prepare("INSERT INTO messages (name, phone, text, date, is_read) VALUES (?, ?, ?, ?, ?)");
        foreach ($msgs as $m) $stmt->execute($m);
        $results[] = "✅ تم إدخال " . count($msgs) . " رسالة تجريبية";
    } else {
        $results[] = "ℹ️ الرسائل موجودة مسبقاً";
    }
} catch (PDOException $e) { $results[] = "❌ " . $e->getMessage(); }

/* ========== 8) مجلد الرفع ========== */
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
    $results[] = "✅ تم إنشاء مجلد uploads";
}

/* ========== الواجهة ========== */
echo "<!DOCTYPE html><html dir='rtl' lang='ar'><head><meta charset='utf-8'><title>تثبيت</title>";
echo "<style>body{font-family:'Cairo',sans-serif;background:#f5f7fb;padding:40px;direction:rtl;color:#1a2c4e}";
echo ".box{max-width:650px;margin:auto;background:#fff;padding:35px;border-radius:20px;box-shadow:0 20px 50px rgba(0,0,0,.08)}";
echo "h1{margin-bottom:25px;font-size:1.6rem}ul{list-style:none;padding:0}li{padding:12px 14px;border-bottom:1px solid #eef1f6;font-size:.95rem}";
echo "li:last-child{border:0}.done{margin-top:25px;padding:16px;background:#fff4ec;color:#d35400;border-radius:12px;font-weight:700;border:1px dashed #f0c9a8}";
echo ".btn{display:inline-block;padding:12px 24px;background:#d35400;color:#fff;text-decoration:none;border-radius:10px;font-weight:700;margin-top:15px}";
echo "</style></head><body><div class='box'>";
echo "<h1>⚙️ نتيجة التثبيت</h1><ul>";
foreach ($results as $r) echo "<li>$r</li>";
echo "</ul><div class='done'>⚠️ امسح ملف setup.php الآن من السيرفر للأمان.</div>";
echo "<a class='btn' href='../admin.html'>الانتقال إلى لوحة التحكم ←</a>";
echo "</div></body></html>";