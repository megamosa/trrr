define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        $(document).ready(function () {
            // Form validation
            $('#tracking-form').on('submit', function (e) {
                var trackingValue = $('#tracking_value').val();
                var trackingType = $('#tracking_type').val();
                
                if (!trackingValue) {
                    e.preventDefault();
                    alert('Please enter a tracking value');
                    return false;
                }
                
                // Validate email format if tracking type is email
                if (trackingType === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trackingValue)) {
                    e.preventDefault();
                    alert('Please enter a valid email address');
                    return false;
                }
                
                return true;
            });
        });
    };
});