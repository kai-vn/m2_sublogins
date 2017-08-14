<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */

namespace SITC\Sublogins\Block\Adminhtml\Customer\Grid\Column\Renderer;

use Magento\Store\Model\StoreManagerInterface as StoreManager;

class Website extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $storeManager;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManager $storeManager,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        return $this->storeManager->getWebsite($row->getWebsiteId())->getName();
    }
}