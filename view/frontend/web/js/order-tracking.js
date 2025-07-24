/**
 * MagoArab Order Tracking JavaScript
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

define([
    'jquery',
    'mage/url',
    'mage/translate',
    'domReady!'
], function ($, urlBuilder, $t) {
    'use strict';

    return {
        /**
         * Initialize order tracking functionality
         */
        init: function(config) {
            this.config = config || {};
            this.bindEvents();
            this.addTrackButtons();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;
            
            // Handle track button clicks
            $(document).on('click', '.track-order', function(e) {
                e.preventDefault();
                self.handleTrackClick($(this));
            });

            // Handle form submission
            $(document).on('submit', '#order-tracking-form', function(e) {
                e.preventDefault();
                self.submitTrackingForm($(this));
            });
        },

        /**
         * Add track buttons to existing order rows
         */
        addTrackButtons: function() {
            var self = this;
            
            // For order history page
            $('.table-order-items tbody tr').each(function() {
                var $row = $(this);
                var $orderCell = $row.find('.col.id');
                var $actionsCell = $row.find('.col.actions');
                
                if ($orderCell.length && $actionsCell.length && !$actionsCell.find('.track-order').length) {
                    var $orderLink = $orderCell.find('a');
                    var orderIdText = $orderLink.text().trim();
                    var orderId = orderIdText.replace('#', '');
                    
                    if (orderId) {
                        var $trackButton = self.createTrackButton(orderId);
                        $actionsCell.append($trackButton);
                    }
                }
            });
        },

        /**
         * Create track button element
         */
        createTrackButton: function(orderId) {
            return $('<a>', {
                'class': 'action track-order',
                'href': '#',
                'data-order-id': orderId,
                'title': $t('Track Order'),
                'html': '<span>' + $t('Track Order') + '</span>'
            });
        },

        /**
         * Handle track button click
         */
        handleTrackClick: function($button) {
            var orderId = $button.data('order-id');
            
            if (!orderId) {
                this.showMessage($t('Order ID not found.'), 'error');
                return;
            }
            
            // Add loading state
            this.setButtonLoading($button, true);
            
            // Submit tracking request
            this.submitTrackingRequest(orderId, $button);
        },

        /**
         * Submit tracking request
         */
        submitTrackingRequest: function(orderId, $button) {
            var self = this;
            var trackingUrl = this.config.trackingUrl || urlBuilder.build('ordertracking/order/track');
            var formKey = $('input[name="form_key"]').val();
            
            // Create hidden form
            var $form = $('<form>', {
                method: 'POST',
                action: trackingUrl,
                style: 'display: none;'
            });
            
            // Add form fields
            $form.append($('<input>', {
                type: 'hidden',
                name: 'tracking_type',
                value: 'order_id'
            }));
            
            $form.append($('<input>', {
                type: 'hidden',
                name: 'tracking_value',
                value: orderId
            }));
            
            if (formKey) {
                $form.append($('<input>', {
                    type: 'hidden',
                    name: 'form_key',
                    value: formKey
                }));
            }
            
            // Submit form
            $('body').append($form);
            
            // Add small delay for better UX
            setTimeout(function() {
                $form.submit();
            }, 300);
        },

        /**
         * Set button loading state
         */
        setButtonLoading: function($button, loading) {
            if (loading) {
                $button.addClass('loading').prop('disabled', true);
                $button.find('span').text($t('Tracking...'));
            } else {
                $button.removeClass('loading').prop('disabled', false);
                $button.find('span').text($t('Track Order'));
            }
        },

        /**
         * Show message to user
         */
        showMessage: function(message, type) {
            type = type || 'info';
            
            // Remove existing messages
            $('.track-order-message').remove();
            
            // Create message element
            var $message = $('<div>', {
                'class': 'track-order-message ' + type,
                'text': message
            });
            
            // Add to page
            $('.page-title-wrapper').after($message);
            
            // Auto remove after 5 seconds
            setTimeout(function() {
                $message.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Submit tracking form (for manual tracking)
         */
        submitTrackingForm: function($form) {
            var self = this;
            var formData = $form.serialize();
            var submitUrl = $form.attr('action');
            
            // Show loading
            var $submitButton = $form.find('button[type="submit"]');
            $submitButton.prop('disabled', true).text($t('Tracking...'));
            
            // Submit via AJAX for better UX (optional)
            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success - redirect or show results
                    window.location.reload();
                },
                error: function() {
                    self.showMessage($t('An error occurred while tracking the order.'), 'error');
                    $submitButton.prop('disabled', false).text($t('Track Order'));
                }
            });
        },

        /**
         * Validate order ID format
         */
        validateOrderId: function(orderId) {
            // Basic validation - can be enhanced based on your order ID format
            return orderId && orderId.length > 0 && /^[0-9]+$/.test(orderId);
        },

        /**
         * Get tracking URL for order
         */
        getTrackingUrl: function(orderId) {
            return urlBuilder.build('ordertracking/order/track', {
                order_id: orderId
            });
        }
    };
});