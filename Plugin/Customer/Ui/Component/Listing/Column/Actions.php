<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Plugin\Customer\Ui\Component\Listing\Column;

use Magento\Customer\Ui\Component\Listing\Column\Actions as GridActions;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Actions
{
    protected $urlBuilder;
    protected $_customerRepositoryInterface;

    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function aroundPrepareDataSource(
        GridActions $subject,
        callable $prepareDataSource,
        array $dataSource
    )
    {
        $result = $prepareDataSource($dataSource);
        if (isset($result['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');
            foreach ($result['data']['items'] as &$item) {
                $customer = $this->_customerRepositoryInterface->getById($item['entity_id']);
                $customAttribute = $customer->getCustomAttribute('can_create_sub_login');
                if ($customAttribute && !empty($customer)) {
                    $canCreateSublogin = $customAttribute->getValue();
                    if ($canCreateSublogin == 1) {
                        $item[$subject->getData('name')]['sublogins'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'customer/index/new',
                                ['sub_parent_id' => $item['entity_id'], 'store' => $storeId]
                            ),
                            'label' => __('Create New Sub Account'),
                            'hidden' => false,
                        ];
                    }
                }
            }
        }
        return $result;
    }

}
