<?php

namespace SITC\Sublogins\Model\ResourceModel\Account;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = \SITC\Sublogins\Model\Account::ACCOUNT_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SITC\Sublogins\Model\Account', 'SITC\Sublogins\Model\ResourceModel\Account');
    }

}