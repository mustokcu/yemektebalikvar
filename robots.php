<?php
/**
 * robots.php - Dynamic robots.txt Generator
 * 
 * Özellikler:
 * - Otomatik sitemap URL
 * - Bot-specific rules
 * - Security (admin panel block)
 * - SEO optimization
 * - Bad bot blocking
 * - Cache control
 * 
 * .htaccess ile aktif et:
 * RewriteRule ^robots\.txt$ robots.php [L]
 */

require_once 'includes/config.php';

// Cache headers (1 saat)
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=3600');
header('X-Robots-Tag: noindex'); // robots.txt'yi indexleme

// Başlık
echo "# ==============================================\n";
echo "# Robots.txt - " . SITE_TITLE . "\n";
echo "# Generated: " . date('Y-m-d H:i:s') . "\n";
echo "# ==============================================\n\n";

// =============================================
// 1. GENEL BOTLAR - HERŞEYİ TARA
// =============================================
echo "# Genel kurallar - Tüm botlar\n";
echo "User-agent: *\n";
echo "Allow: /\n\n";

// =============================================
// 2. GÜVENLİK - YASAKLI ALANLAR
// =============================================
echo "# Güvenlik - Admin & System Files\n";
echo "Disallow: /admin/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /cache/\n";
echo "Disallow: /config.php\n";
echo "Disallow: /database.php\n\n";

echo "# Upload klasörleri (indexleme gerekmez)\n";
echo "Disallow: /assets/uploads/\n\n";

// =============================================
// 3. SEO - DUPLICATE CONTENT ÖNLEYİCİ
// =============================================
echo "# Duplicate Content Önleme\n";
echo "# Arama sonuç sayfalarını engelle\n";
echo "Disallow: /*?ara=*\n";
echo "Disallow: /*?q=*\n";
echo "Disallow: /*?search=*\n\n";

echo "# Pagination parametreleri\n";
echo "Disallow: /*?sayfa=*\n";
echo "Disallow: /*?page=*\n\n";

echo "# Kategori filtreleri (clean URL'leri tercih et)\n";
echo "Disallow: /*?kategori=*\n";
echo "Disallow: /*?category=*\n\n";

echo "# Gereksiz parametreler\n";
echo "Disallow: /*?source=*\n";
echo "Disallow: /*?ref=*\n";
echo "Disallow: /*?utm_*\n";
echo "Disallow: /*?fbclid=*\n";
echo "Disallow: /*?gclid=*\n\n";

echo "# Session & Tracking\n";
echo "Disallow: /*?session=*\n";
echo "Disallow: /*?sid=*\n";
echo "Disallow: /*?PHPSESSID=*\n\n";

// =============================================
// 4. SİTEMAP KONUMU (ÇOK ÖNEMLİ!)
// =============================================
echo "# Sitemap Konumu\n";
echo "Sitemap: " . SITE_URL . "/sitemap.xml\n\n";

// Ek sitemaplar (eğer varsa)
// echo "Sitemap: " . SITE_URL . "/sitemap-images.xml\n";
// echo "Sitemap: " . SITE_URL . "/sitemap-videos.xml\n";
// echo "Sitemap: " . SITE_URL . "/sitemap-news.xml\n\n";

// =============================================
// 5. GOOGLE BOT - ÖZEL KURALLAR
// =============================================
echo "# ==============================================\n";
echo "# Google Bot Özel Kurallar\n";
echo "# ==============================================\n";
echo "User-agent: Googlebot\n";
echo "Allow: /\n";
echo "Allow: /assets/css/\n";
echo "Allow: /assets/js/\n";
echo "Allow: /assets/images/\n";
echo "Crawl-delay: 1\n\n";

echo "# Google Image Bot\n";
echo "User-agent: Googlebot-Image\n";
echo "Allow: /\n";
echo "Allow: /assets/uploads/recipes/\n";
echo "Allow: /assets/uploads/blog/\n";
echo "Allow: /assets/uploads/products/\n";
echo "Disallow: /assets/uploads/user-recipes/\n\n";

echo "# Google Mobile Bot\n";
echo "User-agent: Googlebot-Mobile\n";
echo "Allow: /\n";
echo "Crawl-delay: 1\n\n";

// =============================================
// 6. BING BOT - ÖZEL KURALLAR
// =============================================
echo "# ==============================================\n";
echo "# Bing Bot Özel Kurallar\n";
echo "# ==============================================\n";
echo "User-agent: Bingbot\n";
echo "Allow: /\n";
echo "Allow: /assets/css/\n";
echo "Allow: /assets/js/\n";
echo "Allow: /assets/images/\n";
echo "Crawl-delay: 1\n\n";

echo "# Bing Preview\n";
echo "User-agent: BingPreview\n";
echo "Allow: /\n";
echo "Crawl-delay: 2\n\n";

// =============================================
// 7. YANDEX BOT - ÖZEL KURALLAR
// =============================================
echo "# ==============================================\n";
echo "# Yandex Bot Özel Kurallar (Türkiye için önemli)\n";
echo "# ==============================================\n";
echo "User-agent: Yandex\n";
echo "Allow: /\n";
echo "Allow: /assets/css/\n";
echo "Allow: /assets/js/\n";
echo "Allow: /assets/images/\n";
echo "Crawl-delay: 2\n\n";

echo "User-agent: YandexBot\n";
echo "Allow: /\n";
echo "Crawl-delay: 2\n\n";

echo "User-agent: YandexImages\n";
echo "Allow: /\n";
echo "Allow: /assets/uploads/\n\n";

// =============================================
// 8. DİĞER İYİ BOTLAR
// =============================================
echo "# ==============================================\n";
echo "# Diğer İyi Botlar\n";
echo "# ==============================================\n";

echo "# DuckDuckGo\n";
echo "User-agent: DuckDuckBot\n";
echo "Allow: /\n";
echo "Crawl-delay: 2\n\n";

echo "# Baidu (Çin)\n";
echo "User-agent: Baiduspider\n";
echo "Allow: /\n";
echo "Crawl-delay: 3\n\n";

echo "# Facebook\n";
echo "User-agent: facebookexternalhit\n";
echo "Allow: /\n";
echo "Crawl-delay: 2\n\n";

echo "# Twitter\n";
echo "User-agent: Twitterbot\n";
echo "Allow: /\n";
echo "Crawl-delay: 2\n\n";

echo "# Pinterest\n";
echo "User-agent: Pinterest\n";
echo "Allow: /\n";
echo "Allow: /assets/uploads/recipes/\n";
echo "Crawl-delay: 2\n\n";

echo "# WhatsApp\n";
echo "User-agent: WhatsApp\n";
echo "Allow: /\n\n";

echo "# Telegram\n";
echo "User-agent: TelegramBot\n";
echo "Allow: /\n\n";

// =============================================
// 9. KÖTÜ BOTLAR - ENGELLE! (GÜVENLİK)
// =============================================
echo "# ==============================================\n";
echo "# KÖTÜ BOTLAR - TAMAMEN ENGELLE\n";
echo "# ==============================================\n\n";

$bad_bots = [
    'AhrefsBot' => 'SEO spider (gereksiz)',
    'SemrushBot' => 'SEO spider (gereksiz)',
    'MJ12bot' => 'Majestic crawler',
    'dotbot' => 'Moz crawler',
    'BLEXBot' => 'Backlink checker',
    'Bytespider' => 'ByteDance crawler',
    'PetalBot' => 'Huawei crawler',
    'Serpstatbot' => 'SEO tool',
    'AspiegelBot' => 'Mirror bot',
    'DomainCrawler' => 'Domain scraper',
    'MegaIndex' => 'Russian crawler',
    'Blackboard' => 'Content scraper',
    'Cliqzbot' => 'Privacy browser bot',
    'CCBot' => 'Common Crawl (AI training)',
    'GPTBot' => 'OpenAI crawler (AI training)',
    'ChatGPT-User' => 'ChatGPT bot',
    'anthropic-ai' => 'Claude bot',
    'cohere-ai' => 'Cohere AI',
    'omgili' => 'Content aggregator',
    'Omgilibot' => 'Content aggregator',
    'Barkrowler' => 'SEO spider',
    'DataForSeoBot' => 'SEO tool',
    'SEOkicks' => 'SEO tool',
    'DotBot' => 'OpenSiteExplorer',
    'linkdexbot' => 'SEO spider',
    'spbot' => 'SEO spider',
    'ZoominfoBot' => 'B2B data crawler',
    'MauiBot' => 'Scraper',
    'SurveyBot' => 'Survey scraper',
    'proximic' => 'Ad crawler',
    'AwarioRssBot' => 'Monitoring tool',
    'Sogou' => 'Chinese search (spam)',
    '360Spider' => 'Chinese search (spam)',
    'JikeSpider' => 'Chinese crawler'
];

foreach($bad_bots as $bot => $description) {
    echo "# $description\n";
    echo "User-agent: $bot\n";
    echo "Disallow: /\n\n";
}

// =============================================
// 10. AGGRESSIVE SCRAPERS - TAMAMEN ENGELLE
// =============================================
echo "# ==============================================\n";
echo "# Agresif Scraper'lar\n";
echo "# ==============================================\n";

$scrapers = [
    'HTTrack', 'WebCopier', 'WebZIP', 'WebReaper', 'WebStripper',
    'WebSucker', 'WebWhacker', 'Express WebPictures', 'ExtractorPro',
    'Offline Explorer', 'EmailCollector', 'EmailSiphon', 'EmailWolf',
    'LinkScan', 'LinkWalker', 'NICErsPRO', 'ProWebWalker', 'SiteSnagger',
    'TeleportPro', 'WebAuto', 'WebBandit', 'WebEnhancer', 'WebMasterWorldForumBot'
];

foreach($scrapers as $scraper) {
    echo "User-agent: $scraper\n";
    echo "Disallow: /\n";
}
echo "\n";

// =============================================
// 11. DOWNLOADER BOTLARI
// =============================================
echo "# Download botları\n";
$downloaders = ['Wget', 'Curl', 'libwww-perl'];
foreach($downloaders as $dl) {
    echo "User-agent: $dl\n";
    echo "Disallow: /\n";
}
echo "\n";

// =============================================
// 12. AI TRAINING BOTLARI (Opsiyonel)
// =============================================
echo "# ==============================================\n";
echo "# AI Training Botları (İçeriğini korumak istersen)\n";
echo "# ==============================================\n";
echo "# Not: Bu botları engellemek opsiyoneldir\n";
echo "# İçeriğinin AI'lar tarafından kullanılmasını istemiyorsan aç\n\n";

$ai_bots = [
    'GPTBot' => 'OpenAI (ChatGPT training)',
    'ChatGPT-User' => 'ChatGPT web browsing',
    'anthropic-ai' => 'Claude AI',
    'Claude-Web' => 'Claude web search',
    'cohere-ai' => 'Cohere AI',
    'Google-Extended' => 'Google Bard training',
    'CCBot' => 'Common Crawl (AI datasets)',
    'PerplexityBot' => 'Perplexity AI',
    'Diffbot' => 'AI knowledge graph',
    'FacebookBot' => 'Meta AI training'
];

foreach($ai_bots as $bot => $desc) {
    echo "# $desc\n";
    echo "# User-agent: $bot\n";
    echo "# Disallow: /\n\n";
}

echo "# Yukarıdaki botları engellemek için # işaretlerini kaldır\n\n";

// =============================================
// 13. BİLGİLENDİRME
// =============================================
echo "# ==============================================\n";
echo "# Bilgilendirme\n";
echo "# ==============================================\n";
echo "# Bu robots.txt dinamik olarak oluşturulmuştur.\n";
echo "# Sorularınız için: info@" . str_replace(['http://', 'https://', 'www.'], '', SITE_URL) . "\n";
echo "# Son güncelleme: " . date('Y-m-d H:i:s') . "\n";
echo "# ==============================================\n";
?>