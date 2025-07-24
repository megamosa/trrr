<?php
/**
 * MagoArab Order Tracking Controller
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

namespace MagoArab\OrderTracking\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use MagoArab\OrderTracking\Helper\Data as HelperData;
use MagoArab\OrderTracking\Model\Tracker;
use Psr\Log\LoggerInterface;

class Track extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Tracker
     */
    protected $tracker;
    
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \MagoArab\OrderTracking\Model\SecurityLogger
     */
    protected $securityLogger;

    /**
     * Track constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param HelperData $helperData
     * @param Tracker $tracker
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \MagoArab\OrderTracking\Model\SecurityLogger $securityLogger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        HelperData $helperData,
        Tracker $tracker,
        LoggerInterface $logger,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \MagoArab\OrderTracking\Model\SecurityLogger $securityLogger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
        $this->tracker = $tracker;
        $this->_logger = $logger;
        $this->session = $session;
        $this->remoteAddress = $remoteAddress;
        $this->securityLogger = $securityLogger;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Order Tracking'));
        
        // Generate math CAPTCHA for email and mobile tracking
        $this->generateMathCaptcha();
        
        $post = $this->getRequest()->getPostValue();
        if ($post) {
            // Rate limiting logic
            $ipAddress = $this->getRequest()->getClientIp();
            $currentTime = time();
            $sessionKey = 'tracking_attempts_' . md5($ipAddress);
            $attempts = $this->session->getData($sessionKey) ?: [];
            
            // Clean old attempts (older than 1 hour)
            $attempts = array_filter($attempts, function($attempt) use ($currentTime) {
                return ($currentTime - $attempt['time']) < 3600;
            });
            
            // Count failed attempts in last hour
            $failedAttempts = array_filter($attempts, function($attempt) {
                return isset($attempt['failed']) && $attempt['failed'];
            });
            
            // Check rate limit
            if (count($failedAttempts) >= $this->helperData->getMaxAttempts()) {
                $this->messageManager->addErrorMessage(__('Too many failed attempts. Please try again later.'));
                return $resultPage;
            }
            
            $trackingType = $post['tracking_type'] ?? '';
            $trackingValue = $post['tracking_value'] ?? '';
            
            if (!$trackingValue) {
                $this->messageManager->addErrorMessage(__('Please enter a valid tracking value.'));
                return $resultPage;
            }
            
            $order = null;
            $orders = null;
            
            switch ($trackingType) {
                case 'order_id':
                    if (!$this->helperData->isOrderIdTrackingAllowed()) {
                        $this->messageManager->addErrorMessage(__('Order ID tracking is not allowed.'));
                        return $resultPage;
                    }
                    $order = $this->tracker->trackByOrderId($trackingValue);
                    if (!$order) {
                        $attempts[] = [
                            'ip' => $ipAddress,
                            'time' => $currentTime,
                            'type' => $trackingType,
                            'value' => substr($trackingValue, 0, 3) . '***',
                            'failed' => true
                        ];
                        $this->session->setData($sessionKey, $attempts);
                        
                        $this->securityLogger->logSuspiciousActivity([
                            'ip' => $ipAddress,
                            'user_agent' => $this->getRequest()->getHeader('User-Agent'),
                            'tracking_type' => $trackingType,
                            'tracking_value_partial' => substr($trackingValue, 0, 3) . '***',
                            'attempt_count' => count($failedAttempts) + 1,
                            'reason' => 'Order not found'
                        ]);
                        
                        $this->messageManager->addErrorMessage(__('No order found with this ID.'));
                        return $resultPage;
                    }
                    break;
                
                case 'email':
                    if (!$this->helperData->isEmailTrackingAllowed()) {
                        $this->messageManager->addErrorMessage(__('Email tracking is not allowed.'));
                        return $resultPage;
                    }
                    
                    // Check math CAPTCHA
                    $mathAnswer = $post['math_captcha'] ?? '';
                    $sessionAnswer = $this->session->getData('math_captcha_answer');
                    
                    if (empty($mathAnswer) || (int)$mathAnswer !== (int)$sessionAnswer) {
                        $this->messageManager->addErrorMessage(__('Please solve the math problem correctly.'));
                        // Generate new CAPTCHA
                        $this->generateMathCaptcha();
                        return $resultPage;
                    }
                    
                    $orders = $this->tracker->trackByEmail($trackingValue);
                    if ($orders->getSize() == 0) {
                        $this->messageManager->addErrorMessage(__('No orders found with this email.'));
                        $this->generateMathCaptcha();
                        return $resultPage;
                    }
                    break;
                
                case 'mobile':
                    if (!$this->helperData->isMobileTrackingAllowed()) {
                        $this->messageManager->addErrorMessage(__('Mobile number tracking is not allowed.'));
                        return $resultPage;
                    }
                    
                    // Check math CAPTCHA
                    $mathAnswer = $post['math_captcha'] ?? '';
                    $sessionAnswer = $this->session->getData('math_captcha_answer');
                    
                    if (empty($mathAnswer) || (int)$mathAnswer !== (int)$sessionAnswer) {
                        $this->messageManager->addErrorMessage(__('Please solve the math problem correctly.'));
                        // Generate new CAPTCHA
                        $this->generateMathCaptcha();
                        return $resultPage;
                    }
                    
                    $mobileValidation = $this->helperData->validateMobileNumber($trackingValue);
                    if (!$mobileValidation['valid']) {
                        $this->messageManager->addErrorMessage($mobileValidation['message']);
                        return $resultPage;
                    }
                    
                    $orders = $this->tracker->trackByMobile($trackingValue);
                    if ($orders->getSize() == 0) {
                        $this->messageManager->addErrorMessage(__('No orders found with this mobile number.'));
                        $this->generateMathCaptcha();
                        return $resultPage;
                    }
                    break;
                
                default:
                    $this->messageManager->addErrorMessage(__('Invalid tracking type.'));
                    return $resultPage;
            }
            
            // Clear math CAPTCHA after successful submission
            $this->session->unsetData('math_captcha_answer');
            $this->session->unsetData('math_captcha_question');
            
            // Set data for the view
            if ($order) {
                $trackingBlock = $resultPage->getLayout()->getBlock('magoarab.ordertracking');
                if ($trackingBlock) {
                    $trackingBlock->setData('order', $order)
                        ->setData('tracking_type', $trackingType)
                        ->setData('tracking_value', $trackingValue);
                }
            } elseif ($orders) {
                $trackingBlock = $resultPage->getLayout()->getBlock('magoarab.ordertracking');
                if ($trackingBlock) {
                    $trackingBlock->setData('orders', $orders)
                        ->setData('tracking_type', $trackingType)
                        ->setData('tracking_value', $trackingValue);
                }
            }
        }

        return $resultPage;
    }

    private function generateMathCaptcha()
    {
        $mathCaptcha = $this->tracker->generateMathCaptcha();
        $this->session->setData('math_captcha_answer', $mathCaptcha['answer']);
        $this->session->setData('math_captcha_question', $mathCaptcha['question']);
        return $mathCaptcha;
    }
}