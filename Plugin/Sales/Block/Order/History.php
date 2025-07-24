<?php
/**
 * MagoArab Order Tracking Plugin for Order History
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

namespace MagoArab\OrderTracking\Plugin\Sales\Block\Order;

use Magento\Framework\UrlInterface;
use MagoArab\OrderTracking\Helper\Data as HelperData;

class History
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * History constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param HelperData $helperData
     */
    public function __construct(
        UrlInterface $urlBuilder,
        HelperData $helperData
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helperData = $helperData;
    }

    /**
     * Add track order button to order history actions
     *
     * @param \Magento\Sales\Block\Order\History $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(\Magento\Sales\Block\Order\History $subject, $result)
    {
        // Check if order tracking is enabled
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        // Get orders from the block
        $orders = $subject->getOrders();
        if (!$orders || $orders->getSize() == 0) {
            return $result;
        }

        // Add JavaScript and CSS for the track buttons
        $trackingScript = $this->getTrackingScript();
        
        // Inject the script before closing body tag or at the end
        $result .= $trackingScript;

        return $result;
    }

    /**
     * Get tracking script and styles
     *
     * @return string
     */
    protected function getTrackingScript()
    {
        $trackingUrl = $this->urlBuilder->getUrl('ordertracking/order/track');
        
        return '
        <script type="text/javascript">
        require(["MagoArab_OrderTracking/js/order-tracking"], function(orderTracking) {
            orderTracking.init({
                trackingUrl: "' . $trackingUrl . '",
                enabled: true
            });
        });
        </script>
        ';
    }
}