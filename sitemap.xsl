<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/1999/xhtml"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>XML Sitemap - Yemekte Balık Var</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <meta name="robots" content="noindex, follow"/>
                <style type="text/css">
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Helvetica Neue', sans-serif;
                        background: linear-gradient(135deg, #1a3b6d 0%, #0f2545 100%);
                        padding: 20px;
                        min-height: 100vh;
                        line-height: 1.6;
                    }
                    
                    .container {
                        max-width: 1400px;
                        margin: 0 auto;
                        background: white;
                        border-radius: 20px;
                        box-shadow: 0 25px 80px rgba(0,0,0,0.4);
                        overflow: hidden;
                    }
                    
                    .header {
                        background: linear-gradient(135deg, #1a3b6d 0%, #0f2545 100%);
                        color: white;
                        padding: 50px 40px;
                        text-align: center;
                        position: relative;
                        overflow: hidden;
                    }
                    
                    .header::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,165.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center no-repeat;
                        background-size: cover;
                    }
                    
                    .header-content {
                        position: relative;
                        z-index: 1;
                    }
                    
                    .header h1 {
                        font-size: 3em;
                        margin-bottom: 15px;
                        font-weight: 800;
                        letter-spacing: -1px;
                    }
                    
                    .header .logo {
                        font-size: 4em;
                        margin-bottom: 10px;
                        display: block;
                    }
                    
                    .header p {
                        font-size: 1.2em;
                        opacity: 0.95;
                        font-weight: 300;
                    }
                    
                    .info-box {
                        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                        border-left: 5px solid #1a3b6d;
                        padding: 30px 40px;
                        margin: 40px;
                        border-radius: 12px;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    }
                    
                    .info-box h3 {
                        color: #1a3b6d;
                        margin-bottom: 15px;
                        font-size: 1.5em;
                        font-weight: 700;
                    }
                    
                    .info-box p {
                        color: #0f2545;
                        line-height: 1.8;
                        font-size: 1.05em;
                    }
                    
                    .info-box ul {
                        list-style: none;
                        margin-top: 20px;
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                        gap: 15px;
                    }
                    
                    .info-box li {
                        padding: 12px 0;
                        color: #0f2545;
                        font-weight: 500;
                    }
                    
                    .info-box li:before {
                        content: "🐟";
                        margin-right: 10px;
                        font-size: 1.2em;
                    }
                    
                    .stats {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                        gap: 25px;
                        padding: 40px;
                        background: #f8fafc;
                    }
                    
                    .stat-card {
                        background: white;
                        padding: 30px 25px;
                        border-radius: 16px;
                        text-align: center;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
                        transition: all 0.3s ease;
                        border: 2px solid transparent;
                    }
                    
                    .stat-card:hover {
                        transform: translateY(-8px);
                        box-shadow: 0 8px 30px rgba(26, 59, 109, 0.2);
                        border-color: #1a3b6d;
                    }
                    
                    .stat-icon {
                        font-size: 2.5em;
                        margin-bottom: 15px;
                        display: block;
                    }
                    
                    .stat-number {
                        font-size: 3em;
                        font-weight: 800;
                        color: #1a3b6d;
                        display: block;
                        line-height: 1;
                    }
                    
                    .stat-label {
                        color: #64748b;
                        font-size: 0.95em;
                        margin-top: 12px;
                        text-transform: uppercase;
                        letter-spacing: 1.5px;
                        font-weight: 600;
                    }
                    
                    .url-list {
                        padding: 40px;
                    }
                    
                    .url-list h2 {
                        color: #1a3b6d;
                        margin-bottom: 30px;
                        font-size: 2em;
                        font-weight: 800;
                        display: flex;
                        align-items: center;
                        gap: 15px;
                    }
                    
                    .url-list h2::before {
                        content: "📑";
                        font-size: 1.2em;
                    }
                    
                    table {
                        width: 100%;
                        border-collapse: separate;
                        border-spacing: 0;
                        background: white;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                    }
                    
                    thead {
                        background: linear-gradient(135deg, #1a3b6d 0%, #0f2545 100%);
                        color: white;
                    }
                    
                    th {
                        padding: 18px 20px;
                        text-align: left;
                        font-weight: 700;
                        font-size: 0.95em;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                    }
                    
                    td {
                        padding: 18px 20px;
                        border-bottom: 1px solid #e2e8f0;
                    }
                    
                    tbody tr {
                        transition: all 0.2s ease;
                    }
                    
                    tbody tr:hover {
                        background: #f8fafc;
                        transform: scale(1.01);
                    }
                    
                    tbody tr:last-child td {
                        border-bottom: none;
                    }
                    
                    .url-link {
                        color: #1a3b6d;
                        text-decoration: none;
                        word-break: break-all;
                        font-weight: 600;
                        transition: color 0.3s ease;
                    }
                    
                    .url-link:hover {
                        color: #0f2545;
                        text-decoration: underline;
                    }
                    
                    .priority {
                        display: inline-block;
                        padding: 6px 14px;
                        border-radius: 20px;
                        font-size: 0.85em;
                        font-weight: 700;
                        letter-spacing: 0.5px;
                    }
                    
                    .priority-high {
                        background: #d1fae5;
                        color: #065f46;
                        border: 2px solid #10b981;
                    }
                    
                    .priority-medium {
                        background: #fef3c7;
                        color: #92400e;
                        border: 2px solid #f59e0b;
                    }
                    
                    .priority-low {
                        background: #fee2e2;
                        color: #991b1b;
                        border: 2px solid #ef4444;
                    }
                    
                    .changefreq {
                        color: #64748b;
                        font-size: 0.9em;
                        font-weight: 600;
                        text-transform: capitalize;
                    }
                    
                    .lastmod {
                        color: #94a3b8;
                        font-size: 0.85em;
                        font-family: 'Courier New', monospace;
                    }
                    
                    .badge {
                        display: inline-block;
                        padding: 4px 10px;
                        background: linear-gradient(135deg, #1a3b6d 0%, #0f2545 100%);
                        color: white;
                        border-radius: 6px;
                        font-size: 0.75em;
                        margin-left: 10px;
                        font-weight: 600;
                    }
                    
                    .footer {
                        background: linear-gradient(135deg, #1a3b6d 0%, #0f2545 100%);
                        color: white;
                        padding: 30px 40px;
                        text-align: center;
                        font-size: 0.95em;
                    }
                    
                    .footer a {
                        color: #60a5fa;
                        text-decoration: none;
                        font-weight: 600;
                        transition: color 0.3s ease;
                    }
                    
                    .footer a:hover {
                        color: #93c5fd;
                        text-decoration: underline;
                    }
                    
                    .search-box {
                        margin: 30px 40px;
                        padding: 20px;
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
                    }
                    
                    .search-box input {
                        width: 100%;
                        padding: 15px 20px;
                        border: 2px solid #e2e8f0;
                        border-radius: 10px;
                        font-size: 1em;
                        transition: all 0.3s ease;
                    }
                    
                    .search-box input:focus {
                        outline: none;
                        border-color: #1a3b6d;
                        box-shadow: 0 0 0 3px rgba(26, 59, 109, 0.1);
                    }
                    
                    @media (max-width: 768px) {
                        body {
                            padding: 10px;
                        }
                        
                        .header h1 {
                            font-size: 2em;
                        }
                        
                        .header .logo {
                            font-size: 3em;
                        }
                        
                        .header p {
                            font-size: 1em;
                        }
                        
                        .info-box, .url-list, .search-box {
                            margin: 20px;
                            padding: 20px;
                        }
                        
                        .stats {
                            grid-template-columns: 1fr;
                            padding: 20px;
                        }
                        
                        table {
                            font-size: 0.85em;
                        }
                        
                        th, td {
                            padding: 12px 10px;
                        }
                        
                        .url-list h2 {
                            font-size: 1.5em;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <!-- Header -->
                    <div class="header">
                        <div class="header-content">
                            <span class="logo">🐟</span>
                            <h1>XML Sitemap</h1>
                            <p>Yemekte Balık Var - Tüm Sayfalar ve İçerikler</p>
                        </div>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="info-box">
                        <h3>🗺️ Bu Sitemap Ne İşe Yarar?</h3>
                        <p>Bu XML sitemap, arama motorlarının (Google, Bing, Yandex) sitemizi daha verimli taramasını ve tüm içeriklerimizi keşfetmesini sağlar. SEO optimizasyonu için kritik öneme sahiptir.</p>
                        <ul>
                            <li>Tüm balık tarifleri listelenir</li>
                            <li>Blog yazıları ve rehberler</li>
                            <li>Ürünler ve kategoriler</li>
                            <li>Resim ve video bilgileri</li>
                        </ul>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="🔍 Sitemap içinde ara... (URL, tarif, blog vb.)" onkeyup="searchTable()"/>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="stats">
                        <div class="stat-card">
                            <span class="stat-icon">📄</span>
                            <span class="stat-number">
                                <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/>
                            </span>
                            <span class="stat-label">Toplam Sayfa</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-icon">🖼️</span>
                            <span class="stat-number">
                                <xsl:value-of select="count(sitemap:urlset/sitemap:url/image:image)"/>
                            </span>
                            <span class="stat-label">Toplam Resim</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-icon">🎥</span>
                            <span class="stat-number">
                                <xsl:value-of select="count(sitemap:urlset/sitemap:url/video:video)"/>
                            </span>
                            <span class="stat-label">Toplam Video</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-icon">⭐</span>
                            <span class="stat-number">
                                <xsl:value-of select="count(sitemap:urlset/sitemap:url[sitemap:priority &gt;= 0.8])"/>
                            </span>
                            <span class="stat-label">Öncelikli Sayfa</span>
                        </div>
                    </div>
                    
                    <!-- URL List -->
                    <div class="url-list">
                        <h2>Tüm Sayfalar (<xsl:value-of select="count(sitemap:urlset/sitemap:url)"/>)</h2>
                        
                        <table id="urlTable">
                            <thead>
                                <tr>
                                    <th style="width: 55%">URL</th>
                                    <th style="width: 12%">Öncelik</th>
                                    <th style="width: 15%">Güncelleme</th>
                                    <th style="width: 18%">Son Değişiklik</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="sitemap:urlset/sitemap:url">
                                    <tr>
                                        <td>
                                            <a class="url-link" href="{sitemap:loc}" target="_blank">
                                                <xsl:value-of select="sitemap:loc"/>
                                            </a>
                                            <xsl:if test="count(image:image) &gt; 0">
                                                <span class="badge">🖼️ <xsl:value-of select="count(image:image)"/> resim</span>
                                            </xsl:if>
                                            <xsl:if test="count(video:video) &gt; 0">
                                                <span class="badge">🎥 <xsl:value-of select="count(video:video)"/> video</span>
                                            </xsl:if>
                                        </td>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="sitemap:priority &gt;= 0.8">
                                                    <span class="priority priority-high">
                                                        <xsl:value-of select="sitemap:priority"/>
                                                    </span>
                                                </xsl:when>
                                                <xsl:when test="sitemap:priority &gt;= 0.5">
                                                    <span class="priority priority-medium">
                                                        <xsl:value-of select="sitemap:priority"/>
                                                    </span>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <span class="priority priority-low">
                                                        <xsl:value-of select="sitemap:priority"/>
                                                    </span>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </td>
                                        <td class="changefreq">
                                            <xsl:value-of select="sitemap:changefreq"/>
                                        </td>
                                        <td class="lastmod">
                                            <xsl:value-of select="substring(sitemap:lastmod, 0, 11)"/>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Footer -->
                    <div class="footer">
                        <p>✨ Bu sitemap otomatik olarak oluşturulur ve güncellenir</p>
                        <p style="margin-top: 10px;">
                            <a href="https://yemektebalikvar.com">🏠 Ana Sayfaya Dön</a> | 
                            <a href="https://yemektebalikvar.com/pages/tarifler">🍳 Tariflere Git</a>
                        </p>
                    </div>
                </div>
                
                <script>
                    // Arama fonksiyonu
                    function searchTable() {
                        const input = document.getElementById('searchInput');
                        const filter = input.value.toUpperCase();
                        const table = document.getElementById('urlTable');
                        const tr = table.getElementsByTagName('tr');
                        
                        for (let i = 1; i &lt; tr.length; i++) {
                            const td = tr[i].getElementsByTagName('td')[0];
                            if (td) {
                                const txtValue = td.textContent || td.innerText;
                                if (txtValue.toUpperCase().indexOf(filter) &gt; -1) {
                                    tr[i].style.display = '';
                                } else {
                                    tr[i].style.display = 'none';
                                }
                            }
                        }
                    }
                </script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>