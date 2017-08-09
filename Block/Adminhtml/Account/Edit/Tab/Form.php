<?php

namespace SITC\Sublogins\Block\Adminhtml\Account\Edit\Tab;

use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Controller\RegistryConstants;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_yesNo;
    protected $_fieldFactory;
    /**
     * Customer Repository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param array $data
     */
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Yesno $yesNo,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
        $this->_customerRepository = $customerRepository;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->_fieldFactory = $fieldFactory;
        $this->_yesNo = $yesNo;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getTabLabel()
    {
        return __('Account Informations');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Account Informations');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Init form
     *
     * @return void

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $yesnoSource = $this->_yesNo->toOptionArray();
        $fieldMaps = [];
        $dependenceBlock = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        );
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __(''), 'class' => 'fieldset-wide']
        );

        $fieldMaps['checkbox'] = $fieldset->addField(
            'checkbox',
            'select',
            [
                'name' => 'checkbox',
                'label' => __('Change Password'),
                'title' => __('Change Password'),
                'required' => true,
                'value' => $yesnoSource
            ]
        );
        $fieldset->addField(
            'sub_parent_id',
            'hidden',
            [
                'name' => 'sub_parent_id',
                'label' => __('Sub Parent ID'),
                'title' => __('Sub Parent ID'),
                'required' => true,
                'value' => $this->getRequest()->getParam('sub_parent_id')
            ]
        );
        $fieldMaps['change_password'] = $fieldset->addField(
            'password_hash',
            'password',
            ['name' => 'password', 'label' => __('Password'), 'title' => __('Password'), 'required' => true]
        );
        $fieldMaps['change_password'] = $fieldset->addField(
            'password_confirmation',
            'password',
            ['name' => 'password_confirmation', 'label' => __('Confirm Password'), 'title' => __('Confirm Password'), 'required' => true]
        );
        foreach ($fieldMaps as $fieldMap) {
            $dependenceBlock->addFieldMap($fieldMap->getHtmlId(), $fieldMap->getName());
        }
        $mappingFieldDependence = $this->getMappingFieldDependence();
        /*
        * Add field dependence
        */
        foreach ($mappingFieldDependence as $dependence) {
            $negative = isset($dependence['negative']) && $dependence['negative'];
            if (is_array($dependence['fieldName'])) {
                foreach ($dependence['fieldName'] as $fieldName) {
                    $dependenceBlock->addFieldDependence(
                        $fieldMaps[$fieldName]->getName(),
                        $fieldMaps[$dependence['fieldNameFrom']]->getName(),
                        $this->getDependencyField($dependence['refField'], $negative)
                    );
                }
            } else {
                $dependenceBlock->addFieldDependence(
                    $fieldMaps[$dependence['fieldName']]->getName(),
                    $fieldMaps[$dependence['fieldNameFrom']]->getName(),
                    $this->getDependencyField($dependence['refField'], $negative)
                );
            }
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getMappingFieldDependence()
    {
        return [
            [
                'fieldName' => ['checkbox'],
                'fieldNameFrom' => 'change_password',
                'refField' => '0,1',
            ],

        ];
    }

    public function getDependencyField($refField, $negative = false, $separator = ',', $fieldPrefix = '')
    {
        return $this->_fieldFactory->create(
            ['fieldData' => ['value' => (string)$refField, 'negative' => $negative, 'separator' => $separator], 'fieldPrefix' => $fieldPrefix]
        );
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}