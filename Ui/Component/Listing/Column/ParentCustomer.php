<?php
namespace SITC\Sublogins\Ui\Component\Listing\Column;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class ParentCustomer extends Column
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    protected $_customerRepositoryInterface;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ScopeConfigInterface $scopeConfig,
        array $components,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        array $data
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->getFieldLabel($item);
            }
        }
        return $dataSource;
    }

    /**
     * Retrieve field label
     *
     * @param array $item
     * @return string
     */
    private function getFieldLabel(array $item)
    {
        $parentId = $item[$this->getData('name')];
        if($parentId) {
            $parent = $this->_customerRepositoryInterface->getById($parentId);
            $name = $parent->getFirstname() . ' ' . $parent->getLastname();

            return $name;
        }
        return '';

    }

}
