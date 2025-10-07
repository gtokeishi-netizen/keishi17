<?php
/**
 * 補助金・助成金情報サイト - クリーンヒーローセクション
 * Grant & Subsidy Information Site - Clean Hero Section
 * @package Grant_Insight_Clean
 * @version 29.0-clean
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// ヘルパー関数
if (!function_exists('gip_safe_output')) {
    function gip_safe_output($text, $allow_html = false) {
        return $allow_html ? wp_kses_post($text) : esc_html($text);
    }
}

if (!function_exists('gip_get_option')) {
    function gip_get_option($key, $default = '') {
        $value = get_option('gip_' . $key, $default);
        return !empty($value) ? $value : $default;
    }
}

// 設定データ
$hero_config = array(
    'main_title' => gip_get_option('hero_main_title', '補助金・助成金を'),
    'sub_title' => gip_get_option('hero_sub_title', 'AIが瞬時に発見'),
    'description' => gip_get_option('hero_description', 'あなたのビジネスに最適な補助金・助成金情報を、最新AIテクノロジーが瞬時に発見。専門家による申請サポートで成功率98.7%を実現します。'),
    'cta_primary_text' => gip_get_option('hero_cta_primary_text', '無料で助成金を探す'),
);

// 統計データ
$stats = array(
    array('number' => '12,847', 'label' => '助成金データベース'),
    array('number' => '98.7%', 'label' => 'マッチング精度'),
    array('number' => '24時間', 'label' => 'AI自動更新'),
    array('number' => '完全無料', 'label' => 'サービス利用')
);
?>

<style>
/* ヒーローセクション - クリーン版 */
.hero-clean {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
    overflow: hidden;
    padding: 2rem 0;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    width: 100%;
}

.hero-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    min-height: 600px;
}

/* 左側コンテンツ */
.hero-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #000;
    color: #fff;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    width: fit-content;
}

.hero-badge::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 900;
    line-height: 1.1;
    color: #171717;
    margin: 0;
}

.hero-title-highlight {
    background: linear-gradient(135deg, #000 0%, #404040 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-description {
    font-size: 1.25rem;
    line-height: 1.6;
    color: #525252;
    margin: 0;
}

.hero-cta {
    display: flex;
    gap: 1rem;
}

.btn-hero {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 3rem;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-hero--primary {
    background: linear-gradient(135deg, #000 0%, #262626 100%);
    color: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.btn-hero--primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    margin-top: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: 900;
    color: #171717;
}

.stat-label {
    font-size: 0.875rem;
    color: #737373;
    font-weight: 500;
}

/* 右側ビジュアル */
.hero-visual {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-mockup {
    position: relative;
    width: 100%;
    max-width: 500px;
    background: #fff;
    border-radius: 1.5rem;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.mockup-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.window-controls {
    display: flex;
    gap: 0.5rem;
}

.window-control {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #d1d5db;
}

.window-control:nth-child(1) { background: #ef4444; }
.window-control:nth-child(2) { background: #f59e0b; }
.window-control:nth-child(3) { background: #22c55e; }

.mockup-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.mockup-content {
    padding: 2rem;
}

.mockup-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.mockup-stat {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
}

.mockup-stat-number {
    font-size: 1.5rem;
    font-weight: 900;
    color: #111827;
    margin-bottom: 0.25rem;
}

.mockup-stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.mockup-progress {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    position: relative;
}

.progress-circle {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(from 0deg, #000 0deg, #000 354deg, #e5e7eb 354deg);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::after {
    content: '';
    width: 80px;
    height: 80px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
}

.progress-text {
    position: absolute;
    z-index: 1;
    text-align: center;
}

.progress-number {
    font-size: 1.25rem;
    font-weight: 900;
    color: #111827;
}

.progress-label {
    font-size: 0.75rem;
    color: #6b7280;
}

/* フローティング要素 */
.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
}

.floating-dot {
    position: absolute;
    width: 8px;
    height: 8px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.floating-dot:nth-child(1) {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.floating-dot:nth-child(2) {
    top: 60%;
    right: 20%;
    animation-delay: 2s;
}

.floating-dot:nth-child(3) {
    bottom: 30%;
    left: 30%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* レスポンシブ対応 */
@media (max-width: 1024px) {
    .hero-grid {
        gap: 3rem;
    }
    
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-description {
        font-size: 1.125rem;
    }
}

@media (max-width: 768px) {
    .hero-clean {
        min-height: auto;
        padding: 4rem 0;
    }
    
    .hero-container {
        padding: 0 1rem;
    }
    
    .hero-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
        text-align: center;
    }
    
    .hero-description {
        font-size: 1rem;
        text-align: center;
    }
    
    .hero-cta {
        justify-content: center;
    }
    
    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        text-align: center;
    }
    
    .mockup-content {
        padding: 1.5rem;
    }
    
    .mockup-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

@media (max-width: 640px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-stats {
        grid-template-columns: 1fr;
    }
    
    .btn-hero {
        padding: 0.875rem 1.75rem;
        font-size: 0.9375rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}
</style>

<section class="hero-clean" role="banner" aria-label="補助金・助成金AIプラットフォーム">
    
    <!-- フローティング要素 -->
    <div class="floating-elements" aria-hidden="true">
        <div class="floating-dot"></div>
        <div class="floating-dot"></div>
        <div class="floating-dot"></div>
    </div>
    
    <!-- メインコンテンツ -->
    <div class="hero-container">
        <div class="hero-grid">
            
            <!-- 左側：コンテンツ -->
            <div class="hero-content">
                
                <!-- ステータスバッジ -->
                <div class="hero-badge" role="note" aria-label="AIパワードプラットフォーム">
                    AI POWERED PLATFORM
                </div>
                
                <!-- メインタイトル -->
                <h1 class="hero-title">
                    <?php echo gip_safe_output($hero_config['main_title']); ?><br>
                    <span class="hero-title-highlight"><?php echo gip_safe_output($hero_config['sub_title']); ?></span><br>
                    成功まで完全サポート
                </h1>
                
                <!-- 説明文 -->
                <p class="hero-description">
                    <?php echo gip_safe_output($hero_config['description']); ?>
                </p>
                
                <!-- CTAボタン -->
                <div class="hero-cta">
                    <a href="<?php echo esc_url(home_url('/grant/')); ?>" class="btn-hero btn-hero--primary" aria-label="無料で助成金を探す">
                        <span>🔍</span>
                        <span><?php echo gip_safe_output($hero_config['cta_primary_text']); ?></span>
                    </a>
                </div>
                
                <!-- 統計表示 -->
                <div class="hero-stats">
                    <?php foreach (array_slice($stats, 0, 2) as $stat): ?>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo gip_safe_output($stat['number']); ?></div>
                        <div class="stat-label"><?php echo gip_safe_output($stat['label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- 右側：ビジュアル -->
            <div class="hero-visual">
                <div class="hero-mockup">
                    
                    <!-- ヘッダー -->
                    <div class="mockup-header">
                        <div class="window-controls">
                            <div class="window-control"></div>
                            <div class="window-control"></div>
                            <div class="window-control"></div>
                        </div>
                        <div class="mockup-title">助成金マッチングシステム</div>
                    </div>
                    
                    <!-- コンテンツ -->
                    <div class="mockup-content">
                        
                        <!-- 統計グリッド -->
                        <div class="mockup-stats">
                            <?php foreach (array_slice($stats, 0, 4) as $stat): ?>
                            <div class="mockup-stat">
                                <div class="mockup-stat-number"><?php echo gip_safe_output($stat['number']); ?></div>
                                <div class="mockup-stat-label"><?php echo gip_safe_output($stat['label']); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- プログレス表示 -->
                        <div class="mockup-progress">
                            <div class="progress-circle">
                                <div class="progress-text">
                                    <div class="progress-number">98.7%</div>
                                    <div class="progress-label">精度</div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // シンプルなアニメーション
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // 要素を観察
    document.querySelectorAll('.hero-content > *').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
</script>