<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Block;

class SubAc extends \Magento\Framework\View\Element\Template
{

    protected $helper;

    public function __construct(
        \SITC\Sublogins\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        if ($this->helper->isSublogin()) {
            $this->addChild(
                'list-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'List Sub Account',
                    'path' => 'sublogins/account/listsubaccount'
                ]
            );

        } elseif ($this->helper->getIdSub()) {
            $this->addChild(
                'create-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'Create Sub Account',
                    'path' => 'sublogins/create/account'
                ]
            );
            $this->addChild(
                'list-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'List Sub Account',
                    'path' => 'sublogins/account/listsubaccount'
                ]
            );
            $this->addChild(
                'order-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'My Sub Account Order',
                    'path' => 'sublogins/order/history'
                ]
            );
        } elseif (!$this->helper->getIdSub()) {
            $this->addChild(
                'create-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'Create Sub Account',
                    'path' => 'sublogins/create/account'
                ]
            );
            $this->addChild(
                'list-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'List Sub Account',
                    'path' => 'sublogins/account/listsubaccount'
                ]
            );
            $this->addChild(
                'order-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'My Sub Account Order',
                    'path' => 'sublogins/order/history'
                ]
            );
        } elseif ($this->helper->getCreateSublogin()) {
            $this->addChild(
                'create-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'Create Sub Account',
                    'path' => 'sublogins/create/account'
                ]
            );
            $this->addChild(
                'list-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'List Sub Account',
                    'path' => 'sublogins/account/listsubaccount'
                ]
            );
            $this->addChild(
                'order-sub-account',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'label' => 'My Sub Account Order',
                    'path' => 'sublogins/order/history'
                ]
            );
        }
        return parent::_prepareLayout();
    }
}
