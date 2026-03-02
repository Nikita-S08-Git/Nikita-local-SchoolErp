/**
 * Holiday Validation Module
 * 
 * Handles date blocking and holiday validation for:
 * - Attendance marking
 * - Timetable creation
 * 
 * Features:
 * - AJAX holiday checking
 * - Date picker disabling
 * - Error message display
 */

(function() {
    'use strict';

    // Store holiday dates globally
    window.HolidayValidator = {
        holidayDates: {},
        academicYearId: null,

        /**
         * Initialize holiday validator
         * @param {number|null} academicYearId - Academic year ID
         */
        init: function(academicYearId = null) {
            this.academicYearId = academicYearId;
            this.loadHolidayDates();
        },

        /**
         * Load holiday dates from server
         */
        loadHolidayDates: function() {
            const self = this;
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1);
            
            const endDate = new Date();
            endDate.setMonth(endDate.getMonth() + 13);

            fetch(`/api/holidays/range?start_date=${this.formatDate(startDate)}&end_date=${this.formatDate(endDate)}${this.academicYearId ? '&academic_year_id=' + this.academicYearId : ''}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    self.holidayDates = data.holidays || {};
                    self.applyHolidayBlocking();
                }
            })
            .catch(error => console.error('Error loading holidays:', error));
        },

        /**
         * Check if a specific date is a holiday
         * @param {string} date - Date in YYYY-MM-DD format
         * @returns {Promise<Object>}
         */
        checkDate: function(date) {
            const self = this;
            
            return fetch(`/academic/holidays/check-date?date=${date}${this.academicYearId ? '&academic_year_id=' + this.academicYearId : ''}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                return {
                    isHoliday: data.is_holiday || false,
                    title: data.title || null,
                    type: data.type || null
                };
            })
            .catch(error => {
                console.error('Error checking holiday:', error);
                return { isHoliday: false, title: null, type: null };
            });
        },

        /**
         * Apply holiday blocking to date pickers
         */
        applyHolidayBlocking: function() {
            const self = this;

            // Initialize flatpickr for date inputs
            document.querySelectorAll('input[data-toggle="date"], input[type="date"][data-block-holidays]').forEach(function(input) {
                if (input._flatpickr) {
                    input._flatpickr.destroy();
                }

                const disableFunc = function(date) {
                    const dateStr = self.formatDate(date);
                    return self.holidayDates[dateStr] === true;
                };

                flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    disable: disableFunc,
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr) {
                        self.validateSelectedDate(dateStr, input);
                    }
                });
            });
        },

        /**
         * Validate selected date on change
         * @param {string} dateStr - Selected date string
         * @param {HTMLElement} input - Input element
         */
        validateSelectedDate: function(dateStr, input) {
            const self = this;
            
            if (!dateStr) return;

            self.checkDate(dateStr).then(result => {
                if (result.isHoliday) {
                    // Clear the input
                    if (input) {
                        input.value = '';
                        if (input._flatpickr) {
                            input._flatpickr.clear();
                        }
                    }

                    // Show error message
                    self.showError(input, `Cannot select ${result.title} (${result.type}). Attendance and Timetable cannot be added on holidays.`);
                    
                    // Trigger form validation
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    // Clear any previous error
                    self.clearError(input);
                }
            });
        },

        /**
         * Show error message for input
         * @param {HTMLElement} input - Input element
         * @param {string} message - Error message
         */
        showError: function(input, message) {
            this.clearError(input);

            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block holiday-error';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle"></i> ${message}`;

            if (input.parentNode) {
                input.parentNode.appendChild(errorDiv);
            }
            
            input.classList.add('is-invalid');
        },

        /**
         * Clear error message for input
         * @param {HTMLElement} input - Input element
         */
        clearError: function(input) {
            input.classList.remove('is-invalid');
            
            const existingError = input.parentNode?.querySelector('.holiday-error');
            if (existingError) {
                existingError.remove();
            }
        },

        /**
         * Format date to YYYY-MM-DD
         * @param {Date} date - Date object
         * @returns {string}
         */
        formatDate: function(date) {
            const d = new Date(date);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        /**
         * Validate form before submit
         * @param {HTMLFormElement} form - Form element
         * @returns {Promise<boolean>}
         */
        validateForm: async function(form) {
            const dateInputs = form.querySelectorAll('input[type="date"][data-block-holidays], input[data-toggle="date"]');
            
            for (const input of dateInputs) {
                const dateStr = input.value;
                if (!dateStr) continue;

                const result = await this.checkDate(dateStr);
                if (result.isHoliday) {
                    this.showError(input, `Cannot proceed: ${result.title} falls on this date. Attendance and Timetable cannot be added.`);
                    input.focus();
                    return false;
                }
            }
            
            return true;
        }
    };

    /**
     * Initialize on DOM ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Get academic year ID from meta tag or data attribute
        const academicYearMeta = document.querySelector('meta[name="academic-year-id"]');
        const academicYearId = academicYearMeta?.content || 
                               document.querySelector('[data-academic-year-id]')?.dataset?.academicYearId ||
                               null;

        // Initialize validator
        window.HolidayValidator.init(academicYearId);

        // Add form submit validation
        document.querySelectorAll('form[data-validate-holidays]').forEach(function(form) {
            form.addEventListener('submit', async function(e) {
                const isValid = await window.HolidayValidator.validateForm(form);
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    });

    /**
     * AJAX Helper for attendance marking
     */
    window.AttendanceHolidayCheck = {
        /**
         * Check holiday before marking attendance
         * @param {string} date - Date to check
         * @param {number} divisionId - Division ID
         * @returns {Promise<Object>}
         */
        checkBeforeMark: function(date, divisionId) {
            return fetch('/academic/attendance/check-holiday', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ date, division_id: divisionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.is_holiday) {
                    return {
                        valid: false,
                        message: data.message || 'Cannot mark attendance on holiday',
                        holidayTitle: data.holiday_title,
                        holidayType: data.holiday_type
                    };
                }
                return { valid: true };
            });
        }
    };

    /**
     * AJAX Helper for timetable creation
     */
    window.TimetableHolidayCheck = {
        /**
         * Check holiday before creating timetable
         * @param {string} date - Date to check
         * @returns {Promise<Object>}
         */
        checkBeforeCreate: function(date) {
            return fetch('/academic/timetable/ajax/check-holiday', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ date })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'holiday') {
                    return {
                        valid: false,
                        message: data.message || 'Cannot create timetable on holiday',
                        holidayTitle: data.holiday_title,
                        holidayType: data.holiday_type
                    };
                }
                return { valid: true };
            });
        }
    };

})();
