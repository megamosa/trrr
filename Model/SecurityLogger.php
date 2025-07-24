<?php
namespace MagoArab\OrderTracking\Model;

use Psr\Log\LoggerInterface;

class SecurityLogger
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logSuspiciousActivity($data)
    {
        $this->logger->warning('Order Tracking Suspicious Activity', [
            'ip' => $data['ip'] ?? 'unknown',
            'user_agent' => $data['user_agent'] ?? 'unknown',
            'tracking_type' => $data['tracking_type'] ?? 'unknown',
            'tracking_value_partial' => $data['tracking_value_partial'] ?? 'unknown',
            'attempt_count' => $data['attempt_count'] ?? 0,
            'timestamp' => date('Y-m-d H:i:s'),
            'reason' => $data['reason'] ?? 'unknown'
        ]);
    }

    public function logRateLimitExceeded($data)
    {
        $this->logger->critical('Order Tracking Rate Limit Exceeded', [
            'ip' => $data['ip'] ?? 'unknown',
            'user_agent' => $data['user_agent'] ?? 'unknown',
            'attempt_count' => $data['attempt_count'] ?? 0,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function logCaptchaFailed($data)
    {
        $this->logger->warning('Order Tracking CAPTCHA Failed', [
            'ip' => $data['ip'] ?? 'unknown',
            'user_agent' => $data['user_agent'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}