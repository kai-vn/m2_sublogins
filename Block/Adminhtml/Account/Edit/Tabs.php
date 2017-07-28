<?php

namespace SITC\Sublogins\Block\Adminhtml\Account\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('account_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Account Information'));
    }
}
