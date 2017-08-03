<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SITC\Sublogins\Api\Data;

/**
 * Customer interface.
 * @api
 */
interface CustomerInterface extends \Magento\Customer\Api\Data\CustomerInterface
{
    const PARENT = 'parent';
    const EXPIRE_DATE = 'expire_date';
    /**
     * Get parent id
     *
     * @return string|null
     */
    public function getParent();

    /**
     * Set parent id
     *
     * @param string $parent
     * @return $this
     */
    public function setParent($parent);

    /**
     * Get expire date
     *
     * @return string|null
     */
    public function getExpireDate();

    /**
     * Set expire date
     *
     * @param string $expireDate
     * @return $this
     */
    public function setExpireDate($expireDate);
}
