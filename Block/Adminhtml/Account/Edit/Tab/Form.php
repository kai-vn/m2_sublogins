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
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    )
    {
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
        $this->setTitle(__('Password Management'));
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
            ['legend' => __('Password Management'), 'class' => 'fieldset-wide']
        );


        $fieldset->addField(
            'password',
            'password',
            ['name' => 'password', 'label' => __('Password'), 'title' => __('Password'), 'required' => true]
        );

        $fieldset->addField(
            'password_confirmation',
            'password',
            ['name' => 'password', 'label' => __('ConfimPassword'), 'title' => __('Password'), 'required' => true]
        );


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