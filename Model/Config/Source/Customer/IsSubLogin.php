<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Model\Config\Source\Customer;

class IsSubLogin implements \Magento\Framework\Option\ArrayInterface
{
    const SUB_ACCOUNT_IS_SUB_LOGIN = 1;
    const SUB_ACCOUNT_IS_NOT_SUB_LOGIN = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::SUB_ACCOUNT_IS_SUB_LOGIN,
                'label' => __('Yes')
            ],
            [
                'value' => self::SUB_ACCOUNT_IS_NOT_SUB_LOGIN,
                'label' => __('NO')
            ]
        ];

        return $options;
    }
}
