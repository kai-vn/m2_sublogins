<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
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
                $canCreateSubloginAttribute = $customer->getCustomAttribute('can_create_sub_login');
                $isSubloginAttribute = $customer->getCustomAttribute('is_sub_login');
                if ($canCreateSubloginAttribute && !empty($customer)) {
                    $canCreateSublogin = $canCreateSubloginAttribute->getValue();
                    if ($canCreateSublogin == 1) {
                        $item[$subject->getData('name')]['sublogins'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'sublogins/account/new',
                                ['sub_parent_id' => $item['entity_id'], 'store' => $storeId]
                            ),
                            'label' => __('Create New Sub Account'),
                            'hidden' => false,
                        ];
                    }
                }

                if ($isSubloginAttribute && !empty($customer)) {
                    $isSublogin = $isSubloginAttribute->getValue();
                    if ($isSublogin == 1) {
                        $item[$subject->getData('name')]['edit'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'sublogins/account/edit',
                                ['id' => $item['entity_id'], 'store' => $storeId]
                            ),
                            'label' => __('Edit'),
                            'hidden' => false,
                        ];
                    }
                }


            }
        }
        return $result;
    }
}
