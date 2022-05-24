<?php

namespace MageSuite\UiBookmarkCleaner\Service;

class UiBookmarkCleaner
{
    public const UI_BOOKMARK_USER_ID_FIELD = 'user_id';
    public const UI_BOOKMARK_IDENTIFIER_FIELD = 'identifier';
    public const UI_BOOKMARK_CURRENT_FIELD = 'current';

    protected \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $bookmarkCollectionFactory;

    public function __construct(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $bookmarkCollectionFactory)
    {
        $this->bookmarkCollectionFactory = $bookmarkCollectionFactory;
    }

    public function execute(int $adminUserId, bool $removeAll = true)
    {
        $bookmarksCollection = $this->bookmarkCollectionFactory->create();
        $bookmarksCollection->addFieldToFilter(self::UI_BOOKMARK_USER_ID_FIELD, ['eq' => $adminUserId]);
        if (!$removeAll) {
            $bookmarksCollection->addFieldToFilter(
                [self::UI_BOOKMARK_IDENTIFIER_FIELD, self::UI_BOOKMARK_CURRENT_FIELD],
                [
                    ['attribute' => self::UI_BOOKMARK_IDENTIFIER_FIELD, 'in' => ['current', 'default']],
                    ['attribute' => self::UI_BOOKMARK_CURRENT_FIELD, 'eq' => 1]
                ]
            );
        }

        $bookmarksCollection->walk('delete');
    }
}
