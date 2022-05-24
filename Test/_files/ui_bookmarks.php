<?php

require 'admin_users.php';

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Ui\Api\BookmarkRepositoryInterface $uiBookmarkRepository */
$uiBookmarkRepository = $objectManager->create(\Magento\Ui\Api\BookmarkRepositoryInterface::class);

/** @var \Magento\User\Model\User $user */
$user = $objectManager->create(\Magento\User\Model\User::class);
$admin1 = $user->loadByUsername('TestAdmin1');
$admin1Id = $admin1->getId();

$admin2 = $user->loadByUsername('TestAdmin2');
$admin2Id = $admin2->getId();

$uiBookmarksData = [
    [
        'user_id' => $admin2Id,
        'namespace' => 'sales_order_grid',
        'identifier' => 'default',
        'current' => 1,
        'created_at' => '2020-07-31 12:12:12',
        'updated_at' => '2020-07-31 12:12:12'
    ],
    [
        'user_id' => $admin1Id,
        'namespace' => 'sales_order_invoice_grid',
        'identifier' => 'current',
        'current' => 0,
        'created_at' => '2020-07-31 12:12:12',
        'updated_at' => '2020-07-31 12:12:12'
    ],
    [
        'user_id' => $admin2Id,
        'namespace' => 'sales_order_shipment_grid',
        'identifier' => 'current',
        'current' => 0,
        'created_at' => '2020-07-31 12:12:12',
        'updated_at' => '2020-07-31 12:12:12'
    ],
    [
        'user_id' => $admin2Id,
        'namespace' => 'product_listing',
        'identifier' => 'default',
        'current' => 1,
        'created_at' => '2020-07-31 12:12:12',
        'updated_at' => '2020-07-31 12:12:12'
    ],
    [
        'user_id' => $admin1Id,
        'namespace' => 'customer_listing',
        'identifier' => 'default',
        'current' => 1,
        'created_at' => '2020-07-31 12:12:12',
        'updated_at' => '2020-07-31 12:12:12'
    ],
    [
        'user_id' => $admin1Id,
        'namespace' => 'product_listing',
        'identifier' => '_1653291353275',
        'current' => 0,
        'created_at' => '2020-07-31 12:12:12',
        'updated_at' => '2020-07-31 12:12:12'
    ],
];

foreach ($uiBookmarksData as $uiBookmarkData) {
    /** @var \Magento\Ui\Model\Bookmark $uiBookmark */
    $uiBookmark = $objectManager->create(\Magento\Ui\Model\Bookmark::class);
    $uiBookmark->setUserId($uiBookmarkData['user_id'])
        ->setNamespace($uiBookmarkData['namespace'])
        ->setIdentifier($uiBookmarkData['identifier'])
        ->setCurrent($uiBookmarkData['current'])
        ->setCreatedAt($uiBookmarkData['created_at'])
        ->setUpdatedAt($uiBookmarkData['updated_at']);

    $uiBookmarkRepository->save($uiBookmark);
}
