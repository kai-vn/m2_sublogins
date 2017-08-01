<?php

namespace SITC\Sublogins\Block;

use Magento\Framework\View\Element\Template;

class ListSub extends Template
{
    protected $_template = 'listsub.phtml';
    protected $_customerSession;
    protected $_customerCollectionFactory;
    protected $subAccounts;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */

    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = [])
    {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Sub-Account List'));
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');

    }

    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getSubAccounts()
    {
        if (!$this->subAccounts) {
            $customerId = $this->_customerSession->getCustomerId();
            $this->subAccounts = $this->_customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_sub_login', 1)
                ->addAttributeToFilter('sublogin_parent_id', $customerId);
        }
        return $this->subAccounts;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getSubAccounts()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sitc.sublogins.record.pager'
                )
                ->setAvailableLimit([1 => 1, 2 => 2, 3 => 3])
                ->setShowPerPage(true)
                ->setCollection($this->getSubAccounts());

            $this->setChild('pager', $pager);

            $this->getSubAccounts()->load();
        }
        return $this;
    }

}
