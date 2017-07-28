<?php

namespace SITC\Sublogins\Block\Adminhtml\Account\Edit\Tab;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_status;
    protected $_yesNo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \SITC\Sublogins\Model\Config\Source\IsActive $status,
        \SITC\Sublogins\Model\Config\Source\Yesno $yesNo,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
        $this->_yesNo = $yesNo;
        $this->_status = $status;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
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
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('account_form');
        $this->setTitle(__('Account Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \SITC\Sublogins\Model\Account $model */
        $model = $this->_coreRegistry->registry('sublogins_account');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('account_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
            'prefix',
            'text',
            ['name' => 'prefix', 'label' => __('Prefix'), 'title' => __('Prefix'), 'required' => true]
        );
        $fieldset->addField(
            'lastname',
            'text',
            ['name' => 'lastname', 'label' => __('Lastname'), 'title' => __('Lastname'), 'required' => true]
        );
        $fieldset->addField(
            'firstname',
            'text',
            ['name' => 'firstname', 'label' => __('FirstName'), 'title' => __('FirstName'), 'required' => true]
        );
        $fieldset->addField(
            'password',
            'text',
            ['name' => 'password', 'label' => __('Password'), 'title' => __('Password'), 'required' => true]
        );
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $this->_yesNo->getYesnoOptions()
            ]
        );
        $fieldset->addField(
            'subscribe',
            'select',
            [
                'name' => 'subscribe',
                'label' => __('Subscribe'),
                'title' => __('Subscribe'),

                'values' => $this->_yesNo->getYesnoOptions()
            ]
        );
        $fieldset->addField(
            'address',
            'textarea',
            ['name' => 'address', 'label' => __('Address'), 'title' => __('Address')]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
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