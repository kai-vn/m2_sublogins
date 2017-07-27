<?php
namespace SITC\Sublogins\Helper;

use Magento\Framework\App\Helper\Context;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_storeManager;
    protected $inlineTranslation;
    protected $_transportBuilder;
    protected $temp_id;
    protected $messageManager;
    protected $countryFactory;
    protected $regionFactory;
    protected $logger;
    protected $helper;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Psr\Log\LoggerInterface $logger,
        \SITC\Sublogins\Helper\Data $helper

    ){
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function sendEmail($data, $sendToEmail, $templateId)
    {
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);
        $this->inlineTranslation->suspend();
        $senderInfo = $this->helper->getSenderEmail();
        try{
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId()])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($sendToEmail)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return false;
        }
    }
}