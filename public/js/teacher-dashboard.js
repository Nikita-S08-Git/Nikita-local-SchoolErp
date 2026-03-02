/**
 * Teacher Dashboard - Interactive Features
 * Modern, responsive dashboard with enhanced UX
 */

(function() {
    'use strict';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initDashboard();
        initAnimations();
        initCharts();
        initNotifications();
        initSearch();
        initFilters();
        initTooltips();
        initModals();
    });

    /**
     * Initialize Dashboard
     */
    function initDashboard() {
        // Animate stats cards on load
        animateStatsCards();
        
        // Setup refresh functionality
        setupRefresh();
        
        // Setup real-time updates
        setupRealTimeUpdates();
        
        // Setup keyboard shortcuts
        setupKeyboardShortcuts();
    }

    /**
     * Animate Stats Cards
     */
    function animateStatsCards() {
        const statsCards = document.querySelectorAll('.stats-card');
        
        statsCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                requestAnimationFrame(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, index * 100);
        });

        // Animate numbers
        const statsValues = document.querySelectorAll('.stats-value');
        statsValues.forEach(stat => {
            const text = stat.textContent;
            const isPercentage = text.includes('%');
            const finalValue = parseFloat(text);
            
            if (!isNaN(finalValue)) {
                animateValue(stat, 0, finalValue, 1500, isPercentage);
            }
        });
    }

    /**
     * Animate Value Counter
     */
    function animateValue(element, start, end, duration, isPercentage = false) {
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = start + (end - start) * easeOutQuart;
            
            element.textContent = isPercentage 
                ? current.toFixed(1) + '%' 
                : Math.floor(current);
            
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = isPercentage ? end.toFixed(1) + '%' : end;
            }
        }
        
        requestAnimationFrame(update);
    }

    /**
     * Initialize Animations
     */
    function initAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe dashboard cards
        document.querySelectorAll('.dashboard-card').forEach(card => {
            observer.observe(card);
        });

        // Hover effects for interactive elements
        addHoverEffects();
    }

    /**
     * Add Hover Effects
     */
    function addHoverEffects() {
        const interactiveElements = document.querySelectorAll(
            '.division-card-modern, .schedule-card, .quick-action-item'
        );

        interactiveElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            element.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }

    /**
     * Initialize Charts
     */
    function initCharts() {
        // Animate circular progress
        const circularProgress = document.querySelector('.circular-progress');
        if (circularProgress) {
            const percentage = parseFloat(circularProgress.dataset.percentage) || 0;
            animateCircularProgress(percentage);
        }

        // Create attendance trend chart if element exists
        const trendChart = document.getElementById('attendanceTrendChart');
        if (trendChart) {
            createAttendanceTrendChart(trendChart);
        }
    }

    /**
     * Animate Circular Progress
     */
    function animateCircularProgress(percentage) {
        const circle = document.querySelector('.progress-bar');
        if (!circle) return;

        const radius = 70;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference * (1 - percentage / 100);

        circle.style.strokeDasharray = circumference;
        circle.style.strokeDashoffset = circumference;

        setTimeout(() => {
            circle.style.transition = 'stroke-dashoffset 1.5s ease-in-out';
            circle.style.strokeDashoffset = offset;
        }, 500);
    }

    /**
     * Create Attendance Trend Chart
     */
    function createAttendanceTrendChart(canvas) {
        // Simple line chart implementation
        const ctx = canvas.getContext('2d');
        const data = [85, 88, 82, 90, 87, 92, 89];
        const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        
        drawLineChart(ctx, data, labels);
    }

    /**
     * Draw Line Chart
     */
    function drawLineChart(ctx, data, labels) {
        const canvas = ctx.canvas;
        const width = canvas.width;
        const height = canvas.height;
        const padding = 40;
        
        // Clear canvas
        ctx.clearRect(0, 0, width, height);
        
        // Calculate points
        const maxValue = Math.max(...data);
        const xStep = (width - 2 * padding) / (data.length - 1);
        const yScale = (height - 2 * padding) / maxValue;
        
        // Draw grid
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 5; i++) {
            const y = padding + (height - 2 * padding) * i / 5;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(width - padding, y);
            ctx.stroke();
        }
        
        // Draw line
        ctx.strokeStyle = '#667eea';
        ctx.lineWidth = 3;
        ctx.beginPath();
        
        data.forEach((value, index) => {
            const x = padding + index * xStep;
            const y = height - padding - value * yScale;
            
            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        
        ctx.stroke();
        
        // Draw points
        data.forEach((value, index) => {
            const x = padding + index * xStep;
            const y = height - padding - value * yScale;
            
            ctx.fillStyle = '#667eea';
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, 2 * Math.PI);
            ctx.fill();
        });
        
        // Draw labels
        ctx.fillStyle = '#64748b';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'center';
        
        labels.forEach((label, index) => {
            const x = padding + index * xStep;
            ctx.fillText(label, x, height - 10);
        });
    }

    /**
     * Initialize Notifications
     */
    function initNotifications() {
        // Check for new notifications
        checkNotifications();
        
        // Setup notification polling
        setInterval(checkNotifications, 60000); // Every minute
    }

    /**
     * Check Notifications
     */
    function checkNotifications() {
        // Simulate notification check
        const notificationBadge = document.querySelector('.notification-badge');
        if (notificationBadge) {
            // Update badge count
            const count = Math.floor(Math.random() * 5);
            if (count > 0) {
                notificationBadge.textContent = count;
                notificationBadge.style.display = 'block';
            } else {
                notificationBadge.style.display = 'none';
            }
        }
    }

    /**
     * Initialize Search
     */
    function initSearch() {
        const searchInput = document.getElementById('dashboardSearch');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(e.target.value);
            }, 300);
        });
    }

    /**
     * Perform Search
     */
    function performSearch(query) {
        if (!query) {
            showAllItems();
            return;
        }

        const searchableItems = document.querySelectorAll('[data-searchable]');
        const lowerQuery = query.toLowerCase();

        searchableItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(lowerQuery)) {
                item.style.display = '';
                highlightText(item, query);
            } else {
                item.style.display = 'none';
            }
        });
    }

    /**
     * Show All Items
     */
    function showAllItems() {
        const searchableItems = document.querySelectorAll('[data-searchable]');
        searchableItems.forEach(item => {
            item.style.display = '';
            removeHighlight(item);
        });
    }

    /**
     * Highlight Text
     */
    function highlightText(element, query) {
        // Simple text highlighting
        const text = element.textContent;
        const regex = new RegExp(`(${query})`, 'gi');
        const highlighted = text.replace(regex, '<mark>$1</mark>');
        element.innerHTML = highlighted;
    }

    /**
     * Remove Highlight
     */
    function removeHighlight(element) {
        const marks = element.querySelectorAll('mark');
        marks.forEach(mark => {
            mark.replaceWith(mark.textContent);
        });
    }

    /**
     * Initialize Filters
     */
    function initFilters() {
        const filterButtons = document.querySelectorAll('[data-filter]');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.dataset.filter;
                applyFilter(filter);
                
                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    /**
     * Apply Filter
     */
    function applyFilter(filter) {
        const items = document.querySelectorAll('[data-filter-category]');
        
        items.forEach(item => {
            if (filter === 'all' || item.dataset.filterCategory === filter) {
                item.style.display = '';
                item.classList.add('fade-in');
            } else {
                item.style.display = 'none';
            }
        });
    }

    /**
     * Initialize Tooltips
     */
    function initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', showTooltip);
            element.addEventListener('mouseleave', hideTooltip);
        });
    }

    /**
     * Show Tooltip
     */
    function showTooltip(e) {
        const text = e.target.dataset.tooltip;
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-modern';
        tooltip.textContent = text;
        tooltip.style.position = 'absolute';
        tooltip.style.zIndex = '9999';
        
        document.body.appendChild(tooltip);
        
        const rect = e.target.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        
        e.target._tooltip = tooltip;
    }

    /**
     * Hide Tooltip
     */
    function hideTooltip(e) {
        if (e.target._tooltip) {
            e.target._tooltip.remove();
            delete e.target._tooltip;
        }
    }

    /**
     * Initialize Modals
     */
    function initModals() {
        const modalTriggers = document.querySelectorAll('[data-modal]');
        
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const modalId = this.dataset.modal;
                openModal(modalId);
            });
        });

        // Close modal on backdrop click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                closeAllModals();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllModals();
            }
        });
    }

    /**
     * Open Modal
     */
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    /**
     * Close All Modals
     */
    function closeAllModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        });
        document.body.style.overflow = '';
    }

    /**
     * Setup Refresh
     */
    function setupRefresh() {
        const refreshBtn = document.getElementById('refreshDashboard');
        if (!refreshBtn) return;

        refreshBtn.addEventListener('click', function() {
            this.classList.add('rotating');
            
            // Simulate refresh
            setTimeout(() => {
                location.reload();
            }, 500);
        });
    }

    /**
     * Setup Real-Time Updates
     */
    function setupRealTimeUpdates() {
        // Check for updates every 5 minutes
        setInterval(() => {
            checkForUpdates();
        }, 300000);
    }

    /**
     * Check For Updates
     */
    function checkForUpdates() {
        // Simulate checking for updates
        console.log('Checking for updates...');
        
        // In production, this would make an AJAX call to check for new data
        // fetch('/api/teacher/dashboard/updates')
        //     .then(response => response.json())
        //     .then(data => updateDashboard(data));
    }

    /**
     * Setup Keyboard Shortcuts
     */
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('dashboardSearch');
                if (searchInput) searchInput.focus();
            }
            
            // Ctrl/Cmd + R for refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                const refreshBtn = document.getElementById('refreshDashboard');
                if (refreshBtn) refreshBtn.click();
            }
        });
    }

    /**
     * Show Toast Notification
     */
    window.showToast = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi bi-${getToastIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    /**
     * Get Toast Icon
     */
    function getToastIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    }

    /**
     * Format Date
     */
    window.formatDate = function(date, format = 'short') {
        const d = new Date(date);
        const options = format === 'short' 
            ? { month: 'short', day: 'numeric' }
            : { year: 'numeric', month: 'long', day: 'numeric' };
        return d.toLocaleDateString('en-US', options);
    };

    /**
     * Format Time
     */
    window.formatTime = function(time) {
        const [hours, minutes] = time.split(':');
        const h = parseInt(hours);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const hour12 = h % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    };

    /**
     * Debounce Function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Throttle Function
     */
    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Export utility functions
    window.TeacherDashboard = {
        showToast,
        formatDate,
        formatTime,
        debounce,
        throttle
    };

})();

// Add rotating animation for refresh button
const style = document.createElement('style');
style.textContent = `
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .rotating {
        animation: rotate 0.5s linear;
    }
    
    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 9999;
    }
    
    .toast.show {
        transform: translateX(0);
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .toast-success { border-left: 4px solid #43e97b; }
    .toast-error { border-left: 4px solid #f5576c; }
    .toast-warning { border-left: 4px solid #f59e0b; }
    .toast-info { border-left: 4px solid #4facfe; }
`;
document.head.appendChild(style);
