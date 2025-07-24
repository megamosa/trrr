<?php
/**
 * Simple test script to verify Order Tracking functionality
 * 
 * This script tests the basic functionality of the Order Tracking module
 * to ensure the null reference error is fixed.
 */

namespace MagoArab\OrderTracking\Test;

class TrackingTest
{
    /**
     * Test Block instantiation and session handling
     */
    public function testBlockInstantiation()
    {
        try {
            // Test if we can create the block without errors
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            
            // Create session manager
            $sessionManager = $objectManager->get('\Magento\Framework\Session\SessionManagerInterface');
            
            // Create helper
            $helper = $objectManager->get('\MagoArab\OrderTracking\Helper\Data');
            
            // Create tracker
            $tracker = $objectManager->get('\MagoArab\OrderTracking\Model\Tracker');
            
            // Create data mask helper
            $dataMask = $objectManager->get('\MagoArab\OrderTracking\Helper\DataMask');
            
            // Create context
            $context = $objectManager->get('\Magento\Framework\View\Element\Template\Context');
            
            // Create the tracking block
            $trackingBlock = $objectManager->create(
                '\MagoArab\OrderTracking\Block\Tracking',
                [
                    'context' => $context,
                    'helperData' => $helper,
                    'tracker' => $tracker,
                    'dataMask' => $dataMask,
                    'sessionManager' => $sessionManager
                ]
            );
            
            echo "✓ Block instantiation successful\n";
            
            // Test session methods
            $session = $trackingBlock->getSession();
            if ($session) {
                echo "✓ Session manager properly injected\n";
            } else {
                echo "✗ Session manager not available\n";
                return false;
            }
            
            // Test CAPTCHA methods
            $isCaptchaRequired = $trackingBlock->isCaptchaRequired();
            echo "✓ CAPTCHA check method works (required: " . ($isCaptchaRequired ? 'yes' : 'no') . ")\n";
            
            // Test helper methods
            $isEnabled = $trackingBlock->isEnabled();
            echo "✓ Module enabled check works (enabled: " . ($isEnabled ? 'yes' : 'no') . ")\n";
            
            $title = $trackingBlock->getPageTitle();
            echo "✓ Page title retrieved: " . ($title ?: 'default') . "\n";
            
            return true;
            
        } catch (\Exception $e) {
            echo "✗ Error during block instantiation: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            return false;
        }
    }
    
    /**
     * Test form rendering without errors
     */
    public function testFormRendering()
    {
        try {
            echo "\n--- Testing Form Rendering ---\n";
            
            // This would typically be done in a proper test environment
            // For now, we just verify the template file exists and is readable
            $templatePath = __DIR__ . '/../view/frontend/templates/tracking/form.phtml';
            
            if (file_exists($templatePath)) {
                echo "✓ Template file exists\n";
                
                if (is_readable($templatePath)) {
                    echo "✓ Template file is readable\n";
                    
                    // Check for common syntax errors
                    $content = file_get_contents($templatePath);
                    if (strpos($content, 'getData()') === false) {
                        echo "✓ No direct getData() calls found in template\n";
                    } else {
                        echo "⚠ Warning: Direct getData() calls found in template\n";
                    }
                    
                    if (strpos($content, '$block->isCaptchaRequired()') !== false) {
                        echo "✓ Template uses proper CAPTCHA check method\n";
                    }
                    
                    if (strpos($content, '$block->getCaptchaCode()') !== false) {
                        echo "✓ Template uses proper CAPTCHA code method\n";
                    }
                    
                    if (strpos($content, 'getBlockHtml(\'formkey\')') !== false) {
                        echo "✓ Template includes CSRF protection\n";
                    }
                    
                } else {
                    echo "✗ Template file is not readable\n";
                    return false;
                }
            } else {
                echo "✗ Template file does not exist\n";
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            echo "✗ Error during form rendering test: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Run all tests
     */
    public function runAllTests()
    {
        echo "=== MagoArab Order Tracking Module Tests ===\n\n";
        
        $results = [];
        
        echo "--- Testing Block Instantiation ---\n";
        $results['block'] = $this->testBlockInstantiation();
        
        $results['form'] = $this->testFormRendering();
        
        echo "\n=== Test Results ===\n";
        $passed = 0;
        $total = count($results);
        
        foreach ($results as $test => $result) {
            echo $test . ": " . ($result ? "PASSED" : "FAILED") . "\n";
            if ($result) $passed++;
        }
        
        echo "\nOverall: {$passed}/{$total} tests passed\n";
        
        if ($passed === $total) {
            echo "\n🎉 All tests passed! The null reference error should be fixed.\n";
        } else {
            echo "\n⚠️  Some tests failed. Please check the issues above.\n";
        }
        
        return $passed === $total;
    }
}

// Auto-run tests if this file is executed directly
if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === 'TrackingTest.php') {
    $test = new TrackingTest();
    $test->runAllTests();
}