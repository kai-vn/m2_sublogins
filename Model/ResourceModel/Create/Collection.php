<?php
namespace SITC\Sublogins\Model\ResourceModel\Create;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{


    protected function _construct()
    {
        $this->_init('SITC\Sublogins\Model\Create', 'SITC\Sublogins\Model\ResourceModel\Create');
    }

}