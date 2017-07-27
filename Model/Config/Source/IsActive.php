<?php



namespace SITC\Sublogins\Model\Config\Source;

class IsActive implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Active')],
            ['value' => 0, 'label' => __('InActive')]
        ];
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function getStatusOptions($flag = false)
    {
        $options = [];

        if ($flag) {
            $options[''] = '-- Status --';
        }

        $options[self::STATUS_DISABLED] = __('InActive');
        $options[self::STATUS_ENABLED] = __('Active');

        $this->_options = $options;
        return $this->_options;
    }
}
