<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Block;

use Magento\Customer\Model\Session as CustomerSession;

class SubAc extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;
    protected $helper;

    public function __construct(
        \SITC\Sublogins\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerSession $customerSession,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $canViewOrder = $this->helper->getCanViewOrder();
        if ($this->helper->isSublogin()) {
            return parent::_prepareLayout();
        }

        if ($this->helper->getCreateSublogin()) {
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

            if($canViewOrder) {
                $this->addChild(
                    'order-sub-account',
                    'Magento\Framework\View\Element\Html\Link\Current',
                    [
                        'label' => 'My Sub Account Order',
                        'path' => 'sublogins/order/history'
                    ]
                );
            }
        }
    }
}
