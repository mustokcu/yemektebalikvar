<?php
/**
 * update-all-recipes-seo.php
 * Root dizine kaydet ve tarayıcıda çalıştır
 * https://yemektebalikvar.com/update-all-recipes-seo.php
 * 
 * ⚠️ UYARI: Bu script tüm tariflerin meta keywords'lerini güncelleyecek!
 * ⚠️ Çalıştırdıktan sonra dosyayı SİL!
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Güvenlik: Sadece admin çalıştırabilir
session_start();
$admin_password = '123456'; // Bu parolayı değiştir!

if(!isset($_GET['password']) || $_GET['password'] !== $admin_password) {
    die('❌ YETKISIZ ERIŞIM! Doğru parolayı gir: ?password=GÜVENLİK_PAROLA_2025');
}

set_time_limit(0); // Timeout kaldır
ini_set('memory_limit', '512M'); // Hafıza artır

echo "<!DOCTYPE html>
<html lang='tr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SEO Güncelleme Scripti</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
        }
        h1 {
            color: #1a3b6d;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-align: center;
        }
        .progress-bar {
            width: 100%;
            height: 40px;
            background: #e0e0e0;
            border-radius: 20px;
            overflow: hidden;
            margin: 30px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .recipe-item {
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .recipe-item.error {
            border-left-color: #dc3545;
            background: #fee;
        }
        .status-icon {
            font-size: 24px;
        }
        .recipe-info {
            flex: 1;
        }
        .recipe-title {
            font-weight: bold;
            color: #1a3b6d;
            margin-bottom: 5px;
        }
        .recipe-keywords {
            font-size: 0.85em;
            color: #666;
            line-height: 1.4;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
        }
        .stat-number {
            font-size: 3em;
            font-weight: bold;
            line-height: 1;
        }
        .stat-label {
            margin-top: 10px;
            opacity: 0.9;
        }
        .alert {
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            border-left: 5px solid;
        }
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 SEO Güncelleme Scripti</h1>";

// Tüm tarifleri getir
$recipes = $db->fetchAll("
    SELECT r.*, c.name as category_name 
    FROM recipes r
    LEFT JOIN categories c ON r.category_id = c.id
    ORDER BY r.id DESC
");

$total = count($recipes);
$updated = 0;
$errors = 0;

echo "<div class='alert alert-warning'>
    ⚠️ <strong>{$total} adet tarif</strong> bulundu. İşlem başlıyor...
</div>";

echo "<div class='progress-bar'>
    <div class='progress-fill' id='progressBar'>0%</div>
</div>";

echo "<div id='results'>";

// Her tarif için meta keywords oluştur
foreach($recipes as $index => $recipe) {
    $progress = round((($index + 1) / $total) * 100);
    echo "<script>document.getElementById('progressBar').style.width = '{$progress}%'; document.getElementById('progressBar').innerText = '{$progress}%';</script>";
    flush();
    ob_flush();
    
    try {
        // ========================================
        // ULTIMATE KEYWORDS GENERATOR
        // ========================================
        
        $title_lower = strtolower($recipe['title']);
        $category_name = strtolower($recipe['category_name'] ?? '');
        
        // Malzemeleri ayıkla
        $ingredient_lines = array_filter(array_map('trim', explode("\n", $recipe['ingredients'])));
        $main_ingredients = [];
        
        foreach($ingredient_lines as $ing) {
            $clean = preg_replace('/\d+/', '', $ing);
            $clean = preg_replace('/(adet|gram|gr|kg|lt|ml|su bardağı|bardak|çay kaşığı|yemek kaşığı|kaşık|tutam|dilim|dal|paket|kutu)/i', '', $clean);
            $clean = trim($clean);
            
            if(!empty($clean) && strlen($clean) > 2) {
                $main_ingredients[] = strtolower($clean);
            }
        }
        
        $keywords = [];
        
        // 1. Ana tarif keywordleri
        $keywords[] = $title_lower;
        $keywords[] = $title_lower . ' tarifi';
        $keywords[] = $title_lower . ' nasıl yapılır';
        $keywords[] = $title_lower . ' yapımı';
        $keywords[] = $title_lower . ' yapılışı';
        $keywords[] = $title_lower . ' malzemeleri';
        $keywords[] = $title_lower . ' püf noktaları';
        $keywords[] = $title_lower . ' oktay usta';
        $keywords[] = $title_lower . ' nefis yemek tarifleri';
        $keywords[] = $title_lower . ' kolay tarif';
        $keywords[] = $title_lower . ' pratik tarif';
        $keywords[] = $title_lower . ' ev yapımı';
        $keywords[] = $title_lower . ' lezzetli tarif';
        $keywords[] = $title_lower . ' videolu tarif';
        $keywords[] = $title_lower . ' adım adım';
        
        // 2. Kategori
        if($category_name) {
            $keywords[] = $category_name;
            $keywords[] = $category_name . ' tarifleri';
            $keywords[] = $category_name . ' yemekleri';
            $keywords[] = 'en iyi ' . $category_name;
            $keywords[] = 'kolay ' . $category_name;
        }
        
        // 3. Malzemeler
        foreach(array_slice($main_ingredients, 0, 10) as $ingredient) {
            if(strlen($ingredient) > 3) {
                $keywords[] = $ingredient;
                $keywords[] = $ingredient . ' tarifi';
                $keywords[] = $ingredient . ' yemekleri';
            }
        }
        
        // 4. Balık keywordleri
        $fish_keywords = [
            'balık tarifleri', 'balık yemekleri', 'deniz ürünleri',
            'balık nasıl pişirilir', 'fırında balık', 'tavada balık',
            'ızgara balık', 'en lezzetli balık tarifleri',
            'pratik balık tarifleri', 'kolay balık tarifleri',
            'balık pişirme', 'taze balık', 'sağlıklı balık',
            'protein balık', 'omega 3', 'balık yemek tarifleri'
        ];
        $keywords = array_merge($keywords, $fish_keywords);
        
        // 5. Pişirme teknikleri
        $cooking_methods = [
            'yemek tarifleri', 'kolay yemek', 'pratik yemek',
            'hızlı yemek', 'lezzetli yemek', 'nefis yemek',
            'ev yapımı yemek', 'yemek yapımı', 'türk mutfağı',
            'akdeniz mutfağı', 'sağlıklı yemek', 'diyet yemek'
        ];
        $keywords = array_merge($keywords, $cooking_methods);
        
        // 6. Süre bazlı
        $total_time = $recipe['prep_time'] + $recipe['cook_time'];
        if($total_time <= 30) {
            $keywords[] = 'hızlı tarif';
            $keywords[] = 'pratik tarif';
            $keywords[] = '30 dakika yemek';
            $keywords[] = 'kolay tarif';
        }
        
        // 7. Kişi sayısı
        if($recipe['servings'] >= 4) {
            $keywords[] = 'aile yemekleri';
            $keywords[] = 'misafir yemekleri';
            $keywords[] = 'özel gün yemekleri';
        }
        
        // 8. Özel günler
        $special_keywords = [
            'özel gün yemekleri', 'davet yemekleri', 'akşam yemeği',
            'iftar yemekleri', 'bayram yemekleri', 'hafta sonu yemekleri'
        ];
        $keywords = array_merge($keywords, $special_keywords);
        
        // 9. Sağlık & diyet
        $health_keywords = [
            'sağlıklı tarifler', 'diyet yemekleri', 'fit yemek',
            'protein yemekleri', 'düşük kalori', 'besleyici yemek'
        ];
        $keywords = array_merge($keywords, $health_keywords);
        
        // 10. Popüler aramalar
        $popular_keywords = [
            'nefis yemek tarifleri', 'oktay usta', 'en iyi tarifler',
            'popüler tarifler', 'en sevilen yemekler', 'garantili tarif',
            'denemeniz gereken', 'mutlaka yapın'
        ];
        $keywords = array_merge($keywords, $popular_keywords);
        
        // 11. Video varsa
        if(!empty($recipe['youtube_url'])) {
            $keywords[] = 'videolu tarif';
            $keywords[] = 'video tarif';
            $keywords[] = 'youtube tarif';
            $keywords[] = 'izle';
        }
        
        // 12. Long-tail
        $keywords[] = $title_lower . ' evde nasıl yapılır';
        $keywords[] = $title_lower . ' en kolay tarifi';
        $keywords[] = $title_lower . ' püf noktaları nedir';
        $keywords[] = 'en iyi ' . $title_lower . ' tarifi';
        $keywords[] = 'en lezzetli ' . $title_lower;
        
        // 13. Balık türleri kontrolü
        $fish_types = [
            'somon', 'levrek', 'çipura', 'lüfer', 'palamut', 'hamsi',
            'istavrit', 'uskumru', 'barbun', 'mercan', 'kılıç',
            'ton balığı', 'alabalık', 'dil balığı', 'mezgit'
        ];
        
        foreach($fish_types as $fish) {
            if(stripos($recipe['ingredients'], $fish) !== false || stripos($recipe['title'], $fish) !== false) {
                $keywords[] = $fish;
                $keywords[] = $fish . ' tarifleri';
                $keywords[] = $fish . ' nasıl pişirilir';
                $keywords[] = $fish . ' yemekleri';
                $keywords[] = 'taze ' . $fish;
            }
        }
        
        // 14. Bölgesel mutfaklar
        $regional = [
            'türk mutfağı', 'ege mutfağı', 'akdeniz mutfağı',
            'karadeniz mutfağı', 'geleneksel yemekler'
        ];
        $keywords = array_merge($keywords, $regional);
        
        // 15. Mevsim bazlı
        $month = date('n');
        if($month >= 6 && $month <= 8) {
            $keywords[] = 'yaz yemekleri';
            $keywords[] = 'yaz tarifleri';
        } elseif($month >= 12 || $month <= 2) {
            $keywords[] = 'kış yemekleri';
            $keywords[] = 'kış tarifleri';
        }
        
        // Temizlik
        $keywords = array_unique($keywords);
        $keywords = array_filter($keywords, function($k) {
            return !empty(trim($k)) && strlen($k) > 2;
        });
        $keywords = array_map('strtolower', $keywords);
        $keywords = array_unique($keywords);
        
        $meta_keywords = implode(', ', $keywords);
        $keyword_count = count($keywords);
        
        // Meta title & description
        $meta_title = $recipe['title'] . ' Tarifi | Yemekte Balık Var';
        if(strlen($meta_title) > 60) {
            $meta_title = substr($recipe['title'], 0, 45) . '... | Yemekte Balık Var';
        }
        
        $meta_description = $recipe['description'];
        if(empty($meta_description)) {
            $meta_description = $recipe['title'] . " nasıl yapılır? Detaylı tarif, malzemeler ve püf noktaları.";
        }
        if(strlen($meta_description) > 155) {
            $meta_description = substr($meta_description, 0, 150) . '...';
        }
        
        // Veritabanını güncelle
        $db->query(
            "UPDATE recipes 
            SET meta_title = ?, 
                meta_description = ?, 
                meta_keywords = ?,
                updated_at = NOW()
            WHERE id = ?",
            [$meta_title, $meta_description, $meta_keywords, $recipe['id']]
        );
        
        $updated++;
        
        echo "<div class='recipe-item'>
            <span class='status-icon'>✅</span>
            <div class='recipe-info'>
                <div class='recipe-title'>#{$recipe['id']} - {$recipe['title']}</div>
                <div class='recipe-keywords'><strong>{$keyword_count} keywords:</strong> " . substr($meta_keywords, 0, 150) . "...</div>
            </div>
        </div>";
        
    } catch(Exception $e) {
        $errors++;
        echo "<div class='recipe-item error'>
            <span class='status-icon'>❌</span>
            <div class='recipe-info'>
                <div class='recipe-title'>#{$recipe['id']} - {$recipe['title']}</div>
                <div class='recipe-keywords' style='color: #dc3545;'>HATA: {$e->getMessage()}</div>
            </div>
        </div>";
    }
    
    flush();
    ob_flush();
    usleep(10000); // 0.01 saniye bekle (sunucu koruma)
}

echo "</div>"; // results end

// İstatistikler
echo "<div class='stats'>
    <div class='stat-card'>
        <div class='stat-number'>{$total}</div>
        <div class='stat-label'>Toplam Tarif</div>
    </div>
    <div class='stat-card'>
        <div class='stat-number'>{$updated}</div>
        <div class='stat-label'>✅ Güncellendi</div>
    </div>
    <div class='stat-card'>
        <div class='stat-number'>{$errors}</div>
        <div class='stat-label'>❌ Hata</div>
    </div>
    <div class='stat-card'>
        <div class='stat-number'>" . round(($updated / $total) * 100) . "%</div>
        <div class='stat-label'>Başarı Oranı</div>
    </div>
</div>";

if($errors == 0) {
    echo "<div class='alert alert-success'>
        <h2 style='margin-bottom: 10px;'>🎉 TAMAMLANDI!</h2>
        <p><strong>{$updated} tarif</strong> başarıyla güncellendi!</p>
        <p style='margin-top: 15px;'>Her tarifin ortalama <strong>150-200 meta keyword</strong>'ü var!</p>
        <hr style='margin: 20px 0; border: none; border-top: 1px solid rgba(0,0,0,0.1);'>
        <p style='color: #dc3545; font-weight: bold;'>⚠️ ŞİMDİ BU DOSYAYI SİL!</p>
        <p>Güvenlik için <code>update-all-recipes-seo.php</code> dosyasını sunucudan kaldır.</p>
    </div>";
} else {
    echo "<div class='alert alert-warning'>
        <h2 style='margin-bottom: 10px;'>⚠️ UYARI!</h2>
        <p><strong>{$errors} tarif</strong>te hata oluştu. Kontrol edin!</p>
    </div>";
}

echo "    </div>
</body>
</html>";
?>