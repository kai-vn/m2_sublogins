<?php
namespace SITC\Sublogins\Model\ResourceModel\Parrent;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init('SITC\Sublogins\Model\Parrent', 'SITC\Sublogins\Model\ResourceModel\Parrent');
    }

}