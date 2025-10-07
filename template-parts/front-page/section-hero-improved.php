<?php
/**
 * 補助金・助成金情報サイト - 改良版ヒーローセクション
 * Grant & Subsidy Information Site - Improved Hero Section
 * @package Grant_Insight_Improved
 * @version 30.0-visual-enhanced
 * 
 * 提供画像をベースにしたビジュアル重視デザイン
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
    'third_line' => gip_get_option('hero_third_line', '成功まで完全サポート'),
    'description' => gip_get_option('hero_description', 'あなたのビジネスに最適な補助金・助成金情報を、最新AIテクノロジーが瞬時に発見。専門家による申請サポートで成功率98.7%を実現します。'),
    'cta_primary_text' => gip_get_option('hero_cta_primary_text', '無料で助成金を探す'),
);

// 統計データ
$stats = array(
    array('number' => '12,847', 'label' => '助成金データベース'),
    array('number' => '98.7%', 'label' => 'マッチング精度')
);
?>

<style>
/* ヒーローセクション - 改良版 */
.hero-improved {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f1f5f9 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    width: 100%;
}

.hero-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    min-height: 600px;
}

/* 左側コンテンツ */
.hero-left-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    z-index: 2;
    position: relative;
}

/* AIバッジ */
.ai-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #000000 0%, #262626 100%);
    color: #ffffff;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    width: fit-content;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.ai-badge::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}

/* メインタイトル */
.hero-main-title {
    font-size: 3.5rem;
    font-weight: 900;
    line-height: 1.1;
    color: #1f2937;
    margin: 0;
    text-align: left;
}

.hero-main-title .title-line {
    display: block;
}

.hero-main-title .ai-highlight {
    background: linear-gradient(135deg, #000000 0%, #374151 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
}

/* 説明文 */
.hero-description {
    font-size: 1.125rem;
    line-height: 1.7;
    color: #6b7280;
    margin: 0;
    max-width: 90%;
}

/* CTAボタン */
.hero-cta-container {
    display: flex;
    gap: 1rem;
}

.hero-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #000000;
    border: none;
    border-radius: 3rem;
    font-size: 1rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(251, 191, 36, 0.4);
    cursor: pointer;
}

.hero-cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(251, 191, 36, 0.6);
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

/* 統計表示 */
.hero-stats {
    display: flex;
    gap: 3rem;
    margin-top: 1rem;
}

.hero-stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.hero-stat-number {
    font-size: 2.25rem;
    font-weight: 900;
    color: #111827;
    line-height: 1;
}

.hero-stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

/* 右側ビジュアル */
.hero-visual-section {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

/* メイン画像コンテナ */
.hero-image-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    height: 500px;
    background: url('https://joseikin-insight.com/wp-content/uploads/2025/10/名称未設定のデザイン.png') no-repeat center center;
    background-size: contain;
    z-index: 1;
}

/* フローティング要素 */
.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}

.floating-shape {
    position: absolute;
    opacity: 0.1;
    animation: float-gentle 6s ease-in-out infinite;
}

.floating-shape:nth-child(1) {
    top: 10%;
    left: 10%;
    width: 60px;
    height: 60px;
    background: #fbbf24;
    border-radius: 50%;
    animation-delay: 0s;
}

.floating-shape:nth-child(2) {
    top: 30%;
    right: 15%;
    width: 40px;
    height: 40px;
    background: #000000;
    transform: rotate(45deg);
    animation-delay: 2s;
}

.floating-shape:nth-child(3) {
    bottom: 20%;
    left: 20%;
    width: 50px;
    height: 50px;
    background: #6b7280;
    border-radius: 20%;
    animation-delay: 4s;
}

@keyframes float-gentle {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(5deg); }
}

/* デバイスモックアップ（オプション） */
.device-mockups {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    z-index: 2;
    opacity: 0.8;
}

.device-pc, .device-tablet, .device-mobile {
    position: absolute;
    background: #ffffff;
    border-radius: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.device-pc {
    top: 20%;
    left: 10%;
    width: 200px;
    height: 130px;
    border-radius: 0.75rem;
}

.device-tablet {
    top: 10%;
    right: 20%;
    width: 120px;
    height: 160px;
    border-radius: 1rem;
}

.device-mobile {
    bottom: 25%;
    left: 5%;
    width: 80px;
    height: 140px;
    border-radius: 1.5rem;
}

/* レスポンシブ対応 */
@media (max-width: 1024px) {
    .hero-main-title {
        font-size: 3rem;
    }
    
    .hero-content-grid {
        gap: 3rem;
    }
    
    .hero-description {
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    .hero-improved {
        min-height: auto;
        padding: 4rem 0 2rem;
    }
    
    .hero-container {
        padding: 0 1rem;
    }
    
    .hero-content-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
        text-align: center;
    }
    
    .hero-left-content {
        order: 1;
        align-items: center;
        text-align: center;
    }
    
    .hero-visual-section {
        order: 2;
        height: 300px;
    }
    
    .hero-main-title {
        font-size: 2.5rem;
        text-align: center;
    }
    
    .hero-description {
        text-align: center;
        max-width: 100%;
    }
    
    .hero-stats {
        justify-content: center;
        gap: 2rem;
    }
    
    .hero-image-container {
        height: 300px;
        max-width: 100%;
    }
    
    /* スマホ版で画像が隠れないように調整 */
    .hero-visual-section {
        overflow: visible;
        padding: 0 1rem;
    }
    
    /* デバイスモックアップをスマホでは非表示 */
    .device-mockups {
        display: none;
    }
    
    .floating-elements {
        opacity: 0.5;
    }
}

@media (max-width: 640px) {
    .hero-main-title {
        font-size: 2rem;
        line-height: 1.2;
    }
    
    .hero-description {
        font-size: 0.9375rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1.5rem;
        align-items: center;
    }
    
    .hero-stat-number {
        font-size: 1.875rem;
    }
    
    .hero-cta-btn {
        padding: 0.875rem 1.5rem;
        font-size: 0.9375rem;
    }
    
    .hero-visual-section {
        height: 250px;
    }
    
    .hero-image-container {
        height: 250px;
    }
}

/* 特に小さい画面での最適化 */
@media (max-width: 480px) {
    .hero-improved {
        padding: 3rem 0 1rem;
    }
    
    .hero-container {
        padding: 0 0.75rem;
    }
    
    .hero-content-grid {
        gap: 2rem;
    }
    
    .hero-main-title {
        font-size: 1.75rem;
    }
    
    .hero-visual-section {
        height: 200px;
        margin: 0 -0.75rem;
        padding: 0;
    }
    
    .hero-image-container {
        height: 200px;
        width: calc(100% + 1.5rem);
        max-width: none;
        background-size: cover;
        background-position: center;
    }
}

/* アニメーション */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-left-content > * {
    animation: fadeInUp 0.8s ease-out forwards;
}

.hero-left-content > *:nth-child(1) { animation-delay: 0.1s; }
.hero-left-content > *:nth-child(2) { animation-delay: 0.2s; }
.hero-left-content > *:nth-child(3) { animation-delay: 0.3s; }
.hero-left-content > *:nth-child(4) { animation-delay: 0.4s; }
.hero-left-content > *:nth-child(5) { animation-delay: 0.5s; }
</style>

<section class="hero-improved" role="banner" aria-label="補助金・助成金AIプラットフォーム">
    
    <!-- フローティング背景要素 -->
    <div class="floating-elements" aria-hidden="true">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>
    
    <!-- メインコンテンツ -->
    <div class="hero-container">
        <div class="hero-content-grid">
            
            <!-- 左側：コンテンツ -->
            <div class="hero-left-content">
                
                <!-- AIバッジ -->
                <div class="ai-badge" role="note" aria-label="AIパワードプラットフォーム">
                    AI POWERED PLATFORM
                </div>
                
                <!-- メインタイトル -->
                <h1 class="hero-main-title">
                    <span class="title-line"><?php echo gip_safe_output($hero_config['main_title']); ?></span>
                    <span class="title-line ai-highlight"><?php echo gip_safe_output($hero_config['sub_title']); ?></span>
                    <span class="title-line"><?php echo gip_safe_output($hero_config['third_line']); ?></span>
                </h1>
                
                <!-- 説明文 -->
                <p class="hero-description">
                    <?php echo gip_safe_output($hero_config['description']); ?>
                </p>
                
                <!-- CTAボタン -->
                <div class="hero-cta-container">
                    <a href="<?php echo esc_url(home_url('/grant/')); ?>" class="hero-cta-btn" aria-label="無料で助成金を探す">
                        <span>🔍</span>
                        <span><?php echo gip_safe_output($hero_config['cta_primary_text']); ?></span>
                    </a>
                </div>
                
                <!-- 統計表示 -->
                <div class="hero-stats">
                    <?php foreach ($stats as $stat): ?>
                    <div class="hero-stat-item">
                        <div class="hero-stat-number"><?php echo gip_safe_output($stat['number']); ?></div>
                        <div class="hero-stat-label"><?php echo gip_safe_output($stat['label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- 右側：ビジュアル -->
            <div class="hero-visual-section">
                
                <!-- メイン画像 -->
                <div class="hero-image-container"></div>
                
                <!-- デバイスモックアップ（デスクトップのみ） -->
                <div class="device-mockups">
                    <div class="device-pc"></div>
                    <div class="device-tablet"></div>
                    <div class="device-mobile"></div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // シンプルなスクロールアニメーション
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);
    
    // ヒーロー要素を観察
    const heroElements = document.querySelectorAll('.hero-left-content > *');
    heroElements.forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });
    
    // パララックス効果（デスクトップのみ）
    if (window.innerWidth > 768) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-improved');
            if (hero && scrolled < hero.offsetHeight) {
                const floatingElements = document.querySelectorAll('.floating-shape');
                floatingElements.forEach((el, index) => {
                    const speed = (index + 1) * 0.5;
                    el.style.transform = `translateY(${scrolled * speed}px)`;
                });
            }
        });
    }
});
</script>