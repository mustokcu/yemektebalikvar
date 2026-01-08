<?php
/**
 * sitemap.xml - Production Ready XML Sitemap Generator
 * 
 * Özellikler:
 * - Image sitemap dahil
 * - Video sitemap dahil
 * - Cache sistemi (1 saat)
 * - Gzip compression
 * - 50,000 URL limiti
 * - Priority & changefreq optimization
 * 
 * URL: https://yemektebalikvar.com/sitemap.xml
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Cache kontrolü (1 saat)
$cache_file = __DIR__ . '/cache/sitemap.xml';
$cache_time = 3600; // 1 saat

if(!is_dir(__DIR__ . '/cache')) {
    mkdir(__DIR__ . '/cache', 0755, true);
}

// Cache varsa ve güncel ise onu döndür
if(file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    header('Content-Type: application/xml; charset=utf-8');
    header('X-Sitemap-Cache: HIT');
    readfile($cache_file);
    exit;
}

// Cache yoksa veya eski ise yeni sitemap oluştur
ob_start();

// Headers
header('Content-Type: application/xml; charset=utf-8');
header('X-Sitemap-Cache: MISS');
header('X-Robots-Tag: noindex');

// XML başlangıç
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<?xml-stylesheet type="text/xsl" href="https://yemektebalikvar.com/sitemap.xsl"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' . "\n";
echo '        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";

/**
 * URL Ekleme Fonksiyonu
 */
function addUrl($loc, $lastmod = null, $changefreq = 'weekly', $priority = '0.5', $images = [], $videos = []) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
    
    if($lastmod) {
        $date = is_numeric($lastmod) ? date('c', $lastmod) : date('c', strtotime($lastmod));
        echo "    <lastmod>{$date}</lastmod>\n";
    }
    
    echo "    <changefreq>{$changefreq}</changefreq>\n";
    echo "    <priority>{$priority}</priority>\n";
    
    // Image Sitemap
    foreach($images as $image) {
        echo "    <image:image>\n";
        echo "      <image:loc>" . htmlspecialchars($image['loc'], ENT_XML1) . "</image:loc>\n";
        if(isset($image['title'])) {
            echo "      <image:title>" . htmlspecialchars($image['title'], ENT_XML1) . "</image:title>\n";
        }
        if(isset($image['caption'])) {
            echo "      <image:caption>" . htmlspecialchars($image['caption'], ENT_XML1) . "</image:caption>\n";
        }
        echo "    </image:image>\n";
    }
    
    // Video Sitemap
    foreach($videos as $video) {
        echo "    <video:video>\n";
        echo "      <video:thumbnail_loc>" . htmlspecialchars($video['thumbnail'], ENT_XML1) . "</video:thumbnail_loc>\n";
        echo "      <video:title>" . htmlspecialchars($video['title'], ENT_XML1) . "</video:title>\n";
        echo "      <video:description>" . htmlspecialchars($video['description'], ENT_XML1) . "</video:description>\n";
        
        if(isset($video['content_loc'])) {
            echo "      <video:content_loc>" . htmlspecialchars($video['content_loc'], ENT_XML1) . "</video:content_loc>\n";
        }
        if(isset($video['player_loc'])) {
            echo "      <video:player_loc>" . htmlspecialchars($video['player_loc'], ENT_XML1) . "</video:player_loc>\n";
        }
        if(isset($video['duration'])) {
            echo "      <video:duration>{$video['duration']}</video:duration>\n";
        }
        if(isset($video['publication_date'])) {
            echo "      <video:publication_date>" . date('c', strtotime($video['publication_date'])) . "</video:publication_date>\n";
        }
        
        echo "    </video:video>\n";
    }
    
    echo "  </url>\n";
}

// 1. ANA SAYFA (En yüksek öncelik)
addUrl(
    'https://yemektebalikvar.com',
    time(),
    'daily',
    '1.0'
);

// 2. STATİK SAYFALAR (Yüksek öncelik)
$static_pages = [
    [
        'url' => '/pages/tarifler',
        'changefreq' => 'daily',
        'priority' => '0.9'
    ],
    [
        'url' => '/pages/blog',
        'changefreq' => 'daily',
        'priority' => '0.8'
    ],
    [
        'url' => '/pages/urunler',
        'changefreq' => 'weekly',
        'priority' => '0.7'
    ],
    [
        'url' => '/pages/sizden-gelenler',
        'changefreq' => 'daily',
        'priority' => '0.8'
    ],
    [
        'url' => '/pages/tarif-gonder',
        'changefreq' => 'monthly',
        'priority' => '0.4'
    ]
];

foreach($static_pages as $page) {
    addUrl(
        'https://yemektebalikvar.com' . $page['url'],
        null,
        $page['changefreq'],
        $page['priority']
    );
}

// 3. TARİFLER (En önemli içerik - Image + Video sitemap)
try {
    $recipes = $db->fetchAll("
        SELECT 
            r.slug, 
            r.title, 
            r.description,
            r.image, 
            r.youtube_url,
            r.created_at, 
            r.updated_at,
            r.views
        FROM recipes r
        WHERE r.status = 'active' 
        ORDER BY r.views DESC, r.created_at DESC
        LIMIT 5000
    ");
    
    foreach($recipes as $recipe) {
        $images = [];
        $videos = [];
        
        // Image ekle
        if($recipe['image']) {
            $images[] = [
                'loc' => 'https://yemektebalikvar.com/assets/uploads/recipes/' . $recipe['image'],
                'title' => $recipe['title'] . ' Tarifi',
                'caption' => $recipe['description']
            ];
        }
        
        // YouTube video ekle
        if($recipe['youtube_url']) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $recipe['youtube_url'], $matches);
            if(isset($matches[1])) {
                $video_id = $matches[1];
                $videos[] = [
                    'thumbnail' => 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg',
                    'title' => $recipe['title'] . ' Yapım Videosu',
                    'description' => $recipe['description'],
                    'content_loc' => $recipe['youtube_url'],
                    'player_loc' => 'https://www.youtube.com/embed/' . $video_id,
                    'publication_date' => $recipe['created_at']
                ];
            }
        }
        
        $lastmod = $recipe['updated_at'] ?? $recipe['created_at'];
        
        // Views'e göre priority hesapla (0.6 - 1.0 arası)
        $priority = min(1.0, 0.6 + ($recipe['views'] / 10000));
        $priority = number_format($priority, 1);
        
        addUrl(
            'https://yemektebalikvar.com/tarif/' . $recipe['slug'],
            $lastmod,
            'weekly',
            $priority,
            $images,
            $videos
        );
    }
} catch(Exception $e) {
    error_log("Sitemap tarifleri hatası: " . $e->getMessage());
}

// 4. BLOG YAZILARI (Image sitemap dahil)
try {
    $posts = $db->fetchAll("
        SELECT 
            b.slug, 
            b.title, 
            b.excerpt,
            b.image, 
            b.created_at, 
            b.updated_at 
        FROM blog_posts b
        WHERE b.status = 'active' 
        ORDER BY b.created_at DESC
        LIMIT 2000
    ");
    
    foreach($posts as $post) {
        $images = [];
        
        if($post['image']) {
            $images[] = [
                'loc' => 'https://yemektebalikvar.com/assets/uploads/blog/' . $post['image'],
                'title' => $post['title'],
                'caption' => $post['excerpt']
            ];
        }
        
        $lastmod = $post['updated_at'] ?? $post['created_at'];
        
        addUrl(
            'https://yemektebalikvar.com/blog/' . $post['slug'],
            $lastmod,
            'monthly',
            '0.7',
            $images
        );
    }
} catch(Exception $e) {
    error_log("Sitemap blog hatası: " . $e->getMessage());
}

// 5. ÜRÜNLER (Image sitemap dahil)
try {
    $products = $db->fetchAll("
        SELECT 
            p.slug, 
            p.name, 
            p.description,
            p.image, 
            p.created_at 
        FROM products p
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC
        LIMIT 1000
    ");
    
    foreach($products as $product) {
        $images = [];
        
        if($product['image']) {
            $images[] = [
                'loc' => 'https://yemektebalikvar.com/assets/uploads/products/' . $product['image'],
                'title' => $product['name'],
                'caption' => $product['description']
            ];
        }
        
        addUrl(
            'https://yemektebalikvar.com/urun/' . $product['slug'],
            $product['created_at'],
            'monthly',
            '0.6',
            $images
        );
    }
} catch(Exception $e) {
    error_log("Sitemap ürünler hatası: " . $e->getMessage());
}

// 6. KULLANICI TARİFLERİ (Onaylı)
try {
    $user_recipes = $db->fetchAll("
        SELECT 
            ur.id, 
            ur.title, 
            ur.image,
            ur.created_at,
            ur.view_count
        FROM user_recipes ur
        WHERE ur.status = 'approved' 
        ORDER BY ur.view_count DESC, ur.created_at DESC
        LIMIT 2000
    ");
    
    foreach($user_recipes as $ur) {
        $images = [];
        
        if($ur['image']) {
            $images[] = [
                'loc' => 'https://yemektebalikvar.com/assets/uploads/user-recipes/' . $ur['image'],
                'title' => $ur['title']
            ];
        }
        
        $priority = min(0.7, 0.4 + ($ur['view_count'] / 5000));
        $priority = number_format($priority, 1);
        
        addUrl(
            'https://yemektebalikvar.com/community/' . $ur['id'],
            $ur['created_at'],
            'monthly',
            $priority,
            $images
        );
    }
} catch(Exception $e) {
    error_log("Sitemap kullanıcı tarifleri hatası: " . $e->getMessage());
}

// 7. KATEGORİLER (Tarif)
try {
    $categories = $db->fetchAll("
        SELECT c.slug, c.name
        FROM categories c
        WHERE c.status = 'active' AND c.type = 'recipe'
        ORDER BY c.name
    ");
    
    foreach($categories as $cat) {
        addUrl(
            'https://yemektebalikvar.com/kategori/' . $cat['slug'],
            null,
            'weekly',
            '0.7'
        );
    }
} catch(Exception $e) {
    error_log("Sitemap kategoriler hatası: " . $e->getMessage());
}

// 8. KATEGORİLER (Blog)
try {
    $blog_categories = $db->fetchAll("
        SELECT c.slug, c.name
        FROM categories c
        WHERE c.status = 'active' AND c.type = 'blog'
        ORDER BY c.name
    ");
    
    foreach($blog_categories as $cat) {
        addUrl(
            'https://yemektebalikvar.com/pages/blog?kategori=' . $cat['slug'],
            null,
            'weekly',
            '0.6'
        );
    }
} catch(Exception $e) {
    error_log("Sitemap blog kategorileri hatası: " . $e->getMessage());
}

// XML bitir
echo '</urlset>';

// Cache'e kaydet
$sitemap_content = ob_get_clean();
file_put_contents($cache_file, $sitemap_content);

// Çıktıyı göster
echo $sitemap_content;

// Gzip compression (opsiyonel)
if(extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
    ob_start('ob_gzhandler');
}
?>