<?php
/**
 * seo-check.php
 * Root dizine kaydet
 * https://yemektebalikvar.com/seo-check.php
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

// SEO dosyalarını kontrol et
$seo_files = [
    'includes/seo.php' => 'Ana SEO Motoru',
    'includes/seo-helpers.php' => 'SEO Yardımcılar'
];

// Sayfaları kontrol et
$pages_to_check = [
    'pages/tarif-detay.php' => [
        'generateRecipeSchema',
        'generateOGTags',
        'generateBreadcrumbSchema'
    ],
    'pages/blog-detay.php' => [
        'generateArticleSchema',
        'generateOGTags',
        'generateTwitterCard'
    ],
    'index.php' => [
        'generateOGTags',
        'Schema'
    ]
];

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Kontrol Paneli - <?php echo SITE_TITLE; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        h2 {
            color: #1a3b6d;
            margin-bottom: 20px;
            font-size: 1.8rem;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .check-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .check-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        
        .check-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        
        .check-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        
        .check-item.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        
        .status {
            font-size: 2rem;
            margin-right: 15px;
        }
        
        .details {
            flex: 1;
        }
        
        .details strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        
        .details small {
            color: #666;
            font-size: 0.9rem;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: auto;
        }
        
        .badge.success {
            background: #28a745;
            color: white;
        }
        
        .badge.error {
            background: #dc3545;
            color: white;
        }
        
        .badge.warning {
            background: #ffc107;
            color: #333;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .summary-card .number {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 10px;
        }
        
        .summary-card .number.green { color: #28a745; }
        .summary-card .number.red { color: #dc3545; }
        .summary-card .number.yellow { color: #ffc107; }
        
        .summary-card .label {
            color: #666;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .code-block {
            background: #2d3748;
            color: #a0aec0;
            padding: 20px;
            border-radius: 12px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.95rem;
        }
        
        .code-block .comment {
            color: #68d391;
        }
        
        .test-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .test-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .test-link:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }
            
            .section {
                padding: 20px;
            }
            
            .check-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .badge {
                margin-top: 10px;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 SEO Kontrol Paneli</h1>
        
        <!-- Summary -->
        <?php
        $total_checks = 0;
        $passed = 0;
        $failed = 0;
        $warnings = 0;
        ?>
        
        <!-- 1. SEO Dosyaları Kontrolü -->
        <div class="section">
            <h2>📁 SEO Dosyaları</h2>
            
            <?php foreach($seo_files as $file => $name): ?>
                <?php
                $exists = file_exists($file);
                $total_checks++;
                if($exists) $passed++; else $failed++;
                ?>
                <div class="check-item <?php echo $exists ? 'success' : 'error'; ?>">
                    <span class="status"><?php echo $exists ? '✅' : '❌'; ?></span>
                    <div class="details">
                        <strong><?php echo $name; ?></strong>
                        <small><?php echo $file; ?></small>
                    </div>
                    <span class="badge <?php echo $exists ? 'success' : 'error'; ?>">
                        <?php echo $exists ? 'Mevcut' : 'Eksik'; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- 2. Fonksiyon Kullanım Kontrolü -->
        <div class="section">
            <h2>🔧 Sayfalarda SEO Fonksiyonları</h2>
            
            <?php foreach($pages_to_check as $page => $functions): ?>
                <h3 style="color: #667eea; margin: 20px 0 10px 0;"><?php echo basename($page); ?></h3>
                
                <?php
                $page_content = file_exists($page) ? file_get_contents($page) : '';
                
                foreach($functions as $func):
                    $total_checks++;
                    $found = !empty($page_content) && strpos($page_content, $func) !== false;
                    
                    if($found) $passed++;
                    else {
                        if(file_exists($page)) $warnings++;
                        else $failed++;
                    }
                ?>
                    <div class="check-item <?php echo $found ? 'success' : (file_exists($page) ? 'warning' : 'error'); ?>">
                        <span class="status"><?php echo $found ? '✅' : (file_exists($page) ? '⚠️' : '❌'); ?></span>
                        <div class="details">
                            <strong><?php echo $func; ?>()</strong>
                            <small>
                                <?php if($found): ?>
                                    Fonksiyon kullanılıyor ✓
                                <?php elseif(file_exists($page)): ?>
                                    Sayfa var ama fonksiyon kullanılmıyor
                                <?php else: ?>
                                    Sayfa bulunamadı
                                <?php endif; ?>
                            </small>
                        </div>
                        <span class="badge <?php echo $found ? 'success' : (file_exists($page) ? 'warning' : 'error'); ?>">
                            <?php echo $found ? 'Aktif' : (file_exists($page) ? 'Pasif' : 'N/A'); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        
        <!-- 3. functions.php Kontrolü -->
        <div class="section">
            <h2>⚙️ functions.php - SEO Entegrasyonu</h2>
            
            <?php
            $functions_php = 'includes/functions.php';
            $functions_content = file_exists($functions_php) ? file_get_contents($functions_php) : '';
            
            $checks = [
                "require_once 'seo.php'" => 'seo.php dahil edilmiş mi?',
                "require_once 'seo-helpers.php'" => 'seo-helpers.php dahil edilmiş mi?'
            ];
            
            foreach($checks as $check => $desc):
                $total_checks++;
                $found = strpos($functions_content, $check) !== false;
                if($found) $passed++; else $warnings++;
            ?>
                <div class="check-item <?php echo $found ? 'success' : 'warning'; ?>">
                    <span class="status"><?php echo $found ? '✅' : '⚠️'; ?></span>
                    <div class="details">
                        <strong><?php echo $desc; ?></strong>
                        <small><code><?php echo htmlspecialchars($check); ?></code></small>
                    </div>
                    <span class="badge <?php echo $found ? 'success' : 'warning'; ?>">
                        <?php echo $found ? 'OK' : 'Eksik'; ?>
                    </span>
                </div>
            <?php endforeach; ?>
            
            <?php if($warnings > 0): ?>
            <div style="background: #fff3cd; padding: 20px; border-radius: 12px; margin-top: 20px; border-left: 4px solid #ffc107;">
                <h4 style="color: #856404; margin-bottom: 10px;">🔧 Nasıl Düzeltilir?</h4>
                <p style="color: #856404; margin-bottom: 15px;">functions.php dosyasının başına şu satırları ekle:</p>
                <div class="code-block">
<span class="comment">// SEO fonksiyonlarını yükle</span>
require_once 'seo.php';
require_once 'seo-helpers.php';
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- 4. Test Araçları -->
        <div class="section">
            <h2>🧪 Google Test Araçları</h2>
            
            <p style="margin-bottom: 20px; color: #666;">
                SEO fonksiyonları çalışıyor mu Google'da test et:
            </p>
            
            <div class="test-links">
                <a href="https://search.google.com/test/rich-results?url=<?php echo urlencode(SITE_URL); ?>" 
                   target="_blank" 
                   class="test-link">
                    📊 Rich Results Test
                </a>
                
                <a href="https://validator.schema.org/#url=<?php echo urlencode(SITE_URL); ?>" 
                   target="_blank" 
                   class="test-link">
                    ✅ Schema Validator
                </a>
                
                <a href="https://developers.facebook.com/tools/debug/?q=<?php echo urlencode(SITE_URL); ?>" 
                   target="_blank" 
                   class="test-link">
                    📱 Facebook Debugger
                </a>
                
                <a href="https://cards-dev.twitter.com/validator?url=<?php echo urlencode(SITE_URL); ?>" 
                   target="_blank" 
                   class="test-link">
                    🐦 Twitter Card Validator
                </a>
                
                <a href="https://pagespeed.web.dev/analysis?url=<?php echo urlencode(SITE_URL); ?>" 
                   target="_blank" 
                   class="test-link">
                    ⚡ PageSpeed Insights
                </a>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="summary">
            <div class="summary-card">
                <div class="number green"><?php echo $passed; ?></div>
                <div class="label">✅ Başarılı</div>
            </div>
            
            <div class="summary-card">
                <div class="number yellow"><?php echo $warnings; ?></div>
                <div class="label">⚠️ Uyarı</div>
            </div>
            
            <div class="summary-card">
                <div class="number red"><?php echo $failed; ?></div>
                <div class="label">❌ Hata</div>
            </div>
            
            <div class="summary-card">
                <div class="number" style="color: #667eea;">
                    <?php echo $total_checks > 0 ? round(($passed / $total_checks) * 100) : 0; ?>%
                </div>
                <div class="label">SEO Puanı</div>
            </div>
        </div>
        
        <!-- Genel Durum -->
        <div class="section" style="text-align: center;">
            <?php if($failed == 0 && $warnings == 0): ?>
                <h2 style="color: #28a745;">🎉 Mükemmel!</h2>
                <p style="font-size: 1.2rem; color: #666;">Tüm SEO kontrollerinden geçtiniz. Siteniz Google'a hazır!</p>
            <?php elseif($failed > 0): ?>
                <h2 style="color: #dc3545;">⚠️ Acil Düzeltme Gerekli</h2>
                <p style="font-size: 1.2rem; color: #666;"><?php echo $failed; ?> kritik hata var. Yukarıdaki çözümleri uygula.</p>
            <?php else: ?>
                <h2 style="color: #ffc107;">🔧 İyileştirme Önerileri</h2>
                <p style="font-size: 1.2rem; color: #666;"><?php echo $warnings; ?> uyarı var. SEO'yu daha da güçlendirebiliriz!</p>
            <?php endif; ?>
        </div>
        
    </div>
</body>
</html>