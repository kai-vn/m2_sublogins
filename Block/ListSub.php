<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
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

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');

    }

    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Sub-Account List'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getSubAccounts()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sitc.sublogins.record.pager'
            )
                ->setAvailableLimit([10 => 10])
                ->setShowPerPage(true)
                ->setCollection($this->getSubAccounts());

            $this->setChild('pager', $pager);

            $this->getSubAccounts()->load();
        }
        return $this;
    }

    public function getSubAccounts()
    {
        if (!$this->subAccounts) {
            $customerId = $this->_customerSession->getCustomerId();
            $this->subAccounts = $this->_customerCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_sub_login', \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN)
                ->addAttributeToFilter('sublogin_parent_id', $customerId);
        }
        return $this->subAccounts;
    }

}
