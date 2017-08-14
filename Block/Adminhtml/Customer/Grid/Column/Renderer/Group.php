<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */

namespace SITC\Sublogins\Block\Adminhtml\Customer\Grid\Column\Renderer;

class Group extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    protected $_customerGroup;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        array $data = []
    )
    {
        $this->_customerGroup = $customerGroup;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $groupId = $row->getGroupId();
        $groupLabel = '';
        $customerGroups = $this->_customerGroup->toOptionArray();
        array_unshift($customerGroups, array('value' => '', 'label' => 'Any'));
        foreach ($customerGroups as $group) {
            if ($groupId == $group['value']) {
                $groupLabel = $group['label'];
            }
        }

        return $groupLabel;
    }
}