<?php
/**
 * MagoArab Order Tracking Block
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

namespace MagoArab\OrderTracking\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MagoArab\OrderTracking\Helper\Data as HelperData;
use MagoArab\OrderTracking\Model\Tracker;
use Magento\Framework\Session\SessionManagerInterface;

class Tracking extends Template
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var \MagoArab\OrderTracking\Helper\DataMask
     */
    protected $dataMask;

    /**
     * Tracking constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param Tracker $tracker
     * @param \MagoArab\OrderTracking\Helper\DataMask $dataMask
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        Tracker $tracker,
        \MagoArab\OrderTracking\Helper\DataMask $dataMask,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->tracker = $tracker;
        $this->dataMask = $dataMask;
        parent::__construct($context, $data);
    }

    /**
     * Get helper
     *
     * @return HelperData
     */
    public function getHelper()
    {
        return $this->helperData;
    }

    /**
     * Get helper data
     *
     * @return HelperData
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * Get tracker
     *
     * @return Tracker
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('ordertracking/order/track');
    }

    /**
     * Get form action URL (alternative method)
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('ordertracking/order/track');
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->helperData->getTitle();
    }

    /**
     * Get page description
     *
     * @return string
     */
    public function getPageDescription()
    {
        return $this->helperData->getDescription();
    }

    /**
     * Check if Order ID tracking is allowed
     *
     * @return bool
     */
    public function isOrderIdTrackingAllowed()
    {
        return $this->helperData->isOrderIdTrackingAllowed();
    }

    /**
     * Check if Email tracking is allowed
     *
     * @return bool
     */
    public function isEmailTrackingAllowed()
    {
        return $this->helperData->isEmailTrackingAllowed();
    }

    /**
     * Check if Mobile tracking is allowed
     *
     * @return bool
     */
    public function isMobileTrackingAllowed()
    {
        return $this->helperData->isMobileTrackingAllowed();
    }

    /**
     * Get order data
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        return $this->getData('order');
    }

    /**
     * Get orders data
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection|null
     */
    public function getOrders()
    {
        return $this->getData('orders');
    }

    /**
     * Get tracking type
     *
     * @return string
     */
    public function getTrackingType()
    {
        return $this->getData('tracking_type');
    }

    /**
     * Get tracking value
     *
     * @return string
     */
    public function getTrackingValue()
    {
        return $this->getData('tracking_value');
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->format($price);
    }

    /**
     * Format order date
     *
     * @param string $date
     * @return string
     */
    public function formatOrderDate($date)
    {
        return $this->_localeDate->formatDate($date, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Get order status label
     *
     * @param string $status
     * @return string
     */
    public function getOrderStatusLabel($status)
    {
        $statuses = [
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'complete' => __('Complete'),
            'closed' => __('Closed'),
            'canceled' => __('Canceled'),
            'holded' => __('On Hold'),
            'payment_review' => __('Payment Review')
        ];

        return isset($statuses[$status]) ? $statuses[$status] : ucfirst($status);
    }

    /**
     * Get session manager
     *
     * @return \Magento\Framework\Session\SessionManagerInterface
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Get masked email
     *
     * @param string $email
     * @return string
     */
    public function getMaskedEmail($email)
    {
        return $this->dataMask->maskEmail($email);
    }

    /**
     * Get masked phone
     *
     * @param string $phone
     * @return string
     */
    public function getMaskedPhone($phone)
    {
        return $this->dataMask->maskPhone($phone);
    }

    /**
     * Get masked address
     *
     * @param string $address
     * @return string
     */
    public function getMaskedAddress($address)
    {
        return $this->dataMask->maskAddress($address);
    }

    /**
     * Check if data should be masked for current tracking type
     *
     * @param string $dataType
     * @return bool
     */
    public function shouldMaskData($dataType)
    {
        $trackingType = $this->getTrackingType();
        
        // Don't mask data if user tracked by the same type
        if ($dataType === 'email' && $trackingType === 'email') {
            return false;
        }
        
        if ($dataType === 'phone' && $trackingType === 'mobile') {
            return false;
        }
        
        // Always mask sensitive data for security
        return true;
    }

    /**
     * Check if CAPTCHA is required
     *
     * @return bool
     */
    public function isCaptchaRequired()
    {
        $sessionCaptcha = $this->getSession()->getData('captcha_code');
        return !empty($sessionCaptcha);
    }

    /**
     * Get CAPTCHA code
     *
     * @return string
     */
    public function getCaptchaCode()
    {
        return $this->getSession()->getData('captcha_code');
    }

    /**
     * Generate Math CAPTCHA
     *
     * @return array
     */
    public function generateMathCaptcha()
    {
        $question = $this->getSession()->getData('math_captcha_question');
        $answer = $this->getSession()->getData('math_captcha_answer');
        
        if (!$question || !$answer) {
            // Generate new if not exists
            $mathCaptcha = $this->tracker->generateMathCaptcha();
            $this->getSession()->setData('math_captcha_answer', $mathCaptcha['answer']);
            $this->getSession()->setData('math_captcha_question', $mathCaptcha['question']);
            return $mathCaptcha;
        }
        
        return [
            'question' => $question,
            'answer' => $answer
        ];
    }
}