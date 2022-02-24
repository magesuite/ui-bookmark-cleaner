<?php

namespace MageSuite\UiBookmarkCleaner\Test\Integration\Controller\Adminhtml\Reset;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\User\Model\User
     */
    protected $userFactory;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $redirect;

    protected function setUp(): void
    {
        parent::setUp();
        $this->session = $this->_objectManager->get(\Magento\Backend\Model\Auth\Session::class);
        $this->collectionFactory = $this->_objectManager->get(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class);
        $this->userFactory = $this->_objectManager->get(\Magento\User\Model\UserFactory::class);
        $this->redirect = $this->_objectManager->get(\Magento\Framework\App\Response\RedirectInterface::class);
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadAdmins
     * @magentoDataFixture loadUiBookmarks
     */
    public function testItRemovesUiBookmarks()
    {
        /** @var \Magento\User\Model\User $user */
        $admin1 = $this->userFactory->create()->loadByUsername('TestAdmin1');
        $admin1BookmarksList = $this->getBookmarkList($admin1->getId());
        $this->assertNotEmpty($admin1BookmarksList->getItems());

        $admin2 = $this->userFactory->create()->loadByUsername('TestAdmin2');
        $admin2BookmarksList = $this->getBookmarkList($admin2->getId());
        $this->assertNotEmpty($admin2BookmarksList->getItems());

        $this->session->setUser($admin1);
        $this->session->processLogin();
        $this->dispatch('backend/bookmark/reset/index');
        $this->assertSessionMessages(
            $this->stringStartsWith('Grid Search has been reset successfully'),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains($this->redirect->getRefererUrl()));
        $this->session->processLogout();

        /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $admin1AfterResetBookmarksList */
        $admin1AfterResetBookmarksList = $this->getBookmarkList($admin1->getId());
        $admin2AfterResetBookmarksList = $this->getBookmarkList($admin2->getId());
        $this->assertEmpty($admin1AfterResetBookmarksList->getItems());
        $this->assertNotEmpty($admin2AfterResetBookmarksList->getItems());
    }

    /**
     * @param \PHPUnit\Framework\Constraint\Constraint $constraint
     * @param string|null $messageType
     * @param string $messageManagerClass
     */
    public function assertSessionMessages(
        \PHPUnit\Framework\Constraint\Constraint $constraint,
        $messageType = null,
        $messageManagerClass = \Magento\Framework\Message\Manager::class
    ) {
        $this->_assertSessionErrors = false;

        $messages = $this->getMessages($messageType, $messageManagerClass);
        $this->assertThat(
            $messages[0],
            $constraint,
            'Session messages do not meet expectations ' . var_export($messages, true)
        );
    }

    protected function getBookmarkList($adminId): \Magento\Ui\Model\ResourceModel\Bookmark\Collection
    {
        /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(
                \MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset\Index::UI_BOOKMARK_USER_ID_FIELD,
                ['eq' => $adminId]
            );

        return $collection;
    }

    public static function loadAdmins()
    {
        include __DIR__.'/../../../../_files/admin_users.php';
    }

    public static function loadUiBookmarks()
    {
        include __DIR__.'/../../../../_files/ui_bookmarks.php';
    }
}
