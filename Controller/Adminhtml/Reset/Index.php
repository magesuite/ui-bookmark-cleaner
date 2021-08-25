<?php

namespace MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset;

class Index extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const UI_BOOKMARK_USER_ID_FIELD = 'user_id';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $bookmarkCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $bookmarkCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $bookmarkCollectionFactory
    )
    {
        parent::__construct($context);
        $this->session = $session;
        $this->bookmarkCollectionFactory = $bookmarkCollectionFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute(): \Magento\Backend\Model\View\Result\Redirect
    {
        try {
            $userId = (int)$this->session->getUser()->getId();
            $bookmarksList = $this->bookmarkCollectionFactory->create()
                ->addFieldToFilter(self::UI_BOOKMARK_USER_ID_FIELD, ['eq' => $userId]);
            $bookmarksList->walk('delete');

            $this->messageManager->addSuccessMessage(__('Grid Search has been reset successfully'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred during resetting Search Grid'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setUrl($this->_redirect->getRefererUrl());

        return $redirect;
    }
}
