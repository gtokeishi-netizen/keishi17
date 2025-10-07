/*!
 * Grant Insight Clean - Essential Frontend JavaScript
 * Cleaned and optimized main script
 * 
 * @version 2.0.0
 * @date 2025-10-07
 */

/**
 * Main namespace for Grant Insight functionality
 */
const GrantInsight = {
    version: '2.0.0',
    
    config: {
        debounceDelay: 300,
        apiEndpoint: '/wp-admin/admin-ajax.php',
        searchMinLength: 2
    },

    initialized: false,
    
    state: {
        lastScrollY: 0,
        isScrolling: false,
        activeFilters: new Map()
    },

    elements: {},

    /**
     * Initialize the application
     */
    init() {
        if (this.initialized) return;
        
        this.cacheElements();
        this.bindEvents();
        this.initializeComponents();
        
        this.initialized = true;
        console.log(`Grant Insight Clean v${this.version} initialized`);
    },

    /**
     * Cache DOM elements for performance
     */
    cacheElements() {
        this.elements = {
            body: document.body,
            header: document.querySelector('.site-header'),
            searchForm: document.querySelector('#grant-search-form'),
            searchInput: document.querySelector('#grant-search-input'),
            filterForm: document.querySelector('.grant-filters'),
            grantsContainer: document.querySelector('.grants-grid, .grants-list'),
            loadMoreBtn: document.querySelector('.load-more-grants'),
            scrollToTop: document.querySelector('.scroll-to-top')
        };
    },

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Search functionality
        if (this.elements.searchInput) {
            this.elements.searchInput.addEventListener('input', 
                this.debounce(this.handleSearch.bind(this), this.config.debounceDelay)
            );
        }

        // Filter functionality
        if (this.elements.filterForm) {
            this.elements.filterForm.addEventListener('change', this.handleFilter.bind(this));
        }

        // Load more button
        if (this.elements.loadMoreBtn) {
            this.elements.loadMoreBtn.addEventListener('click', this.handleLoadMore.bind(this));
        }

        // Scroll to top
        if (this.elements.scrollToTop) {
            this.elements.scrollToTop.addEventListener('click', this.scrollToTop.bind(this));
        }

        // Window events
        window.addEventListener('scroll', this.debounce(this.handleScroll.bind(this), 100));
        window.addEventListener('resize', this.debounce(this.handleResize.bind(this), 250));

        // Form submissions
        document.addEventListener('submit', this.handleFormSubmit.bind(this));
    },

    /**
     * Initialize components
     */
    initializeComponents() {
        this.initScrollToTop();
        this.initSmoothScrolling();
        this.updateHeaderHeight();
    },

    /**
     * Debounce utility function
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Handle search input
     */
    handleSearch(event) {
        const query = event.target.value.trim();
        
        if (query.length < this.config.searchMinLength && query.length > 0) {
            return;
        }
        
        this.performSearch(query);
    },

    /**
     * Perform search request
     */
    async performSearch(query) {
        if (!this.elements.grantsContainer) return;
        
        try {
            this.showLoading(this.elements.grantsContainer);
            
            const formData = new FormData();
            formData.append('action', 'filter_grants');
            formData.append('search', query);
            formData.append('nonce', this.getNonce());
            
            const response = await fetch(this.config.apiEndpoint, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.elements.grantsContainer.innerHTML = data.data.html;
                this.initializeCardComponents();
            } else {
                this.showError('検索中にエラーが発生しました。');
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showError('検索中にエラーが発生しました。');
        } finally {
            this.hideLoading(this.elements.grantsContainer);
        }
    },

    /**
     * Handle filter changes
     */
    handleFilter(event) {
        const formData = new FormData(this.elements.filterForm);
        this.performFilter(formData);
    },

    /**
     * Perform filter request
     */
    async performFilter(formData) {
        if (!this.elements.grantsContainer) return;
        
        try {
            this.showLoading(this.elements.grantsContainer);
            
            formData.append('action', 'filter_grants');
            formData.append('nonce', this.getNonce());
            
            const response = await fetch(this.config.apiEndpoint, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.elements.grantsContainer.innerHTML = data.data.html;
                this.initializeCardComponents();
            } else {
                this.showError('フィルター中にエラーが発生しました。');
            }
        } catch (error) {
            console.error('Filter error:', error);
            this.showError('フィルター中にエラーが発生しました。');
        } finally {
            this.hideLoading(this.elements.grantsContainer);
        }
    },

    /**
     * Handle load more button
     */
    async handleLoadMore(event) {
        event.preventDefault();
        
        const page = parseInt(this.elements.loadMoreBtn.dataset.page || '1') + 1;
        
        try {
            this.showLoading(this.elements.loadMoreBtn);
            
            const formData = new FormData();
            formData.append('action', 'load_more_grants');
            formData.append('page', page);
            formData.append('nonce', this.getNonce());
            
            const response = await fetch(this.config.apiEndpoint, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success && data.data.html) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.data.html;
                
                while (tempDiv.firstChild) {
                    this.elements.grantsContainer.appendChild(tempDiv.firstChild);
                }
                
                this.elements.loadMoreBtn.dataset.page = page;
                this.initializeCardComponents();
                
                if (!data.data.has_more) {
                    this.elements.loadMoreBtn.style.display = 'none';
                }
            } else {
                this.showError('これ以上読み込むデータがありません。');
            }
        } catch (error) {
            console.error('Load more error:', error);
            this.showError('データの読み込み中にエラーが発生しました。');
        } finally {
            this.hideLoading(this.elements.loadMoreBtn);
        }
    },

    /**
     * Handle scroll events
     */
    handleScroll() {
        const currentScrollY = window.scrollY;
        this.state.isScrolling = true;
        
        // Update scroll to top button
        this.updateScrollToTop(currentScrollY);
        
        // Update header on scroll
        this.updateHeaderOnScroll(currentScrollY);
        
        this.state.lastScrollY = currentScrollY;
        
        // Reset scrolling state
        clearTimeout(this.scrollTimeout);
        this.scrollTimeout = setTimeout(() => {
            this.state.isScrolling = false;
        }, 150);
    },

    /**
     * Handle window resize
     */
    handleResize() {
        this.updateHeaderHeight();
    },

    /**
     * Handle form submissions
     */
    handleFormSubmit(event) {
        const form = event.target;
        
        if (form.matches('#grant-search-form')) {
            event.preventDefault();
            const query = form.querySelector('input[type="search"]').value;
            this.performSearch(query);
        }
    },

    /**
     * Initialize scroll to top functionality
     */
    initScrollToTop() {
        if (!this.elements.scrollToTop) {
            this.createScrollToTop();
        }
        this.updateScrollToTop(window.scrollY);
    },

    /**
     * Create scroll to top button
     */
    createScrollToTop() {
        const button = document.createElement('button');
        button.className = 'scroll-to-top';
        button.innerHTML = '↑';
        button.setAttribute('aria-label', 'ページトップへ戻る');
        button.style.cssText = `
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        `;
        
        document.body.appendChild(button);
        this.elements.scrollToTop = button;
        
        button.addEventListener('click', this.scrollToTop.bind(this));
    },

    /**
     * Update scroll to top button visibility
     */
    updateScrollToTop(scrollY) {
        if (!this.elements.scrollToTop) return;
        
        if (scrollY > 300) {
            this.elements.scrollToTop.style.opacity = '1';
            this.elements.scrollToTop.style.visibility = 'visible';
        } else {
            this.elements.scrollToTop.style.opacity = '0';
            this.elements.scrollToTop.style.visibility = 'hidden';
        }
    },

    /**
     * Scroll to top smoothly
     */
    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    },

    /**
     * Initialize smooth scrolling for anchor links
     */
    initSmoothScrolling() {
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href^="#"]');
            if (!link) return;
            
            const href = link.getAttribute('href');
            const target = document.querySelector(href);
            
            if (target) {
                event.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    },

    /**
     * Update header height for calculations
     */
    updateHeaderHeight() {
        if (this.elements.header) {
            this.state.headerHeight = this.elements.header.offsetHeight;
        }
    },

    /**
     * Update header appearance on scroll
     */
    updateHeaderOnScroll(scrollY) {
        if (!this.elements.header) return;
        
        if (scrollY > 100) {
            this.elements.header.classList.add('scrolled');
        } else {
            this.elements.header.classList.remove('scrolled');
        }
    },

    /**
     * Initialize card components after content update
     */
    initializeCardComponents() {
        // Reinitialize any card-specific functionality
        const cards = document.querySelectorAll('.grant-card-unified');
        cards.forEach(card => {
            // Ensure buttons are clickable
            const buttons = card.querySelectorAll('.grant-btn');
            buttons.forEach(btn => {
                btn.style.pointerEvents = 'auto';
            });
        });
    },

    /**
     * Show loading state
     */
    showLoading(element) {
        if (!element) return;
        
        element.style.opacity = '0.6';
        element.style.pointerEvents = 'none';
        
        if (!element.querySelector('.loading-spinner')) {
            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner';
            spinner.innerHTML = '読み込み中...';
            spinner.style.cssText = `
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(255, 255, 255, 0.9);
                padding: 1rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                z-index: 10;
            `;
            
            if (element.style.position === '' || element.style.position === 'static') {
                element.style.position = 'relative';
            }
            
            element.appendChild(spinner);
        }
    },

    /**
     * Hide loading state
     */
    hideLoading(element) {
        if (!element) return;
        
        element.style.opacity = '';
        element.style.pointerEvents = '';
        
        const spinner = element.querySelector('.loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    },

    /**
     * Show error message
     */
    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'error-toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: #dc2626;
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            z-index: 10001;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },

    /**
     * Get nonce for AJAX requests
     */
    getNonce() {
        const nonceElement = document.querySelector('#gi_ajax_nonce');
        return nonceElement ? nonceElement.value : '';
    }
};

/**
 * Initialize when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
    GrantInsight.init();
});

/**
 * Expose to global scope for compatibility
 */
window.GrantInsight = GrantInsight;