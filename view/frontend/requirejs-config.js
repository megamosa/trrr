/**
 * MagoArab Order Tracking RequireJS Configuration
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

var config = {
    map: {
        '*': {
            'orderTracking': 'MagoArab_OrderTracking/js/order-tracking'
        }
    },
    paths: {
        'MagoArab_OrderTracking/js/order-tracking': 'MagoArab_OrderTracking/js/order-tracking'
    },
    shim: {
        'MagoArab_OrderTracking/js/order-tracking': {
            deps: ['jquery', 'mage/url', 'mage/translate']
        }
    }
};