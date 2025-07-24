<?php
/**
 * MagoArab Order Tracking Model
 *
 * @category  MagoArab
 * @package   MagoArab_OrderTracking
 * @author    MagoArab Team
 */

namespace MagoArab\OrderTracking\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Tracker extends AbstractModel
{
    protected $orderFactory;
    protected $orderCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        OrderFactory $orderFactory,
        OrderCollectionFactory $orderCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function trackByOrderId($orderId)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if ($order && $order->getId()) {
            return $order;
        }
        return null;
    }

    public function trackByEmail($email)
    {
        $collection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $email)
            ->setOrder('created_at', 'DESC');
            
        return $collection;
    }

    public function trackByMobile($mobile)
    {
        try {
            $collection = $this->orderCollectionFactory->create();
            
            $collection->getSelect()->joinLeft(
                ['order_address' => $collection->getTable('sales_order_address')],
                'main_table.entity_id = order_address.parent_id',
                []
            );
            
            $collection->addFieldToFilter(
                'order_address.telephone',
                ['like' => '%' . $mobile . '%']
            );
            
            $collection->getSelect()->group('main_table.entity_id');
            $collection->addAttributeToSelect('*')
                ->setOrder('created_at', 'DESC');
            
            return $collection;
        } catch (\Exception $e) {
            return $this->orderCollectionFactory->create();
        }
    }

    public function generateMathCaptcha()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operation = rand(0, 1) ? '+' : '-';
        
        if ($operation === '+') {
            $answer = $num1 + $num2;
            $question = "$num1 + $num2 = ?";
        } else {
            if ($num1 < $num2) {
                $temp = $num1;
                $num1 = $num2;
                $num2 = $temp;
            }
            $answer = $num1 - $num2;
            $question = "$num1 - $num2 = ?";
        }
        
        return [
            'question' => $question,
            'answer' => $answer
        ];
    }
}