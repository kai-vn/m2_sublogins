<?php

namespace SITC\Sublogins\Plugin\Sales\Controller\AbstractController;

use Magento\Sales\Controller\AbstractController\OrderViewAuthorization as SalesOrderViewAuthorization;
use Magento\Framework\App\ObjectManager;

class OrderViewAuthorization
{
    /**
     * Encryptor.
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $_encryptor;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;

    protected $customerRepository;
    protected $subloginsHelper;

    public function __construct(
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \SITC\Sublogins\Helper\Data $subloginsHelper
    )
    {
        $this->_encryptor = $encryptor;
        $this->customerSession = $customerSession;
        $this->orderConfig = $orderConfig;
        $this->customerRepository = $customerRepository;
        $this->subloginsHelper = $subloginsHelper;
    }

    public function aroundCanView(SalesOrderViewAuthorization $object, \Closure $proceed, \Magento\Sales\Model\Order $order)
    {
        $proceed($order);
        $customerSessionId = $this->customerSession->getCustomerId();
        $availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();
        $canViewSubOrder = $this->subloginsHelper->getCanViewOrder();
        $orderCustomerId = $order->getCustomerId();
        $customAttribute = $this->customerRepository->getById($orderCustomerId)->getCustomAttribute('sublogin_parent_id');
        if ($customAttribute) {
            $parentId = $customAttribute->getValue();
            if ($order->getId()
                && $orderCustomerId
                && $parentId
                && $parentId == $customerSessionId
                && in_array($order->getStatus(), $availableStatuses, true)
                && $canViewSubOrder
            ) {
                return true;
            }
        }
        return false;

    }

}
