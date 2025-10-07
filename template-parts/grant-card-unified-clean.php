<?php
/**
 * Grant Card Unified - Clean & Optimized Edition
 * template-parts/grant-card-unified.php
 * 
 * シンプルでスタイリッシュな統一カードテンプレート（最適化版）
 * 
 * @package Grant_Insight_Clean
 * @version 11.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// グローバル変数から必要データを取得
global $post, $current_view, $display_mode;

$post_id = get_the_ID();
if (!$post_id) return;

// 表示モードの判定
$display_mode = $display_mode ?? (isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'card');
$view_class = 'grant-view-' . $display_mode;

// 基本データ取得
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

// ACFフィールド取得
$grant_data = array(
    'organization' => get_field('organization', $post_id) ?: '',
    'max_amount' => get_field('max_amount', $post_id) ?: '',
    'max_amount_numeric' => intval(get_field('max_amount_numeric', $post_id)),
    'deadline' => get_field('deadline', $post_id) ?: '',
    'deadline_date' => get_field('deadline_date', $post_id) ?: '',
    'grant_target' => get_field('grant_target', $post_id) ?: '',
    'application_method' => get_field('application_method', $post_id) ?: '',
    'contact_info' => get_field('contact_info', $post_id) ?: '',
    'official_url' => get_field('official_url', $post_id) ?: '',
    'regional_limitation' => get_field('regional_limitation', $post_id) ?: '',
    'application_status' => get_field('application_status', $post_id) ?: 'open',
    'required_documents' => get_field('required_documents', $post_id) ?: '',
    'adoption_rate' => floatval(get_field('adoption_rate', $post_id)),
    'grant_difficulty' => get_field('grant_difficulty', $post_id) ?: 'normal',
    'target_expenses' => get_field('target_expenses', $post_id) ?: '',
    'subsidy_rate' => get_field('subsidy_rate', $post_id) ?: '',
    'is_featured' => get_field('is_featured', $post_id) ?: false,
    'priority_order' => intval(get_field('priority_order', $post_id)) ?: 100,
    'ai_summary' => get_field('ai_summary', $post_id) ?: get_post_meta($post_id, 'ai_summary', true),
);

// 個別変数に展開
$ai_summary = $grant_data['ai_summary'];
$max_amount = $grant_data['max_amount'];
$max_amount_numeric = $grant_data['max_amount_numeric'];
$application_status = $grant_data['application_status'];
$organization = $grant_data['organization'];
$grant_target = $grant_data['grant_target'];
$grant_difficulty = $grant_data['grant_difficulty'];
$official_url = $grant_data['official_url'];
$eligible_expenses = $grant_data['target_expenses'];
$required_documents = $grant_data['required_documents'];
$contact_info = $grant_data['contact_info'];
$is_featured = $grant_data['is_featured'];
$priority_order = $grant_data['priority_order'];

// 締切日の計算
$deadline_info_text = '';
$deadline_class = '';
$days_remaining = 0;
$deadline_timestamp = 0;
$deadline_formatted = '';

if ($grant_data['deadline_date']) {
    $deadline_timestamp = strtotime($grant_data['deadline_date']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $deadline_formatted = date('Y年n月j日', $deadline_timestamp);
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
    }
} elseif ($grant_data['deadline']) {
    $deadline_formatted = $grant_data['deadline'];
    $deadline_timestamp = strtotime($grant_data['deadline']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
    }
}

// タクソノミーデータ
$taxonomies = array(
    'categories' => get_the_terms($post_id, 'grant_category'),
    'prefectures' => get_the_terms($post_id, 'grant_prefecture'),
);

$main_category = ($taxonomies['categories'] && !is_wp_error($taxonomies['categories'])) ? $taxonomies['categories'][0]->name : '';
$main_prefecture = ($taxonomies['prefectures'] && !is_wp_error($taxonomies['prefectures'])) ? $taxonomies['prefectures'][0] : null;
$prefecture = $main_prefecture ? $main_prefecture->name : '全国';

// 金額フォーマット
$formatted_amount = '';
$max_amount_yen = $grant_data['max_amount_numeric'];
if ($max_amount_yen > 0) {
    if ($max_amount_yen >= 100000000) {
        $formatted_amount = number_format($max_amount_yen / 100000000, 1) . '億円';
    } elseif ($max_amount_yen >= 10000) {
        $formatted_amount = number_format($max_amount_yen / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_yen) . '円';
    }
} elseif ($grant_data['max_amount']) {
    $formatted_amount = $grant_data['max_amount'];
}
$amount_display = $formatted_amount;

// ステータス表示
$status_labels = array(
    'open' => '募集中',
    'closed' => '募集終了',
    'planned' => '募集予定',
    'suspended' => '一時停止'
);
$status_display = $status_labels[$application_status] ?? '募集中';

// 締切日情報の処理
$deadline_info = array();
if ($deadline_timestamp > 0 && $days_remaining > 0) {
    if ($days_remaining <= 0) {
        $deadline_class = 'expired';
        $deadline_info_text = '募集終了';
        $deadline_info = array('class' => 'expired', 'text' => '募集終了');
    } elseif ($days_remaining <= 7) {
        $deadline_class = 'urgent';
        $deadline_info_text = 'あと' . $days_remaining . '日';
        $deadline_info = array('class' => 'urgent', 'text' => '残り'.$days_remaining.'日');
    } elseif ($days_remaining <= 30) {
        $deadline_class = 'warning';
        $deadline_info_text = 'あと' . $days_remaining . '日';
        $deadline_info = array('class' => 'warning', 'text' => '残り'.$days_remaining.'日');
    } else {
        $deadline_info = array('class' => 'normal', 'text' => $deadline_formatted);
    }
} elseif ($deadline_formatted) {
    $deadline_info = array('class' => 'normal', 'text' => $deadline_formatted);
}

// 難易度設定
$difficulty_configs = array(
    'easy' => array('label' => '簡単', 'dots' => 1, 'color' => '#16a34a'),
    'normal' => array('label' => '普通', 'dots' => 2, 'color' => '#525252'),
    'hard' => array('label' => '難しい', 'dots' => 3, 'color' => '#d97706'),
    'very_hard' => array('label' => '非常に困難', 'dots' => 4, 'color' => '#dc2626')
);
$difficulty = $grant_data['grant_difficulty'];
$difficulty_data = $difficulty_configs[$difficulty] ?? $difficulty_configs['normal'];

// CSS・JSの重複防止
static $assets_loaded = false;
?>

<?php if (!$assets_loaded): $assets_loaded = true; ?>

<style>
/* Clean Grant Card Design System - Optimized Edition */

:root {
    /* Core Colors */
    --clean-primary: #000000;
    --clean-white: #ffffff;
    --clean-gray-100: #f5f5f5;
    --clean-gray-200: #e5e5e5;
    --clean-gray-300: #d4d4d4;
    --clean-gray-400: #a3a3a3;
    --clean-gray-500: #737373;
    --clean-gray-600: #525252;
    --clean-gray-800: #262626;
    --clean-gray-900: #171717;
    
    /* Gradients */
    --clean-gradient-primary: linear-gradient(135deg, #000000 0%, #262626 100%);
    
    /* Shadows */
    --clean-shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --clean-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    
    /* Border Radius */
    --clean-radius-md: 0.5rem;
    --clean-radius-lg: 0.75rem;
    --clean-radius-xl: 1rem;
    --clean-radius-2xl: 1.5rem;
    
    /* Transitions */
    --clean-transition: all 0.15s ease-in-out;
    --clean-transition-slow: all 0.3s ease-in-out;
}

/* グリッド */
.grants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
    width: 100%;
    box-sizing: border-box;
}

@media (max-width: 900px) {
    .grants-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        padding: 1.25rem;
    }
}

/* カード基本スタイル */
.grant-view-card .grant-card-unified {
    position: relative;
    width: 100%;
    min-height: 320px;
    background: var(--clean-white);
    border: 2px solid var(--clean-gray-300);
    border-radius: var(--clean-radius-xl);
    overflow: hidden;
    transition: var(--clean-transition-slow);
    cursor: default;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.grant-view-card .grant-card-unified:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: var(--clean-gray-500);
}

/* ステータスヘッダー */
.grant-status-header {
    position: relative;
    height: 3rem;
    background: var(--clean-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
}

.grant-status-header.status--closed {
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
}

.grant-status-badge {
    color: var(--clean-white);
    font-size: 0.875rem;
    font-weight: 600;
}

.grant-deadline-indicator {
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    color: var(--clean-white);
    font-size: 0.75rem;
    font-weight: 600;
}

/* カードコンテンツ */
.grant-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
}

/* タイトルセクション */
.grant-title-section {
    margin-bottom: 1rem;
}

.grant-category-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    border-radius: var(--clean-radius-2xl);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.75rem;
}

.grant-title {
    font-size: 1.125rem;
    font-weight: 700;
    line-height: 1.4;
    color: var(--clean-gray-900);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.grant-title a {
    color: inherit;
    text-decoration: none;
    transition: var(--clean-transition);
}

.grant-title a:hover {
    color: var(--clean-gray-800);
}

/* AI要約セクション - 800px高さ対応版 */
.grant-ai-summary {
    position: relative;
    padding: 2rem;
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 30%, #ffffff 70%, #f0f9ff 100%);
    border: 4px solid var(--clean-gray-900);
    border-radius: var(--clean-radius-2xl);
    margin-bottom: 1.5rem;
    min-height: 180px;
    max-height: 240px;
    overflow: hidden;
    transition: var(--clean-transition-slow);
    box-shadow: 
        0 12px 35px rgba(0, 0, 0, 0.15),
        inset 0 2px 0 rgba(255, 255, 255, 0.9);
    cursor: default;
    flex: 1;
}

.grant-ai-summary:hover {
    transform: translateY(-8px) scale(1.03);
    max-height: 280px;
    overflow-y: auto;
    box-shadow: 
        0 20px 45px rgba(0, 0, 0, 0.25),
        0 8px 20px rgba(0, 0, 0, 0.15);
    border-color: var(--clean-gray-600);
}

.grant-ai-summary-label {
    color: var(--clean-white);
    background: var(--clean-gradient-primary);
    font-size: 0.8rem;
    font-weight: 900;
    margin: -1.8rem -1.8rem 1.25rem -1.8rem;
    padding: 0.9rem 1.8rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    text-align: center;
}

.grant-ai-summary-text {
    color: var(--clean-gray-900);
    font-size: 1.05rem;
    line-height: 1.7;
    margin: 0;
    font-weight: 600;
    max-height: 140px;
    overflow-y: hidden;
}

.grant-ai-summary:hover .grant-ai-summary-text {
    max-height: 220px;
    overflow-y: auto;
}

/* 情報グリッド - 2×1レイアウト（助成額と地域） */
.grant-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.grant-info-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 1.25rem;
    background: var(--clean-white);
    border: 3px solid var(--clean-gray-900);
    border-radius: var(--clean-radius-xl);
    transition: var(--clean-transition-slow);
    min-height: 100px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}

.grant-info-item:hover {
    transform: translateY(-6px) scale(1.05);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
    border-color: var(--clean-gray-600);
}

.grant-info-label {
    font-size: 0.7rem;
    font-weight: 900;
    color: var(--clean-gray-800);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 0.75rem;
}

.grant-info-value {
    display: block;
    font-size: 1rem;
    font-weight: 900;
    color: var(--clean-gray-900);
    line-height: 1.3;
    text-align: center;
}

/* アクションフッター */
.grant-card-footer {
    padding: 1.25rem;
    background: var(--clean-white);
    border-top: 2px solid var(--clean-gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: auto;
}

.grant-actions {
    display: flex;
    gap: 0.5rem;
    flex: 1;
    flex-wrap: wrap;
}

.grant-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.75rem 1.25rem;
    min-height: 45px;
    border: 2px solid transparent;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--clean-transition-slow);
    text-decoration: none;
    white-space: nowrap;
    flex: 1;
    min-width: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.grant-btn--primary {
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 2px solid var(--clean-gray-800);
}

.grant-btn--primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.grant-btn--secondary {
    background: var(--clean-white);
    color: var(--clean-gray-900);
    border: 2px solid var(--clean-gray-400);
}

.grant-btn--secondary:hover {
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    transform: translateY(-2px) scale(1.01);
    border-color: var(--clean-gray-800);
}

.grant-btn--ai {
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    border: 2px solid var(--clean-gray-800);
}

.grant-btn--ai:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .grants-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
        gap: 1.25rem;
    }
    
    .grant-view-card .grant-card-unified {
        min-height: 280px;
    }
    
    .grant-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .grant-info-item {
        min-height: 80px;
        padding: 1rem;
    }
    
    .grant-ai-summary {
        min-height: 160px;
        max-height: 200px;
        padding: 1.25rem;
    }
    
    .grant-ai-summary:hover {
        max-height: 240px;
    }
    
    .grant-ai-summary-text {
        font-size: 0.95rem;
        max-height: 120px;
    }
    
    .grant-btn {
        width: 100%;
        justify-content: center;
        min-height: 48px;
    }
    
    .grant-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
}

/* さらに小さい画面向け */
@media (max-width: 480px) {
    .grants-grid {
        padding: 0.75rem;
        gap: 0.75rem;
    }
    
    .grant-card-content {
        padding: 0.875rem;
    }
    
    .grant-title {
        font-size: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // カードクリック処理
    document.addEventListener('click', function(e) {
        if (e.target.closest('.grant-btn--primary')) {
            const btn = e.target.closest('.grant-btn--primary');
            const href = btn.getAttribute('href');
            if (href) {
                btn.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    window.location.href = href;
                }, 100);
            }
        }
    });
    
    // AI質問モーダル
    window.openGrantAIChat = function(button) {
        const postId = button.getAttribute('data-post-id');
        const grantTitle = button.getAttribute('data-grant-title');
        
        if (!postId) {
            console.error('Post ID not found');
            return;
        }
        
        showAIChatModal(postId, grantTitle);
    };
    
    // AI質問モーダルの表示
    function showAIChatModal(postId, grantTitle) {
        const existingModal = document.querySelector('.grant-ai-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        const modalHTML = `
            <div class="grant-ai-modal" id="grant-ai-modal">
                <div class="grant-ai-modal-overlay" onclick="closeAIChatModal()"></div>
                <div class="grant-ai-modal-container">
                    <div class="grant-ai-modal-header">
                        <div class="grant-ai-modal-title">
                            <span>この助成金について質問する</span>
                        </div>
                        <div class="grant-ai-modal-subtitle">${grantTitle}</div>
                        <button class="grant-ai-modal-close" onclick="closeAIChatModal()">
                            閉じる
                        </button>
                    </div>
                    <div class="grant-ai-modal-body">
                        <div class="grant-ai-chat-messages" id="ai-chat-messages-${postId}">
                            <div class="grant-ai-message grant-ai-message--assistant">
                                <div class="grant-ai-message-content">
                                    この助成金について何でもお聞きください。申請条件、必要書類、申請方法などについてお答えします。
                                </div>
                            </div>
                        </div>
                        <div class="grant-ai-chat-input-container">
                            <div class="grant-ai-chat-input-wrapper">
                                <textarea 
                                    class="grant-ai-chat-input" 
                                    id="ai-chat-input-${postId}"
                                    placeholder="例：申請条件は何ですか？必要書類を教えてください"
                                    rows="3"></textarea>
                                <button 
                                    class="grant-ai-chat-send" 
                                    id="ai-chat-send-${postId}"
                                    onclick="sendAIQuestion('${postId}')">
                                    送信
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        setTimeout(() => {
            const input = document.getElementById(`ai-chat-input-${postId}`);
            if (input) {
                input.focus();
            }
        }, 100);
        
        const input = document.getElementById(`ai-chat-input-${postId}`);
        if (input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendAIQuestion(postId);
                }
            });
        }
    }
    
    window.closeAIChatModal = function() {
        const modal = document.querySelector('.grant-ai-modal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    };
    
    window.sendAIQuestion = function(postId) {
        const input = document.getElementById(`ai-chat-input-${postId}`);
        const sendBtn = document.getElementById(`ai-chat-send-${postId}`);
        const messagesContainer = document.getElementById(`ai-chat-messages-${postId}`);
        
        if (!input || !messagesContainer) return;
        
        const question = input.value.trim();
        if (!question) return;
        
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '送信中...';
        }
        
        const userMessage = document.createElement('div');
        userMessage.className = 'grant-ai-message grant-ai-message--user';
        userMessage.innerHTML = `
            <div class="grant-ai-message-content">${escapeHtml(question)}</div>
        `;
        messagesContainer.appendChild(userMessage);
        
        input.value = '';
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        const formData = new FormData();
        formData.append('action', 'handle_grant_ai_question');
        formData.append('post_id', postId);
        formData.append('question', question);
        formData.append('nonce', '<?php echo wp_create_nonce('gi_ajax_nonce'); ?>');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const loadingMessage = document.createElement('div');
            loadingMessage.className = 'grant-ai-message grant-ai-message--assistant grant-ai-loading';
            loadingMessage.innerHTML = `
                <div class="grant-ai-message-content">
                    <div class="grant-ai-typing">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(loadingMessage);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            setTimeout(() => {
                loadingMessage.remove();
                
                if (data.success) {
                    const assistantMessage = document.createElement('div');
                    assistantMessage.className = 'grant-ai-message grant-ai-message--assistant';
                    assistantMessage.innerHTML = `
                        <div class="grant-ai-message-content">${escapeHtml(data.data.response)}</div>
                    `;
                    messagesContainer.appendChild(assistantMessage);
                } else {
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'grant-ai-message grant-ai-message--error';
                    errorMessage.innerHTML = `
                        <div class="grant-ai-message-content">エラー: 申し訳ございません。エラーが発生しました。</div>
                    `;
                    messagesContainer.appendChild(errorMessage);
                }
                
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'grant-ai-message grant-ai-message--error';
            errorMessage.innerHTML = `
                <div class="grant-ai-message-content">エラー: 通信エラーが発生しました。</div>
            `;
            messagesContainer.appendChild(errorMessage);
        })
        .finally(() => {
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '送信';
            }
            input.focus();
        });
    };
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
<?php endif; ?>

<!-- クリーンカード本体 -->
<article class="grant-card-unified <?php echo esc_attr($view_class); ?>" 
         data-post-id="<?php echo esc_attr($post_id); ?>"
         data-priority="<?php echo esc_attr($priority_order); ?>"
         role="article"
         aria-label="助成金情報カード">
    
    <!-- ステータスヘッダー -->
    <header class="grant-status-header <?php echo $application_status === 'closed' ? 'status--closed' : ''; ?>">
        <div class="grant-status-badge">
            <span><?php echo esc_html($status_display); ?></span>
        </div>
        <?php if (!empty($deadline_info)): ?>
        <div class="grant-deadline-indicator">
            <span><?php echo esc_html($deadline_info['text']); ?></span>
        </div>
        <?php endif; ?>
    </header>
    
    <!-- カードコンテンツ -->
    <div class="grant-card-content">
        <div class="grant-main-info">
            <!-- タイトルセクション -->
            <div class="grant-title-section">
                <?php if ($main_category): ?>
                <div class="grant-category-tag">
                    <span><?php echo esc_html($main_category); ?></span>
                </div>
                <?php endif; ?>
                <h3 class="grant-title">
                    <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>の詳細ページ" tabindex="-1">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>
            </div>
            
            <!-- AI要約 -->
            <?php if ($ai_summary || $excerpt): ?>
            <div class="grant-ai-summary">
                <div class="grant-ai-summary-label">
                    <span>AI要約</span>
                </div>
                <p class="grant-ai-summary-text">
                    <?php echo esc_html(wp_trim_words($ai_summary ?: $excerpt, 40, '...')); ?>
                </p>
            </div>
            <?php endif; ?>
            
            <!-- 情報グリッド - 助成額と地域のみ -->
            <div class="grant-info-grid">
                <?php if ($amount_display): ?>
                <div class="grant-info-item">
                    <div class="grant-info-label">最大助成額</div>
                    <div class="grant-info-value"><?php echo esc_html($amount_display); ?></div>
                </div>
                <?php endif; ?>
                <div class="grant-info-item">
                    <div class="grant-info-label">対象地域</div>
                    <div class="grant-info-value"><?php echo esc_html($prefecture); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- アクションフッター -->
    <footer class="grant-card-footer">
        <div class="grant-actions">
            <a href="<?php echo esc_url($permalink); ?>" class="grant-btn grant-btn--primary" role="button">
                <span>詳細を見る</span>
            </a>
            <button class="grant-btn grant-btn--ai" 
                    data-post-id="<?php echo esc_attr($post_id); ?>" 
                    data-grant-title="<?php echo esc_attr($title); ?>"
                    onclick="openGrantAIChat(this)" 
                    role="button">
                <span>質問する</span>
            </button>
            <?php if ($official_url): ?>
            <a href="<?php echo esc_url($official_url); ?>" class="grant-btn grant-btn--secondary" target="_blank" rel="noopener noreferrer" role="button">
                <span>公式サイト</span>
            </a>
            <?php endif; ?>
        </div>
    </footer>
</article>