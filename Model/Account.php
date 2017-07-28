<?php

namespace SITC\Sublogins\Model;

use Magento\Framework\Model\AbstractModel;

class Account extends AbstractModel
{
    const ACCOUNT_ID = 'account_id'; // We define the id fieldname

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sublogins'; // parent value is 'core_abstract'

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'account'; // parent value is 'object'

    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = self::ACCOUNT_ID; // parent value is 'id'

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('SITC\Sublogins\Model\ResourceModel\Account');
    }

}