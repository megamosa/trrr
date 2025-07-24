<?php
/**
 * MagoArab Order Tracking Helper
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

namespace MagoArab\OrderTracking\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'magoarab_ordertracking/general/enabled';
    const XML_PATH_TITLE = 'magoarab_ordertracking/general/title';
    const XML_PATH_DESCRIPTION = 'magoarab_ordertracking/general/description';
    const XML_PATH_ALLOW_ORDER_ID = 'magoarab_ordertracking/tracking_options/allow_order_id';
    const XML_PATH_ALLOW_EMAIL = 'magoarab_ordertracking/tracking_options/allow_email';
    const XML_PATH_ALLOW_MOBILE = 'magoarab_ordertracking/tracking_options/allow_mobile';
	const XML_PATH_ENABLE_CAPTCHA = 'magoarab_ordertracking/tracking_options/enable_captcha';
    const XML_PATH_CAPTCHA_THRESHOLD = 'magoarab_ordertracking/tracking_options/captcha_threshold';
	const XML_PATH_MAX_ATTEMPTS = 'magoarab_ordertracking/tracking_options/max_attempts';
	const XML_PATH_LOCKOUT_DURATION = 'magoarab_ordertracking/tracking_options/lockout_duration';
	const XML_PATH_MOBILE_VALIDATION = 'magoarab_ordertracking/tracking_options/mobile_validation';
	const XML_PATH_MOBILE_MIN_LENGTH = 'magoarab_ordertracking/tracking_options/mobile_min_length';
	const XML_PATH_MOBILE_MAX_LENGTH = 'magoarab_ordertracking/tracking_options/mobile_max_length';
	const XML_PATH_MOBILE_ALLOWED_FORMATS = 'magoarab_ordertracking/tracking_options/mobile_allowed_formats';
    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get page title
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get page description
     *
     * @param int|null $storeId
     * @return string
     */
    public function getDescription($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if order ID tracking is allowed
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOrderIdTrackingAllowed($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ALLOW_ORDER_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if email tracking is allowed
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEmailTrackingAllowed($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ALLOW_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if mobile tracking is allowed
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isMobileTrackingAllowed($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ALLOW_MOBILE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
	/**
 * Check if CAPTCHA is enabled
 *
 * @param int|null $storeId
 * @return bool
 */
public function isCaptchaEnabled($storeId = null)
{
    return (bool)$this->scopeConfig->getValue(
        self::XML_PATH_ENABLE_CAPTCHA,
        ScopeInterface::SCOPE_STORE,
        $storeId
    );
}

/**
 * Get CAPTCHA threshold
 *
 * @param int|null $storeId
 * @return int
 */
public function getCaptchaThreshold($storeId = null)
{
    return (int)$this->scopeConfig->getValue(
        self::XML_PATH_CAPTCHA_THRESHOLD,
        ScopeInterface::SCOPE_STORE,
        $storeId
    ) ?: 3;
}
public function getMaxAttempts($storeId = null)
{
    return (int)$this->scopeConfig->getValue(
        self::XML_PATH_MAX_ATTEMPTS,
        ScopeInterface::SCOPE_STORE,
        $storeId
    ) ?: 5;
}

public function getLockoutDuration($storeId = null)
{
    return (int)$this->scopeConfig->getValue(
        self::XML_PATH_LOCKOUT_DURATION,
        ScopeInterface::SCOPE_STORE,
        $storeId
    ) ?: 15;
}
/**
 * Check if mobile validation is enabled
 *
 * @param int|null $storeId
 * @return bool
 */
public function isMobileValidationEnabled($storeId = null)
{
    return (bool)$this->scopeConfig->getValue(
        self::XML_PATH_MOBILE_VALIDATION,
        ScopeInterface::SCOPE_STORE,
        $storeId
    );
}

/**
 * Get minimum mobile length
 *
 * @param int|null $storeId
 * @return int
 */
public function getMobileMinLength($storeId = null)
{
    return (int)$this->scopeConfig->getValue(
        self::XML_PATH_MOBILE_MIN_LENGTH,
        ScopeInterface::SCOPE_STORE,
        $storeId
    ) ?: 10;
}

/**
 * Get maximum mobile length
 *
 * @param int|null $storeId
 * @return int
 */
public function getMobileMaxLength($storeId = null)
{
    return (int)$this->scopeConfig->getValue(
        self::XML_PATH_MOBILE_MAX_LENGTH,
        ScopeInterface::SCOPE_STORE,
        $storeId
    ) ?: 15;
}

/**
 * Get allowed mobile formats
 *
 * @param int|null $storeId
 * @return array
 */
public function getAllowedMobileFormats($storeId = null)
{
    $formats = $this->scopeConfig->getValue(
        self::XML_PATH_MOBILE_ALLOWED_FORMATS,
        ScopeInterface::SCOPE_STORE,
        $storeId
    );
    
    return $formats ? explode(',', $formats) : ['digits_only', 'international', 'local'];
}

/**
 * Validate mobile number
 *
 * @param string $mobile
 * @return array
 */
public function validateMobileNumber($mobile)
{
    $result = ['valid' => true, 'message' => ''];
    
    if (!$this->isMobileValidationEnabled()) {
        return $result;
    }
    
    // Remove all non-digit characters for length validation
    $digitsOnly = preg_replace('/[^\d]/', '', $mobile);
    $digitCount = strlen($digitsOnly);
    
    // Check minimum length
    if ($digitCount < $this->getMobileMinLength()) {
        $result['valid'] = false;
        $result['message'] = __('Mobile number must be at least %1 digits.', $this->getMobileMinLength());
        return $result;
    }
    
    // Check maximum length
    if ($digitCount > $this->getMobileMaxLength()) {
        $result['valid'] = false;
        $result['message'] = __('Mobile number cannot exceed %1 digits.', $this->getMobileMaxLength());
        return $result;
    }
    
    // Check format
    $allowedFormats = $this->getAllowedMobileFormats();
    $formatValid = false;
    
    foreach ($allowedFormats as $format) {
        if ($this->checkMobileFormat($mobile, $format)) {
            $formatValid = true;
            break;
        }
    }
    
    if (!$formatValid) {
        $result['valid'] = false;
        $result['message'] = __('Mobile number format is not valid. Please use one of the allowed formats.');
        return $result;
    }
    
    return $result;
}

/**
 * Check mobile format
 *
 * @param string $mobile
 * @param string $format
 * @return bool
 */
private function checkMobileFormat($mobile, $format)
{
    switch ($format) {
        case 'digits_only':
            return preg_match('/^\d+$/', $mobile);
            
        case 'international':
            return preg_match('/^\+\d+$/', $mobile);
            
        case 'local':
            return preg_match('/^\d{3}-\d{3}-\d{4}$/', $mobile) || 
                   preg_match('/^\d+$/', $mobile);
            
        case 'spaces':
            return preg_match('/^\d{3}\s\d{3}\s\d{4}$/', $mobile) || 
                   preg_match('/^\d+$/', $mobile);
            
        case 'brackets':
            return preg_match('/^\(\d{3}\)\s\d{3}-\d{4}$/', $mobile) || 
                   preg_match('/^\d+$/', $mobile);
            
        default:
            return true;
    }
}
}