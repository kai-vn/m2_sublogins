<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Block\Adminhtml\Customer\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class OrderButton
 */
class SubloginButton extends \Magento\Customer\Block\Adminhtml\Edit\GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    protected $_customerRepositoryInterface;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    )
    {
        $this->authorization = $context->getAuthorization();
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $data = [];
        if ($customerId) {
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $customAttribute = $customer->getCustomAttribute('can_create_sub_login');
            if (!empty($customAttribute)) {
                $canCreateSublogin = $customAttribute->getValue();
                if ($canCreateSublogin == 1) {
                    $data = [
                        'label' => __('Create Sub-Account'),
                        'on_click' => sprintf("location.href = '%s';", $this->getCreateSubUrl()),
                        'class' => 'add',
                        'sort_order' => 40,
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getCreateSubUrl()
    {
        return $this->getUrl('sublogins/account/new', ['sub_parent_id' => $this->getCustomerId()]);
    }
}
