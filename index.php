<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/seo.php';

// Analiz kaydı
logAnalytics('/');

// Site ayarlarını getir
$settings = getSettings();
$site_title = $settings['site_title'] ?? SITE_TITLE;
$site_description = $settings['site_description'] ?? SITE_DESCRIPTION;

// Verileri try-catch ile güvenli şekilde getir
try {
    // Öne çıkan tarifleri getir (4 adet)
    $featured_recipes = getFeaturedRecipes(4);
    
    // Videolu tarifler önce, sonra en çok görüntülenen tarifler
    $recent_recipes = $db->fetchAll("
        SELECT r.*, c.name as category_name, c.slug as category_slug
        FROM recipes r
        LEFT JOIN categories c ON r.category_id = c.id
        WHERE r.status = 'active'
        ORDER BY 
            CASE 
                WHEN r.youtube_url IS NOT NULL AND r.youtube_url != '' THEN 0
                ELSE 1
            END,
            r.views DESC,
            r.created_at DESC
        LIMIT 100
    ");
    
    // Kategorileri getir
    $categories = getCategories('recipe');
    
    // Son blog yazılarını getir
    $recent_posts = getRecentBlogPosts(3);
    
} catch (Exception $e) {
    // Hata durumunda boş array'ler kullan
    $featured_recipes = [];
    $recent_recipes = [];
    $categories = [];
    $recent_posts = [];
    
    error_log("Index.php veri getirme hatası: " . $e->getMessage());
}

// İlk gösterilecek tarif sayısı
$displayCount = 12;
$recipesToShow = array_slice($recent_recipes, 0, $displayCount);
$totalRecipes = count($recent_recipes);

// Canonical URL
$canonical_url = SITE_URL;

// Meta description optimize
$meta_description = "Türkiye'nin en lezzetli balık ve deniz ürünleri tarifleri. " . number_format($totalRecipes) . "+ tarif, adım adım yapılış, malzemeler ve püf noktaları.";

// Keywords
$meta_keywords = "balık tarifleri, deniz ürünleri, levrek, çipura, somon, hamsi, palamut, yemek tarifleri, ev yemekleri";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Temel Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Title & Description -->
    <title><?php echo htmlspecialchars($site_title); ?> | Lezzetli Balık ve Deniz Ürünleri Tarifleri</title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $canonical_url; ?>">
    
    <!-- Robots -->
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <?php 
    $og_data = [
        'title' => $site_title . ' | En Lezzetli Balık Tarifleri',
        'description' => $meta_description,
        'type' => 'website',
        'url' => $canonical_url,
        'image' => SITE_URL . '/assets/images/og-home.jpg'
    ];
    
    if(function_exists('generateOGTags')) {
        echo generateOGTags($og_data);
    } else {
        ?>
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?php echo $canonical_url; ?>">
        <meta property="og:title" content="<?php echo htmlspecialchars($site_title); ?> | En Lezzetli Balık Tarifleri">
        <meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
        <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-home.jpg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:locale" content="tr_TR">
        <meta property="og:site_name" content="<?php echo htmlspecialchars($site_title); ?>">
        <?php
    }
    ?>
    
    <!-- Twitter Card -->
    <?php 
    if(function_exists('generateTwitterCard')) {
        echo generateTwitterCard($og_data);
    } else {
        ?>
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="<?php echo $canonical_url; ?>">
        <meta name="twitter:title" content="<?php echo htmlspecialchars($site_title); ?> | Balık Tarifleri">
        <meta name="twitter:description" content="<?php echo htmlspecialchars($meta_description); ?>">
        <meta name="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/twitter-home.jpg">
        <meta name="twitter:site" content="@yemektebalikvar">
        <?php
    }
    ?>
    
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/favicon.ico">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="<?php echo UPLOAD_URL; ?>">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/home.css">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo SITE_URL; ?>/assets/css/style.css" as="style">
    <link rel="preload" href="<?php echo SITE_URL; ?>/assets/js/main.js" as="script">
    
    <!-- Structured Data - Organization -->
    <?php
    if(function_exists('generateOrganizationSchema')) {
        echo '<script type="application/ld+json">' . "\n";
        echo generateOrganizationSchema();
        echo "\n" . '</script>' . "\n";
    } else {
    ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?php echo htmlspecialchars($site_title); ?>",
      "url": "<?php echo SITE_URL; ?>",
      "logo": {
        "@type": "ImageObject",
        "url": "<?php echo SITE_URL; ?>/assets/images/logo.png",
        "width": 250,
        "height": 60
      },
      "description": "<?php echo htmlspecialchars($site_description); ?>",
      "foundingDate": "2024",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+90-XXX-XXX-XXXX",
        "contactType": "customer service",
        "availableLanguage": ["Turkish"],
        "areaServed": "TR"
      },
      "sameAs": [
        "https://www.instagram.com/yemektebalikvar",
        "https://www.youtube.com/@yemektebalikvar",
        "https://www.facebook.com/yemektebalikvar",
        "https://twitter.com/yemektebalikvar"
      ],
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo SITE_URL; ?>/pages/tarifler.php?ara={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    <?php } ?>
    
    <!-- Structured Data - WebSite -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "<?php echo htmlspecialchars($site_title); ?>",
      "url": "<?php echo SITE_URL; ?>",
      "description": "<?php echo htmlspecialchars($site_description); ?>",
      "inLanguage": "tr-TR",
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "<?php echo SITE_URL; ?>/pages/tarifler.php?ara={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      },
      "publisher": {
        "@type": "Organization",
        "name": "<?php echo htmlspecialchars($site_title); ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?php echo SITE_URL; ?>/assets/images/logo.png"
        }
      }
    }
    </script>
    
    <?php if(!empty($featured_recipes)): ?>
    <!-- Structured Data - ItemList (Featured Recipes) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": "Öne Çıkan Tarifler",
      "description": "En popüler balık ve deniz ürünleri tarifleri",
      "itemListElement": [
        <?php foreach($featured_recipes as $index => $recipe): ?>
        {
          "@type": "ListItem",
          "position": <?php echo $index + 1; ?>,
          "item": {
            "@type": "Recipe",
            "name": "<?php echo htmlspecialchars($recipe['title']); ?>",
            "url": "<?php echo SITE_URL; ?>/pages/tarif-detay.php?slug=<?php echo $recipe['slug']; ?>",
            "image": "<?php echo $recipe['image'] ? UPLOAD_URL . 'recipes/' . $recipe['image'] : SITE_URL . '/assets/images/no-image.jpg'; ?>",
            "description": "<?php echo htmlspecialchars($recipe['description']); ?>"
          }
        }<?php if($index < count($featured_recipes) - 1): ?>,<?php endif; ?>
        <?php endforeach; ?>
      ]
    }
    </script>
    <?php endif; ?>
    
    <script src="https://t.contentsquare.net/uxa/e5426bd928499.js"></script>
    
    <style>
/* ==================== DESKTOP OPTIMIZATION STYLES ==================== */

:root {
    --primary: #1a3b6d;
    --white: #ffffff;
    --light-gray: #f8f9fa;
    --border: #e9ecef;
    --shadow: rgba(26, 59, 109, 0.1);
    --gold: #ffd700;
}

/* Dark Mode Variables */
body.dark-mode {
    background: #1a1a1a;
}

body.dark-mode .featured-section,
body.dark-mode .categories-section,
body.dark-mode .recent-section,
body.dark-mode .blog-section {
    background: #1a1a1a;
}

body.dark-mode .social-banner {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

body.dark-mode .featured-card,
body.dark-mode .category-card-modern,
body.dark-mode .recent-card,
body.dark-mode .blog-card {
    background: #2d2d2d;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

body.dark-mode .featured-card-title,
body.dark-mode .category-card-modern h3,
body.dark-mode .recipe-content h3,
body.dark-mode .blog-content h3,
body.dark-mode .section-title {
    color: #e0e0e0;
}

body.dark-mode .featured-card-description,
body.dark-mode .category-card-modern p,
body.dark-mode .blog-content p {
    color: #b0b0b0;
}

body.dark-mode .featured-card-meta,
body.dark-mode .recipe-meta,
body.dark-mode .blog-meta {
    color: #b0b0b0;
}

body.dark-mode .search-box {
    background: #2d2d2d;
    border-color: #404040;
}

body.dark-mode .search-box input {
    color: #e0e0e0;
}

body.dark-mode .search-box input::placeholder {
    color: #888;
}

body.dark-mode .btn-load-more {
    background: #2d5a9e;
}

body.dark-mode .no-results-modern {
    background: #2d2d2d;
}

body.dark-mode .no-results-modern h2,
body.dark-mode .no-results-modern h3 {
    color: #e0e0e0;
}

body.dark-mode .no-results-modern p {
    color: #b0b0b0;
}

/* ==================== MOBİL HAMBURGER MENU ==================== */

/* Hamburger menu navbar.php'de halledildi */

/* ==================== SOSYAL MEDYA BANNERLARI - KOMPAKT ==================== */

.social-banners-wrapper {
    padding: 1rem 1rem 0.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.social-banner {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 1rem;
    padding: 0.75rem 1.25rem;
    margin-bottom: 0.75rem;
    border-radius: 12px;
    text-decoration: none;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.social-banner:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.youtube-banner {
    background: #FF0000;
}

.instagram-banner {
    background: linear-gradient(135deg, #feda75 0%, #fa7e1e 25%, #d62976 50%, #962fbf 75%, #4f5bd5 100%);
}

.banner-icon {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.banner-icon svg {
    width: 100%;
    height: 100%;
    fill: white;
}

.instagram-banner .banner-icon {
    background: white;
    border-radius: 12px;
    padding: 6px;
}

.instagram-banner .banner-icon svg {
    width: 38px;
    height: 38px;
}

.banner-content {
    flex: 1;
}

.banner-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    color: white;
    line-height: 1.3;
}

/* ==================== KATEGORİLER - TEK SATIRDA ==================== */

.categories-section {
    padding: 1rem 0;
    background: var(--light-gray);
}

.categories-showcase {
    display: flex;
    flex-wrap: nowrap;
    gap: 1rem;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 1rem 0;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: var(--primary) var(--light-gray);
}

.categories-showcase::-webkit-scrollbar {
    height: 8px;
}

.categories-showcase::-webkit-scrollbar-track {
    background: var(--light-gray);
    border-radius: 10px;
}

.categories-showcase::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.categories-showcase::-webkit-scrollbar-thumb:hover {
    background: #2d5a9e;
}

.category-card-modern {
    min-width: 200px;
    max-width: 200px;
    flex-shrink: 0;
    background: white;
    padding: 1.25rem;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px var(--shadow);
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.category-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(26, 59, 109, 0.15);
}

.category-card-icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
}

.category-card-modern h3 {
    font-size: 1rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.category-card-modern p {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ==================== MOBILE OPTIMIZATION STYLES ==================== */

@media (max-width: 768px) {
    /* Sosyal Medya Bannerları - Daha Kompakt */
    .social-banners-wrapper {
        padding: 0.75rem 0.5rem;
    }
    
    .social-banner {
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 10px;
    }
    
    .banner-icon {
        width: 40px;
        height: 40px;
    }
    
    .instagram-banner .banner-icon {
        width: 40px;
        height: 40px;
        padding: 5px;
        border-radius: 10px;
    }
    
    .instagram-banner .banner-icon svg {
        width: 30px;
        height: 30px;
    }
    
    .banner-title {
        font-size: 0.85rem;
    }
    
    /* Featured Grid - 2 Columns on Mobile */
    .featured-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .featured-card {
        border-radius: 12px;
    }
    
    .featured-card-image {
        height: 140px;
        border-radius: 12px 12px 0 0;
    }
    
    .featured-card-image img {
        border-radius: 12px 12px 0 0;
    }
    
    .featured-badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        top: 0.5rem;
        right: 0.5rem;
    }
    
    .featured-card-content {
        padding: 0.75rem;
    }
    
    .featured-card-category {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        margin-bottom: 0.4rem;
    }
    
    .featured-card-title {
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .featured-card-description {
        display: none;
    }
    
    .featured-card-meta {
        gap: 0.5rem;
        font-size: 0.7rem;
        margin-top: 0.5rem;
    }
    
    /* Kategoriler - Minimal ve Kompakt */
    .categories-section {
        padding: 1rem 0;
    }
    
    .categories-showcase {
        gap: 0.5rem;
        padding: 0.5rem 0;
    }
    
    .category-card-modern {
        min-width: 120px;
        max-width: 120px;
        padding: 0.75rem;
        border-radius: 10px;
    }
    
    .category-card-icon {
        font-size: 1.25rem;
        margin-bottom: 0.4rem;
    }
    
    .category-card-modern h3 {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }
    
    .category-card-modern p {
        font-size: 0.7rem;
        display: none; /* Mobilde açıklamayı gizle */
    }
    
    /* Tarifler Grid - Mobil */
    .recent-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .recent-card {
        border-radius: 12px;
    }
    
    .recipe-image {
        height: 140px;
        border-radius: 12px 12px 0 0;
    }
    
    .recipe-image img {
        border-radius: 12px 12px 0 0;
    }
    
    .recipe-content {
        padding: 0.75rem;
    }
    
    .recipe-category {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
        margin-bottom: 0.4rem;
    }
    
    .recipe-content h3 {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .recipe-meta {
        gap: 0.5rem;
        font-size: 0.7rem;
    }
    
    /* Section Spacing - Minimal */
    .featured-section,
    .recent-section,
    .blog-section {
        padding: 1.25rem 0;
    }
    
    .categories-section {
        padding: 1rem 0;
    }
    
    .section-title {
        font-size: 1.3rem;
        margin-bottom: 0.75rem;
        padding: 0 0.5rem;
    }
    
    .container {
        padding: 0 0.5rem;
    }
    
    /* Search Section - Kompakt */
    .search-section {
        padding: 0 0.5rem 0.75rem;
    }
    
    .search-box {
        height: 42px;
        border-radius: 21px;
        padding: 0.25rem 0.5rem;
    }
    
    .search-box input {
        font-size: 0.85rem;
        padding: 0 0.75rem;
    }
    
    .search-box button {
        width: 38px;
        height: 38px;
        font-size: 1rem;
        border-radius: 50%;
    }
    
    /* Blog Cards - Mobil */
    .blog-showcase {
        gap: 1rem;
    }
    
    .blog-card {
        border-radius: 12px;
    }
    
    .blog-image {
        height: 160px;
        border-radius: 12px 12px 0 0;
    }
    
    .blog-content {
        padding: 1rem;
    }
    
    .blog-category {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        margin-bottom: 0.5rem;
    }
    
    .blog-content h3 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .blog-content p {
        font-size: 0.85rem;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-meta {
        font-size: 0.75rem;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    /* Load More Button */
    .btn-load-more {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
    }
}

/* Extra Small Phones */
@media (max-width: 480px) {
    .social-banners-wrapper {
        padding: 0.5rem 0.5rem 0.25rem;
    }
    
    .social-banner {
        padding: 0.65rem 0.85rem;
        gap: 0.65rem;
    }
    
    .banner-icon {
        width: 36px;
        height: 36px;
    }
    
    .instagram-banner .banner-icon {
        width: 36px;
        height: 36px;
    }
    
    .instagram-banner .banner-icon svg {
        width: 26px;
        height: 26px;
    }
    
    .banner-title {
        font-size: 0.8rem;
    }
    
    .featured-section,
    .categories-section,
    .recent-section,
    .blog-section {
        padding: 0.75rem 0 1rem;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
    
    .featured-card-image,
    .recipe-image {
        height: 110px;
    }
    
    .featured-card-title,
    .recipe-content h3 {
        font-size: 0.8rem;
    }
    
    .featured-card-meta,
    .recipe-meta {
        font-size: 0.65rem;
    }
    
    .category-card-modern {
        min-width: 100px;
        max-width: 100px;
        padding: 0.6rem;
    }
    
    .category-card-icon {
        font-size: 1.1rem;
        margin-bottom: 0.3rem;
    }
    
    .category-card-modern h3 {
        font-size: 0.75rem;
    }
    
    .search-box {
        height: 38px;
    }
    
    .search-box input {
        font-size: 0.8rem;
    }
    
    .search-box button {
        width: 34px;
        height: 34px;
        font-size: 0.9rem;
    }
}

/* Desktop'ta Normal Görünüm */
@media (min-width: 769px) {
    .featured-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
    
    .recent-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
}

/* Tablet Landscape */
@media (min-width: 769px) and (max-width: 1024px) {
    .featured-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .recent-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .categories-showcase {
        gap: 0.75rem;
    }
    
    .category-card-modern {
        min-width: 180px;
        max-width: 180px;
    }
}

/* Floating Social - Gizle */
.social-floating {
    display: none !important;
}
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <main>
        <!-- Sosyal Medya Bannerları -->
        <div class="social-banners-wrapper">
            <!-- YouTube Banner -->
            <a href="https://www.youtube.com/@yemektebalikvar" 
               class="social-banner youtube-banner"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="YouTube Kanalımızı Takip Edin">
                <div class="banner-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="white" d="M10 8.64L15.27 12 10 15.36V8.64M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                </div>
                <div class="banner-content">
                    <h2 class="banner-title">YouTube Kanalımızı Takip Edin !</h2>
                </div>
            </a>
            
            <!-- Instagram Banner -->
            <a href="https://www.instagram.com/yemektebalikvar" 
               class="social-banner instagram-banner"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Instagram Kanalımızı Takip Edin">
                <div class="banner-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="instagram-gradient-icon" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#feda75" />
                                <stop offset="25%" style="stop-color:#fa7e1e" />
                                <stop offset="50%" style="stop-color:#d62976" />
                                <stop offset="75%" style="stop-color:#962fbf" />
                                <stop offset="100%" style="stop-color:#4f5bd5" />
                            </linearGradient>
                        </defs>
                        <path fill="url(#instagram-gradient-icon)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </div>
                <div class="banner-content">
                    <h2 class="banner-title">Instagram Sayfamızı Takip Edin !</h2>
                </div>
            </a>
        </div>
        
        <!-- Öne Çıkan Tarifler -->
        <section class="featured-section">
            <div class="container">
                <h1 class="section-title">Öne Çıkan Tarifler</h1>
                
                <?php if(!empty($featured_recipes)): ?>
                <div class="featured-grid">
                    <?php foreach($featured_recipes as $recipe): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/tarif-detay.php?slug=<?php echo $recipe['slug']; ?>" 
                       class="featured-card"
                       itemscope 
                       itemtype="https://schema.org/Recipe">
                        <div class="featured-card-image">
                            <?php if(isset($recipe['image']) && $recipe['image']): ?>
                                <img src="<?php echo UPLOAD_URL . 'recipes/' . $recipe['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($recipe['title']); ?> Tarifi" 
                                     width="400"
                                     height="300"
                                     loading="lazy"
                                     decoding="async"
                                     itemprop="image">
                            <?php else: ?>
                                <div class="no-image">🍳</div>
                            <?php endif; ?>
                            <span class="featured-badge">⭐ Öne Çıkan</span>
                        </div>
                        <div class="featured-card-content">
                            <span class="featured-card-category" itemprop="recipeCategory">
                                <?php echo htmlspecialchars($recipe['category_name'] ?? 'Genel'); ?>
                            </span>
                            <h2 class="featured-card-title" itemprop="name">
                                <?php echo htmlspecialchars($recipe['title']); ?>
                            </h2>
                            <?php if(isset($recipe['description']) && $recipe['description']): ?>
                            <p class="featured-card-description" itemprop="description">
                                <?php echo htmlspecialchars($recipe['description']); ?>
                            </p>
                            <?php endif; ?>
                            <div class="featured-card-meta">
                                <span class="featured-meta-item">
                                    ⏱️ <time itemprop="totalTime" datetime="PT<?php echo ($recipe['prep_time'] + $recipe['cook_time']); ?>M"><?php echo ($recipe['prep_time'] + $recipe['cook_time']); ?> dk</time>
                                </span>
                                <span class="featured-meta-item">
                                    👨‍👩‍👧‍👦 <span itemprop="recipeYield"><?php echo $recipe['servings']; ?> kişi</span>
                                </span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="no-results-modern">
                    <h2>🍽️ Henüz Öne Çıkan Tarif Yok</h2>
                    <p>Admin panelinden tarifleri öne çıkarabilirsiniz.</p>
                    <a href="<?php echo SITE_URL; ?>/admin/login.php" class="btn">Admin Paneli</a>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Kategoriler -->
        <section class="categories-section">
            <div class="container">
                <h2 class="section-title">Kategoriler</h2>
                
                <?php if(!empty($categories)): ?>
                <div class="categories-showcase">
                    <?php foreach($categories as $category): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/tarifler.php?kategori=<?php echo $category['slug']; ?>" 
                       class="category-card-modern"
                       itemscope 
                       itemtype="https://schema.org/Thing">
                        <div class="category-card-icon">🍽️</div>
                        <h3 itemprop="name"><?php echo htmlspecialchars($category['name']); ?></h3>
                        <?php if(isset($category['description']) && $category['description']): ?>
                        <p itemprop="description"><?php echo htmlspecialchars($category['description']); ?></p>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="no-results-modern">
                    <h3>📂 Henüz Kategori Yok</h3>
                    <p>Admin panelinden kategori ekleyebilirsiniz.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Son Eklenen Tarifler -->
        <section class="recent-section" id="recipes-section">
            <div class="container">
                <h2 class="section-title">Tariflerimiz</h2>
                
                <!-- Banner Reklam -->
                <div class="banner-ad-container" style="text-align: center; margin: 1.5rem auto; max-width: 100%; padding: 0;">
                    <a href="https://balik.market" target="_blank" rel="noopener noreferrer" aria-label="Balık Market">
                        <img src="<?php echo SITE_URL; ?>/assets/balik.market-banner-reklam.gif" 
                             alt="Balık Market - Taze Balık ve Deniz Ürünleri" 
                             style="width: 100%; max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: block; margin: 0 auto;"
                             loading="lazy">
                    </a>
                </div>
                
                <!-- Tarif Arama -->
                <div class="search-section">
                    <div class="search-container">
                        <form class="search-box" 
                              id="recipeSearchForm" 
                              method="GET" 
                              action="<?php echo SITE_URL; ?>/pages/tarifler.php"
                              role="search"
                              aria-label="Tarif Arama">
                            <input type="search" 
                                   name="q" 
                                   id="searchInput" 
                                   placeholder="Tarif ara... (örn: tavuk, makarna, çorba)" 
                                   autocomplete="off"
                                   aria-label="Tarif arama">
                            <button type="submit" aria-label="Ara">
                                🔍
                            </button>
                        </form>
                    </div>
                </div>
                
                <?php if(!empty($recent_recipes)): ?>
                <div class="recent-grid" id="recipesGrid">
                    <?php foreach($recipesToShow as $recipe): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/tarif-detay.php?slug=<?php echo $recipe['slug']; ?>" 
                       class="recent-card animated"
                       itemscope 
                       itemtype="https://schema.org/Recipe">
                        <div class="recipe-image">
                            <?php if(isset($recipe['image']) && $recipe['image']): ?>
                                <img src="<?php echo UPLOAD_URL . 'recipes/' . $recipe['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($recipe['title']); ?> Tarifi" 
                                     width="350"
                                     height="250"
                                     loading="lazy"
                                     decoding="async"
                                     itemprop="image">
                            <?php else: ?>
                                <div class="no-image">🍳</div>
                            <?php endif; ?>
                        </div>
                        <div class="recipe-content">
                            <span class="recipe-category" itemprop="recipeCategory">
                                <?php echo htmlspecialchars($recipe['category_name'] ?? 'Genel'); ?>
                            </span>
                            <h3 itemprop="name"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <div class="recipe-meta">
                                <span>⏱️ <time itemprop="totalTime" datetime="PT<?php echo ($recipe['prep_time'] + $recipe['cook_time']); ?>M"><?php echo ($recipe['prep_time'] + $recipe['cook_time']); ?> dk</time></span>
                                <span>👨‍👩‍👧‍👦 <span itemprop="recipeYield"><?php echo $recipe['servings']; ?> kişi</span></span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <?php if($totalRecipes > $displayCount): ?>
                <div class="load-more-section">
                    <button class="btn-load-more" 
                            id="loadMoreBtn" 
                            data-offset="<?php echo $displayCount; ?>"
                            data-total="<?php echo $totalRecipes; ?>"
                            aria-label="Daha fazla tarif göster">
                        Daha Fazla Göster
                        <span class="icon">↓</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="no-results-modern">
                    <h3>🍽️ Henüz Tarif Yok</h3>
                    <p>İlk tarifi eklemek için admin paneline gidin.</p>
                    <a href="<?php echo SITE_URL; ?>/admin/login.php" class="btn">Tarif Ekle</a>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Blog Yazıları -->
        <section class="blog-section">
            <div class="container">
                <h2 class="section-title">Son Blog Yazıları</h2>
                
                <?php if(!empty($recent_posts)): ?>
                <div class="blog-showcase">
                    <?php foreach($recent_posts as $post): ?>
                    <article class="blog-card" 
                             itemscope 
                             itemtype="https://schema.org/BlogPosting">
                        <?php if(isset($post['image']) && $post['image']): ?>
                        <div class="blog-image">
                            <img src="<?php echo UPLOAD_URL . 'blog/' . $post['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                 width="400"
                                 height="250"
                                 loading="lazy"
                                 decoding="async"
                                 itemprop="image">
                        </div>
                        <?php endif; ?>
                        <div class="blog-content">
                            <span class="blog-category" itemprop="articleSection">
                                <?php echo htmlspecialchars($post['category_name'] ?? 'Genel'); ?>
                            </span>
                            <h3 itemprop="headline"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p itemprop="description"><?php echo htmlspecialchars($post['excerpt'] ?: (substr($post['content'] ?? '', 0, 150) . '...')); ?></p>
                            <div class="blog-meta">
                                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                                    ✏️ <span itemprop="name"><?php echo htmlspecialchars($post['author'] ?? 'Admin'); ?></span>
                                </span>
                                <span>
                                    📅 <time itemprop="datePublished" datetime="<?php echo date('c', strtotime($post['created_at'])); ?>"><?php echo date('d.m.Y', strtotime($post['created_at'])); ?></time>
                                </span>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/pages/blog-detay.php?slug=<?php echo $post['slug']; ?>" 
                               class="btn"
                               itemprop="url">Devamını Oku</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="no-results-modern">
                    <h3>📝 Henüz Blog Yazısı Yok</h3>
                    <p>İlk blog yazısını eklemek için admin paneline gidin.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script>
        // UPLOAD_URL'yi JavaScript'e aktar
        window.UPLOAD_URL = '<?php echo UPLOAD_URL; ?>';
        
        // Tüm tarifleri JavaScript'e aktar (gizli olarak)
        window.allRecipes = <?php echo json_encode($recent_recipes); ?>;
    </script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js" defer></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/home.js" defer></script>
    <!-- Sosyal Medya Tıklama Tracking -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // YouTube banner tracking
    const youtubeButton = document.querySelector('.youtube-banner');
    if (youtubeButton) {
        youtubeButton.addEventListener('click', function(e) {
            trackSocialClick('youtube');
        });
    }
    
    // Instagram banner tracking
    const instagramButton = document.querySelector('.instagram-banner');
    if (instagramButton) {
        instagramButton.addEventListener('click', function(e) {
            trackSocialClick('instagram');
        });
    }
    
    // Tracking fonksiyonu
    function trackSocialClick(platform) {
        fetch('<?php echo SITE_URL; ?>/track_social.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                click_type: 'social_media_' + platform,
                page_url: window.location.href
            })
        }).catch(err => console.log('Tracking error:', err));
    }
});
</script>
</body>
</html>