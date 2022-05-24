<?php

namespace MageSuite\UiBookmarkCleaner\Test\Integration\Service;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class UiBookmarkCleanerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \MageSuite\UiBookmarkCleaner\Service\UiBookmarkCleaner
     */
    protected $uiBookmarkCleaner;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->collectionFactory = $this->objectManager->create(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class);
        $this->userFactory = $this->objectManager->get(\Magento\User\Model\UserFactory::class);

        $this->uiBookmarkCleaner = $this->objectManager->create(\MageSuite\UiBookmarkCleaner\Service\UiBookmarkCleaner::class);
    }

    /**
     * @magentoDataFixture MageSuite_UiBookmarkCleaner::Test/_files/ui_bookmarks.php
     */
    public function testRemoveAllBookmarks()
    {
        $admin1 = $this->userFactory->create()->loadByUsername('TestAdmin1');
        $admin2 = $this->userFactory->create()->loadByUsername('TestAdmin2');

        $admin1BookmarksList = $this->getBookmarkList($admin1->getId());
        $this->assertNotEmpty($admin1BookmarksList->getItems());
        $admin2BookmarksList = $this->getBookmarkList($admin2->getId());
        $this->assertNotEmpty($admin2BookmarksList->getItems());

        $this->uiBookmarkCleaner->execute($admin1->getId(), true);

        $admin1BookmarksList = $this->getBookmarkList($admin1->getId());
        $this->assertEmpty($admin1BookmarksList->getItems());
        $admin2BookmarksList = $this->getBookmarkList($admin2->getId());
        $this->assertNotEmpty($admin2BookmarksList->getItems());
    }

    /**
     * @magentoDataFixture MageSuite_UiBookmarkCleaner::Test/_files/ui_bookmarks.php
     */
    public function testRemoveCurrentBookmarks()
    {
        $admin1 = $this->userFactory->create()->loadByUsername('TestAdmin1');

        $admin1BookmarksList = $this->getBookmarkList($admin1->getId());
        $this->assertNotEmpty($admin1BookmarksList->getItems());

        $this->uiBookmarkCleaner->execute($admin1->getId(), false);

        $admin1BookmarksList = $this->getBookmarkList($admin1->getId());
        $this->assertEquals(1, $admin1BookmarksList->getSize());
    }

    protected function getBookmarkList($adminId): \Magento\Ui\Model\ResourceModel\Bookmark\Collection
    {
        $bookmarksCollection = $this->collectionFactory->create();
        $bookmarksCollection->addFieldToFilter(\MageSuite\UiBookmarkCleaner\Service\UiBookmarkCleaner::UI_BOOKMARK_USER_ID_FIELD, ['eq' => $adminId]);

        return $bookmarksCollection;
    }
}
