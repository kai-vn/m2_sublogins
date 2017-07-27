<?php
namespace SITC\Sublogins\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Account post mysql resource
 */
class Account extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('sitc_sublogins_info', 'account_id');
    }

}