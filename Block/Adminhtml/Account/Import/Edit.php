<?php

namespace SITC\Sublogins\Block\Adminhtml\Account\Import;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import');
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $importUrl = $this->getUrl('sublogins/account/start');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Import'));

        $this->_objectId = 'import_id';
        $this->_blockGroup = 'SITC_Sublogins';
        $this->_controller = 'adminhtml_account_import';
    }
}
