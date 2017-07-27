<?php

namespace SITC\Sublogins\Block\Adminhtml\Account;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class ListSubAccounts extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $customerCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CustomerCollectionFactory $customerCollectionFactory,
        array $data = []
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sublogins_account_grid');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->customerCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addAttributeToSelect('expire_date', true)
            ->addAttributeToFilter('is_sub_login', 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'width' => '20px',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]);

        $this->addColumn(
            'sub_parent_id',
            [
                'header' => __('Parent Id of Sub-Account'),
                'index' => 'sublogin_parent_id'
            ]
        );

        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'index' => 'firstname',
                'type' => 'text',
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'index' => 'lastname',
                'type' => 'text',
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'group',
            [
                'header' => __('Group'),
                'index' => 'group',
                'renderer' => 'SITC\Sublogins\Block\Adminhtml\Customer\Grid\Column\Renderer\Group'
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
                'type' => 'text',
                'header_css_class' => 'col-mail',
                'column_css_class' => 'col-mail'
            ]);

        $this->addColumn(
            'website',
            [
                'header' => __('Website'),
                'index' => 'group',
                'renderer' => 'SITC\Sublogins\Block\Adminhtml\Customer\Grid\Column\Renderer\Website'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Customer Since'),
                'index' => 'created_at',
                'type' => 'date'
            ]
        );

        $this->addColumn(
            'expire_date',
            [
                'header' => __('Expire Date'),
                'index' => 'expire_date',
                'type' => 'date'
            ]
        );


        $this->addColumn(
            'action',
            [
                'header' => ' ',
                'filter' => false,
                'sortable' => false,
                'width' => '100px',
                'renderer' => 'SITC\Sublogins\Block\Adminhtml\Customer\Grid\Column\Renderer\Action'
            ]
        );


        return parent::_prepareColumns();
    }

}