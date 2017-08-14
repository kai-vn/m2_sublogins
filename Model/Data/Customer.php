<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */
namespace SITC\Sublogins\Model\Data;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Customer extends \Magento\Customer\Model\Data\Customer implements
    \SITC\Sublogins\Api\Data\CustomerInterface
{
    public function __construct(\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, AttributeValueFactory $attributeValueFactory, \Magento\Customer\Api\CustomerMetadataInterface $metadataService, array $data = [])
    {
        parent::__construct($extensionFactory, $attributeValueFactory, $metadataService, $data);
    }


    public function getParent()
    {
        return $this->_get(self::PARENT);
    }

    public function setParent($parent)
    {
        return $this->setData(self::PARENT, $parent);
    }

    public function getExpireDate()
    {
        return $this->_get(self::EXPIRE_DATE);
    }

    public function setExpireDate($expireDate)
    {
        return $this->setData(self::EXPIRE_DATE, $expireDate);
    }
}
