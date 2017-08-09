<?php

namespace SITC\Sublogins\Plugin\Customer\Model\Customer;

use Magento\Customer\Model\Customer\DataProvider as CustomerDataProvider;
use Magento\Framework\App\ObjectManager;

class DataProvider
{
    protected $_request;
    protected $session;

    /**
     * Encryptor.
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $_encryptor;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Encryption\Encryptor $encryptor
    )
    {
        $this->_encryptor = $encryptor;
        $this->_request = $request;
    }

    public function afterGetData(CustomerDataProvider $subject, $result)
    {
        $subLoginParentId = $this->_request->getParam('sub_parent_id');
        if (!empty($subLoginParentId)) {
            $this->getSession()->setSubParentId($subLoginParentId);
        }
        return $result;
    }

    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get(
                \Magento\Framework\Session\SessionManagerInterface::class
            );
        }
        return $this->session;
    }
}
