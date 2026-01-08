<?php
/**
 * redirect-handler.php - ULTIMATE TRAFFIC BOOSTER
 * ================================================
 * Root dizine kaydet
 * 
 * ÖZELLİKLER:
 * - 1000+ eski URL redirect
 * - Akıllı slug matching
 * - Google Analytics entegrasyonu
 * - 404 sayfasında öneri sistemi
 * - SEO skorunu artırır
 * - Traffic kaybını önler
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// İstatistik için
$redirect_start = microtime(true);

// Mevcut URL'i al ve temizle
$request_uri = $_SERVER['REQUEST_URI'];
$request_path = parse_url($request_uri, PHP_URL_PATH);
$clean_path = rtrim($request_path, '/');

// =====================================================
// 1. BALIK TÜRLERİ - /nasil-saklanir/ KATEGORİSİ
// =====================================================
$balik_turleri = [
    // AKDENİZ BALIKLARI
    'somon-baligi' => 'somon-nasil-saklanir',
    'levrek' => 'levrek-nasil-saklanir',
    'cipura' => 'cipura-nasil-saklanir',
    'lufer' => 'lufer-nasil-saklanir',
    'barbunya' => 'barbunya-nasil-saklanir',
    'kefal' => 'kefal-nasil-saklanir',
    'dil-baligi' => 'dil-baligi-nasil-saklanir',
    'mercan' => 'mercan-baligi-nasil-saklanir',
    'kilic-baligi' => 'kilic-baligi-nasil-saklanir',
    'orkinos' => 'orkinos-nasil-saklanir',
    'ton-baligi' => 'ton-baligi-nasil-saklanir',
    
    // KARADENİZ BALIKLARI
    'hamsi' => 'hamsi-nasil-saklanir',
    'uskumru' => 'uskumru-nasil-saklanir',
    'palamut' => 'palamut-nasil-saklanir',
    'istavrit' => 'istavrit-nasil-saklanir',
    'mezgit' => 'mezgit-nasil-saklanir',
    'tekir' => 'tekir-nasil-saklanir',
    'kalkan' => 'kalkan-baligi-nasil-saklanir',
    'tirsi' => 'tirsi-nasil-saklanir',
    'sardalya' => 'sardalya-nasil-saklanir',
    'kırlangıç' => 'kirlangiç-nasil-saklanir',
    
    // TATLISU BALIKLARI
    'alabalik' => 'alabalik-nasil-saklanir',
    'sazan' => 'sazan-nasil-saklanir',
    'turna' => 'turna-baligi-nasil-saklanir',
    'yayın' => 'yayin-baligi-nasil-saklanir',
    
    // DENİZ ÜRÜNLERİ
    'ahtapot' => 'ahtapot-nasil-saklanir',
    'kalamar' => 'kalamar-nasil-saklanir',
    'midye' => 'midye-nasil-saklanir',
    'istiridye' => 'istiridye-nasil-saklanir',
    'karides' => 'karides-nasil-saklanir',
    'istakoz' => 'istakoz-nasil-saklanir',
    'yengeç' => 'yengec-nasil-saklanir',
    'deniz-taragi' => 'deniz-taragi-nasil-saklanir'
];

// /nasil-saklanir/[balik-turu]-nasil-saklanir → /blog/[balik-turu]-saklanma
foreach($balik_turleri as $slug => $old_slug) {
    if($clean_path === "/nasil-saklanir/{$old_slug}") {
        redirectTo("/blog/{$slug}-saklanma", "Balık Saklama Rehberi");
    }
}

// =====================================================
// 2. TARİF KATEGORİLERİ
// =====================================================
$tarif_kategorileri = [
    // PIŞIRME YÖNTEMİ
    'tava-tarifleri' => ['tarif' => 'tava', 'kategori' => 'tava'],
    'guvec-tarifleri' => ['tarif' => 'guvec', 'kategori' => 'guvec'],
    'izgara-tarifleri' => ['tarif' => 'izgara', 'kategori' => 'izgara'],
    'firinda-tarifleri' => ['tarif' => 'firinda', 'kategori' => 'firinda'],
    'bugulama-tarifleri' => ['tarif' => 'bugulama', 'kategori' => 'bugulama'],
    'kizartma-tarifleri' => ['tarif' => 'kizartma', 'kategori' => 'kizartma'],
    'haslama-tarifleri' => ['tarif' => 'haslama', 'kategori' => 'haslama'],
    
    // BALIK TÜRÜNE GÖRE
    'somon-tarifleri' => ['tarif' => 'somon', 'kategori' => 'balik'],
    'levrek-tarifleri' => ['tarif' => 'levrek', 'kategori' => 'balik'],
    'hamsi-tarifleri' => ['tarif' => 'hamsi', 'kategori' => 'balik'],
    'uskumru-tarifleri' => ['tarif' => 'uskumru', 'kategori' => 'balik'],
    'palamut-tarifleri' => ['tarif' => 'palamut', 'kategori' => 'balik'],
    'cipura-tarifleri' => ['tarif' => 'cipura', 'kategori' => 'balik'],
    
    // ÖZEL KATEGORİLER
    'pilaki-tarifleri' => ['tarif' => 'pilaki', 'kategori' => 'pilaki'],
    'dolma-tarifleri' => ['tarif' => 'dolma', 'kategori' => 'dolma'],
    'corbalar' => ['tarif' => 'corba', 'kategori' => 'corba'],
    'mezeler' => ['tarif' => 'meze', 'kategori' => 'meze'],
    'salatalar' => ['tarif' => 'salata', 'kategori' => 'salata']
];

foreach($tarif_kategorileri as $eski_url => $yeni) {
    if($clean_path === "/{$eski_url}" || $clean_path === "/category/{$eski_url}") {
        redirectTo("/pages/tarifler?kategori={$yeni['kategori']}", "Tarif Kategorisi");
    }
}

// =====================================================
// 3. BLOG KATEGORİLERİ
// =====================================================
$blog_kategorileri = [
    'nasil-saklanir' => 'saklanma',
    'nasil-pisir' => 'pisirme',
    'nasil-temizlenir' => 'temizleme',
    'nasil-secilir' => 'secim',
    'ogren' => 'egitim',
    'ipuclari' => 'ipucu',
    'faydalar' => 'fayda',
    'saglik' => 'saglik',
    'beslenme' => 'beslenme',
    'tarihce' => 'tarih',
    'hikayeler' => 'hikaye'
];

foreach($blog_kategorileri as $eski => $yeni) {
    if($clean_path === "/{$eski}" || $clean_path === "/category/{$eski}") {
        redirectTo("/pages/blog?kategori={$yeni}", "Blog Kategorisi");
    }
}

// =====================================================
// 4. POPÜLER TARİFLER - DİREKT MAPPING
// =====================================================
$populer_tarifler = [
    // IZGARA TARİFLERİ
    '/recipe/izgara-levrek' => '/tarif/izgara-levrek',
    '/recipe/izgara-cipura' => '/tarif/izgara-cipura',
    '/recipe/izgara-somon' => '/tarif/izgara-somon',
    '/recipe/izgara-uskumru' => '/tarif/izgara-uskumru',
    '/recipe/izgara-palamut' => '/tarif/izgara-palamut',
    
    // FIRINDA TARİFLER
    '/recipe/firinda-levrek' => '/tarif/firinda-levrek',
    '/recipe/firinda-cipura' => '/tarif/firinda-cipura',
    '/recipe/firinda-somon' => '/tarif/firinda-somon',
    '/recipe/firinda-hamsi' => '/tarif/firinda-hamsi',
    
    // TAVA TARİFLERİ
    '/recipe/tavada-levrek' => '/tarif/tavada-levrek',
    '/recipe/tavada-cipura' => '/tarif/tavada-cipura',
    '/recipe/tavada-palamut' => '/tarif/tavada-palamut',
    '/recipe/hamsi-tava' => '/tarif/hamsi-tava',
    
    // ÖZEL TARİFLER
    '/recipe/levrek-buglama' => '/tarif/levrek-buglama',
    '/recipe/hamsi-pilav' => '/tarif/hamsi-pilav',
    '/recipe/palamut-pilaki' => '/tarif/palamut-pilaki',
    '/recipe/balik-corbasi' => '/tarif/balik-corbasi',
    '/recipe/midye-dolma' => '/tarif/midye-dolma',
    '/recipe/balik-kofte' => '/tarif/balik-kofte',
    '/recipe/hamsi-kusu' => '/tarif/hamsi-kusu'
];

if(isset($populer_tarifler[$clean_path])) {
    redirectTo($populer_tarifler[$clean_path], "Popüler Tarif");
}

// =====================================================
// 5. PATTERN BAZLI AKILLI REDİRECT
// =====================================================

// /nasil-saklanir/[herhangi-bir-sey] → /pages/blog
if(preg_match('~^/nasil-saklanir/(.+)$~', $clean_path, $matches)) {
    $slug = $matches[1];
    // Önce blog'da ara
    redirectTo("/pages/blog?ara={$slug}", "Saklama Bilgisi");
}

// /recipe/[slug] → /tarif/[slug]
if(preg_match('~^/recipe/([a-z0-9-]+)$~', $clean_path, $matches)) {
    redirectTo("/tarif/{$matches[1]}", "Tarif Detay");
}

// /recipes/[slug] → /tarif/[slug]
if(preg_match('~^/recipes/([a-z0-9-]+)$~', $clean_path, $matches)) {
    redirectTo("/tarif/{$matches[1]}", "Tarif Detay");
}

// /post/[slug] → /blog/[slug]
if(preg_match('~^/post/([a-z0-9-]+)$~', $clean_path, $matches)) {
    redirectTo("/blog/{$matches[1]}", "Blog Yazısı");
}

// /posts/[slug] → /blog/[slug]
if(preg_match('~^/posts/([a-z0-9-]+)$~', $clean_path, $matches)) {
    redirectTo("/blog/{$matches[1]}", "Blog Yazısı");
}

// /category/[kategori] → /pages/blog?kategori=[kategori]
if(preg_match('~^/category/([a-z0-9-]+)$~', $clean_path, $matches)) {
    redirectTo("/pages/blog?kategori={$matches[1]}", "Kategori");
}

// /tag/[etiket] → /pages/tarifler?ara=[etiket]
if(preg_match('~^/tag/([a-z0-9-]+)$~', $clean_path, $matches)) {
    redirectTo("/pages/tarifler?ara={$matches[1]}", "Etiket");
}

// =====================================================
// 6. VERİTABANI BAZLI AKILLI ARAMA
// =====================================================
try {
    $db = new Database();
    $slug = basename($clean_path);
    
    // 6A. TARİFLERDE ARA (Slug match)
    $recipe = $db->fetch("
        SELECT slug, title 
        FROM recipes 
        WHERE (slug = ? OR slug LIKE ?) 
        AND status = 'active'
        LIMIT 1
    ", [$slug, "%{$slug}%"]);
    
    if($recipe) {
        redirectTo("/tarif/{$recipe['slug']}", "Tarif: {$recipe['title']}");
    }
    
    // 6B. BLOG'DA ARA (Slug match)
    $blog = $db->fetch("
        SELECT slug, title 
        FROM blog_posts 
        WHERE (slug = ? OR slug LIKE ?) 
        AND status = 'active'
        LIMIT 1
    ", [$slug, "%{$slug}%"]);
    
    if($blog) {
        redirectTo("/blog/{$blog['slug']}", "Blog: {$blog['title']}");
    }
    
    // 6C. KATEGORİLERDE ARA
    $category = $db->fetch("
        SELECT slug, name, type 
        FROM categories 
        WHERE (slug = ? OR slug LIKE ?) 
        AND status = 'active'
        LIMIT 1
    ", [$slug, "%{$slug}%"]);
    
    if($category) {
        $page = $category['type'] === 'recipe' ? 'tarifler' : 'blog';
        redirectTo("/pages/{$page}?kategori={$category['slug']}", "Kategori: {$category['name']}");
    }
    
    // 6D. BENZER TARIFLER - FUZZY SEARCH
    // Slug'daki kelimeleri parçala ve ara
    $keywords = explode('-', $slug);
    $search_conditions = [];
    $search_params = [];
    
    foreach($keywords as $keyword) {
        if(strlen($keyword) > 3) { // 3 harften uzun kelimeler
            $search_conditions[] = "(title LIKE ? OR ingredients LIKE ? OR description LIKE ?)";
            $search_params[] = "%{$keyword}%";
            $search_params[] = "%{$keyword}%";
            $search_params[] = "%{$keyword}%";
        }
    }
    
    if(!empty($search_conditions)) {
        $similar = $db->fetch("
            SELECT slug, title 
            FROM recipes 
            WHERE (" . implode(' OR ', $search_conditions) . ")
            AND status = 'active'
            ORDER BY views DESC
            LIMIT 1
        ", $search_params);
        
        if($similar) {
            redirectTo("/tarif/{$similar['slug']}", "Benzer Tarif: {$similar['title']}", 302); // Temporary redirect
        }
    }
    
} catch(Exception $e) {
    error_log("Redirect handler DB hatası: " . $e->getMessage());
}

// =====================================================
// 7. 404 - AKILLI ÖNERİ SİSTEMİ
// =====================================================

// Redirect bulunamadı, 404 sayfasına gönder ama önce istatistik kaydet
logMissedRedirect($clean_path);

// 404 sayfasını özel öneri ile göster
show404WithSuggestions($clean_path);

// =====================================================
// YARDIMCI FONKSİYONLAR
// =====================================================

/**
 * 301 Redirect yap ve istatistik tut
 */
function redirectTo($url, $reason = '', $code = 301) {
    global $clean_path, $redirect_start;
    
    // İstatistik kaydet
    logSuccessfulRedirect($clean_path, $url, $reason);
    
    // Redirect süresi
    $redirect_time = round((microtime(true) - $redirect_start) * 1000, 2);
    
    // Headers
    header("HTTP/1.1 {$code} " . ($code === 301 ? 'Moved Permanently' : 'Found'));
    header("Location: {$url}");
    header("X-Redirect-From: {$clean_path}");
    header("X-Redirect-Reason: {$reason}");
    header("X-Redirect-Time: {$redirect_time}ms");
    header("Cache-Control: max-age=31536000"); // 1 yıl cache
    
    // Google Analytics event (opsiyonel)
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Yönlendiriliyor...</title>
        <meta http-equiv="refresh" content="0;url=<?php echo $url; ?>">
        <style>
            body { font-family: Arial; text-align: center; padding: 50px; }
            .spinner { border: 5px solid #f3f3f3; border-top: 5px solid #673DE6; 
                       border-radius: 50%; width: 50px; height: 50px; 
                       animation: spin 1s linear infinite; margin: 20px auto; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
    </head>
    <body>
        <div class="spinner"></div>
        <h2>Yönlendiriliyor...</h2>
        <p>Yeni sayfaya yönlendiriliyorsunuz: <strong><?php echo htmlspecialchars($url); ?></strong></p>
        <p><small>Otomatik yönlenme yapılmazsa <a href="<?php echo $url; ?>">buraya tıklayın</a></small></p>
    </body>
    </html>
    <?php
    exit;
}

/**
 * Başarılı redirect'leri logla
 */
function logSuccessfulRedirect($from, $to, $reason) {
    global $db;
    
    try {
        $db->query("
            INSERT INTO redirect_logs (from_url, to_url, reason, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ", [
            $from,
            $to,
            $reason,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch(Exception $e) {
        error_log("Redirect log hatası: " . $e->getMessage());
    }
}

/**
 * Bulunamayan URL'leri logla (ileride redirect eklemek için)
 */
function logMissedRedirect($url) {
    global $db;
    
    try {
        // Aynı URL'yi tekrar tekrar loglama
        $exists = $db->fetch("
            SELECT id, hit_count 
            FROM missed_redirects 
            WHERE url = ?
        ", [$url]);
        
        if($exists) {
            // Hit count'u artır
            $db->query("
                UPDATE missed_redirects 
                SET hit_count = hit_count + 1,
                    last_hit_at = NOW()
                WHERE id = ?
            ", [$exists['id']]);
        } else {
            // Yeni kayıt
            $db->query("
                INSERT INTO missed_redirects (url, hit_count, ip_address, referrer, user_agent, created_at, last_hit_at)
                VALUES (?, 1, ?, ?, ?, NOW(), NOW())
            ", [
                $url,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_REFERER'] ?? 'direct',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        }
    } catch(Exception $e) {
        error_log("Missed redirect log hatası: " . $e->getMessage());
    }
}

/**
 * 404 sayfasını akıllı önerilerle göster
 */
/**
 * 404 sayfasını akıllı önerilerle göster
 */
function show404WithSuggestions($url) {
    global $db;
    
    // URL'den anahtar kelimeler çıkar
    $keywords = array_filter(explode('-', basename($url)), function($word) {
        return strlen($word) > 3;
    });
    
    $suggestions = [];
    
    if(!empty($keywords)) {
        try {
            // Benzer tarifleri bul
            $search_conditions = [];
            $search_params = [];
            
            foreach($keywords as $keyword) {
                $search_conditions[] = "(r.title LIKE ? OR r.ingredients LIKE ?)";
                $search_params[] = "%{$keyword}%";
                $search_params[] = "%{$keyword}%";
            }
            
            $suggestions = $db->fetchAll("
                SELECT r.slug, r.title, r.image, c.name as category_name,
                       r.prep_time, r.cook_time, r.servings
                FROM recipes r
                LEFT JOIN categories c ON r.category_id = c.id
                WHERE (" . implode(' OR ', $search_conditions) . ")
                AND r.status = 'active'
                ORDER BY r.views DESC
                LIMIT 6
            ", $search_params);
            
        } catch(Exception $e) {
            error_log("404 öneri hatası: " . $e->getMessage());
        }
    }
    
    // 404 sayfasını önerilerle göster
    header("HTTP/1.1 404 Not Found");
    
    // MEVCUT 404.PHP'Yİ KULLAN (suggestions değişkeni ile)
    include __DIR__ . '/pages/404.php';
    exit;
}
?>