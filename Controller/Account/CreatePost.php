<?php
/**
 * @copyright Copyright (c) 2017 www.tigren.com
 */

namespace SITC\Sublogins\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlFactory;
use Magento\Framework\Exception\StateException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePost extends \Magento\Customer\Controller\AbstractAccount
{
    /** @var AccountManagementInterface */
    protected $accountManagement;
    protected $helper;
    /** @var SubscriberFactory */
    protected $subscriberFactory;
    /** @var CustomerUrl */
    protected $customerUrl;
    /** @var Escaper */
    protected $escaper;
    /** @var CustomerExtractor */
    protected $customerExtractor;
    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;
    protected $_customerSession;
    protected $pageFactory;
    protected $date;
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
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $accountManagement
     * @param UrlFactory $urlFactory
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerUrl $customerUrl
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param AccountRedirect $accountRedirect
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \SITC\Sublogins\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        UrlFactory $urlFactory,
        PageFactory $pageFactory,
        SubscriberFactory $subscriberFactory,
        CustomerUrl $customerUrl,
        Escaper $escaper,
        CustomerExtractor $customerExtractor
    )
    {
        $this->date = $date;
        $this->helper = $helper;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerUrl = $customerUrl;
        $this->escaper = $escaper;
        $this->_customerSession = $customerSession;
        $this->customerExtractor = $customerExtractor;
        $this->urlModel = $urlFactory->create();
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
                return $resultRedirect->setUrl($this->_url->getUrl('customer/account'));
            }
            $parentId = $this->_customerSession->getCustomer()->getId();
            $countSubAccounts = $this->helper->getCountSubAccounts($parentId);
            $maxSublogins = $this->_customerSession->getCustomer()->getMaxSubLogins();
            if ($maxSublogins && $countSubAccounts + 1 > $maxSublogins) {
                $this->_customerSession->setCustomerFormData($this->getRequest()->getPostValue());
                $this->messageManager->addError(__('You cannot create more than %1 sub accounts for this customer.', $maxSublogins));
                return $resultRedirect->setUrl($this->_url->getUrl('sublogins/create/account/'));
            }
            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
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
