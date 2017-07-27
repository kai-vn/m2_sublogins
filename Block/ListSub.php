<?php
namespace SITC\Sublogins\Block;

use \Magento\Framework\View\Element\Template;

class ListSub extends Template
{
    private $_objectManager;
    protected $_parrent;
    protected $_template = 'listsub.phtml';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $filterProvider;
    protected $coreRegistry;
    protected $_customerSession;
    protected $_customerRepository;
    /**
     * @param Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */

    public function __construct(
        Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customer_Repository,
        \SITC\Sublogins\Model\Parrent $parrent,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        array $data = [])
    {
        $this->customer_Repository = $customer_Repository;
        $this->_objectManager = $objectmanager;
        $this->_parrent = $parrent;
        $this->scopeConfig = $context->getScopeConfig();
        $this->filterProvider = $filterProvider;
        $this->coreRegistry = $registry;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $collection = $this->_parrent->getCollection();
        $this->setCollection($collection);
        $this->pageConfig->getTitle()->set(__('My Sub-Account List'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sitc.sublogins.record.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }

    /**
     * @return string
     */
    // method for get pager html
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}
