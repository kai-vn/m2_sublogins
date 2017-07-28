<?php

namespace SITC\Sublogins\Cron;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class EmailExpired
{
    protected $customerCollectionFactory;
    protected $helper;
    protected $email;

    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        \SITC\Sublogins\Helper\Data $helper,
        \SITC\Sublogins\Helper\Email $email
    )
    {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->helper = $helper;
        $this->email = $email;
    }

    public function execute()
    {
        $collection = $this->customerCollectionFactory->create()
            ->addAttributeToSelect('expire_date', true)
            ->addAttributeToFilter('is_sub_login', 1);
        $templateExpired = $this->helper->getEmailExpired();
        foreach ($collection as $customer) {
            $date = new \DateTime($customer->getExpireDate());
            if ($date < new \DateTime('today')) {
                $customerName = ['name' => $customer->getName()];
                $sendEmail = $this->email->sendEmail($customerName, $customer->getEmail(), $templateExpired);
                if ($sendEmail == true) {
                    $result = "\n Sent email success to " . $customer->getEmail() . "\n";
                    echo $result;
                } else {
                    $result = "\n Could not send email to " . $customer->getEmail() . "\n";
                    echo $result;
                }

            }
        }
    }

}