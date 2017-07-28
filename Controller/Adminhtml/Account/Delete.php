<?php

namespace SITC\Sublogins\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $_model;

    /**
     * @param Action\Context $context
     * @param \SITC\Sublogins\Model\Account $model
     */
    public function __construct(
        Action\Context $context,
        \SITC\Sublogins\Model\Account $model
    )
    {
        parent::__construct($context);
        $this->_model = $model;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Account deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Account does not exist'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('SITC_Sublogins::account_delete');
    }
}