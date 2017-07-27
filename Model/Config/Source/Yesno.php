<?php

namespace SITC\Sublogins\Model\Config\Source;

class Yesno implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [];
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getYesnoOptions()
    {
        $options = [
            '1' => __('Yes'),
            '0' => __('No'),
        ];

        $this->_options = $options;
        return $this->_options;
    }
}
