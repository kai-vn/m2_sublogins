<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Helper\Address;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount
{
    protected $helper;
    /** @var AccountManagementInterface */
    protected $accountManagement;

    /** @var Address */
    protected $addressHelper;

    /** @var FormFactory */
    protected $formFactory;

    /** @var SubscriberFactory */
    protected $subscriberFactory;

    /** @var RegionInterfaceFactory */
    protected $regionDataFactory;

    /** @var AddressInterfaceFactory */
    protected $addressDataFactory;

    /** @var Registration */
    protected $registration;

    /** @var CustomerInterfaceFactory */
    protected $customerDataFactory;

    /** @var CustomerUrl */
    protected $customerUrl;

    /** @var Escaper */
    protected $escaper;
    protected $url;
    /** @var CustomerExtractor */
    protected $customerExtractor;

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;
    protected $customerFactory;
    /** @var DataObjectHelper */
    protected $dataObjectHelper;
    protected $_customerSession;
    protected $date;
    /**
     * @var Session
     */
    protected $pageFactory;
    /**
     * @var AccountRedirect
     */
    private $accountRedirect;
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;
    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param FormFactory $formFactory
     * @param SubscriberFactory $subscriberFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param AddressInterfaceFactory $addressDataFactory
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param CustomerUrl $customerUrl
     * @param Registration $registration
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param AccountRedirect $accountRedirect
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \SITC\Sublogins\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        Address $addressHelper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        UrlFactory $urlFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        PageFactory $pageFactory,
        FormFactory $formFactory,
        SubscriberFactory $subscriberFactory,
        RegionInterfaceFactory $regionDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerUrl $customerUrl,
        Registration $registration,
        Escaper $escaper,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
        AccountRedirect $accountRedirect
    )
    {
        $this->helper = $helper;
        $this->date = $date;
        $this->pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->url = $url;
        $this->accountManagement = $accountManagement;
        $this->addressHelper = $addressHelper;
        $this->formFactory = $formFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->regionDataFactory = $regionDataFactory;
        $this->addressDataFactory = $addressDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerUrl = $customerUrl;
        $this->registration = $registration;
        $this->escaper = $escaper;
        $this->customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
        $this->dataObjectHelper = $dataObjectHelper;
        $this->accountRedirect = $accountRedirect;

        parent::__construct($context);
    }

    /**
     * Create customer account action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $canCreateSubLogin = $this->_customerSession->getCustomer()->getCanCreateSubLogin();
            if (!$canCreateSubLogin) {
                $this->messageManager->addError(__('Your account is not allowed to create sub-accounts.'));
                return $resultRedirect->setUrl($this->url->getUrl('customer/account'));
            }
            $parentId = $this->_customerSession->getCustomer()->getId();
            $countSubAccounts = $this->helper->getCountSubAccounts($parentId);
            $maxSublogins = $this->_customerSession->getCustomer()->getMaxSubLogins();
            if ($maxSublogins && $countSubAccounts + 1 > $maxSublogins) {
                $this->_customerSession->setCustomerFormData($this->getRequest()->getPostValue());
                $this->messageManager->addError(__('You cannot create more than %1 sub accounts for this customer.', $maxSublogins));
                return $resultRedirect->setUrl($this->url->getUrl('sublogins/create/account/'));
            }
            $address = $this->extractAddress();
            $addresses = $address === null ? [] : [$address];
            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
            $customer->setAddresses($addresses);
            $currentCustomerId = $this->_customerSession->getCustomer()->getId();
            $customer->setCustomAttribute('sublogin_parent_id', $currentCustomerId);
            $data = (int)$this->getRequest()->getPostValue('active');
            $customer->setCustomAttribute('is_sub_login', \SITC\Sublogins\Model\Config\Source\Customer\IsSubLogin::SUB_ACCOUNT_IS_SUB_LOGIN);
            $customer->setCustomAttribute('is_active_sublogin', $data);
            $dataDate = $this->getRequest()->getPostValue('expire_date');
            $dateFormat = $this->date->date('d-m-Y', $dataDate);
            $customer->setCustomAttribute('expire_date', $dateFormat);
            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $redirectUrl = $this->_customerSession->getBeforeAuthUrl();
            $this->checkPasswordConfirmation($password, $confirmation);
            $customer = $this->accountManagement
                ->createAccount($customer, $password, $redirectUrl);

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }

            $this->_eventManager->dispatch(
                'customer_register_success',
                ['account_controller' => $this, 'customer' => $customer]
            );

            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                // @codingStandardsIgnoreStart
                $this->messageManager->addSuccess(
                    __('You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.', $email));
                // @codingStandardsIgnoreEnd
            }

            $this->_customerSession->setCustomerFormData([]);
            $defaultUrl = $this->urlModel->getUrl('sublogins/account/listsubaccount', ['_secure' => true]);
            $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
            return $resultRedirect;
        } catch (StateException $e) {
            $url = $this->urlModel->getUrl('customer/account/forgotpassword');
            // @codingStandardsIgnoreStart
            $message = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
            // @codingStandardsIgnoreEnd
            $this->messageManager->addError($message);
        } catch (InputException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($this->escaper->escapeHtml($error->getMessage()));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError($this->escaper->escapeHtml($e->getMessage()));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t save the customer.'));
        }
        if(empty($this->_customerSession->setCustomerFormData($this->getRequest()->getPostValue()))) {
            $this->_customerSession->getCustomer()->getData();
        }
        $this->_customerSession->setCustomerFormData($this->getRequest()->getPostValue());
        $defaultUrl = $this->urlModel->getUrl('sublogins/create/account', ['_secure' => true]);
        $resultRedirect->setUrl($this->_redirect->error($defaultUrl));
        return $resultRedirect;
    }

    /**
     * Add address to customer during create account
     *
     * @return AddressInterface|null
     */
    protected function extractAddress()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }

        $addressForm = $this->formFactory->create('customer_address', 'customer_register_address');
        $allowedAttributes = $addressForm->getAllowedAttributes();

        $addressData = [];

        $regionDataObject = $this->regionDataFactory->create();
        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = $this->getRequest()->getParam($attributeCode);
            if ($value === null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id':
                    $regionDataObject->setRegionId($value);
                    break;
                case 'region':
                    $regionDataObject->setRegion($value);
                    break;
                default:
                    $addressData[$attributeCode] = $value;
            }
        }
        $addressDataObject = $this->addressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            $addressData,
            '\Magento\Customer\Api\Data\AddressInterface'
        );
        $addressDataObject->setRegion($regionDataObject);

        $addressDataObject->setIsDefaultBilling(
            $this->getRequest()->getParam('default_billing', false)
        )->setIsDefaultShipping(
            $this->getRequest()->getParam('default_shipping', false)
        );
        return $addressDataObject;
    }

    /**
     * Make sure that password and password confirmation matched
     *
     * @param string $password
     * @param string $confirmation
     * @return void
     * @throws InputException
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        if ($password != $confirmation) {
            throw new InputException(__('Please make sure your passwords match.'));
        }
    }

    protected function converToTz($dateTime = "", $toTz = '', $fromTz = '')
    {
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');
        return $dateTime;
    }

    protected function _getSession()
    {
        return $this->session;
    }
    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        $message = __('Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName());
        return $message;
    }
}
