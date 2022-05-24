<?php

namespace MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset;

class Current extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const UI_BOOKMARK_USER_ID_FIELD = 'user_id';

    protected \Magento\Backend\Model\Auth\Session $session;

    protected \MageSuite\UiBookmarkCleaner\Service\UiBookmarkCleaner $uiBookmarkCleaner;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $session,
        \MageSuite\UiBookmarkCleaner\Service\UiBookmarkCleaner $uiBookmarkCleaner
    ) {
        parent::__construct($context);

        $this->session = $session;
        $this->uiBookmarkCleaner = $uiBookmarkCleaner;
    }

    public function execute(): \Magento\Backend\Model\View\Result\Redirect
    {
        try {
            $adminUserId = (int)$this->session->getUser()->getId();

            $this->uiBookmarkCleaner->execute($adminUserId, false);

            $this->messageManager->addSuccessMessage(__('Current Bookmarks has been reset successfully'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred during resetting Bookmarks'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setUrl($this->_redirect->getRefererUrl());

        return $redirect;
    }
}
