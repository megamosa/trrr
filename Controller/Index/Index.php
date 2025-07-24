<?php
/**
 * MagoArab Order Tracking Index Controller
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

namespace MagoArab\OrderTracking\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use MagoArab\OrderTracking\Helper\Data as HelperData;

class Index extends Action
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
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        HelperData $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->helperData->isEnabled()) {
            $this->messageManager->addErrorMessage(__('Order tracking is currently disabled.'));
            return $this->_redirect('/');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($this->helperData->getTitle());
        
        return $resultPage;
    }
}