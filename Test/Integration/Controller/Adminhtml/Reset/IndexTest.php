<?php

namespace MageSuite\UiBookmarkCleaner\Test\Integration\Controller\Adminhtml\Reset;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManager\ConfigLoaderInterface
     */
    protected $configLoader;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Backend\Model\Session\AdminConfig
     */
    protected $sessionConfig;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Security\Model\AdminSessionsManager
     */
    protected $adminSessionManager;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $redirect;

    public function setUp(): void
    {
        parent::setUp();

        $this->state = $this->_objectManager->get(\Magento\Framework\App\State::class);
        $this->request = $this->_objectManager->get(\Magento\Framework\App\Request\Http::class);
        $this->configLoader = $this->_objectManager->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $this->session = $this->_objectManager->get(\Magento\Backend\Model\Auth\Session::class);
        $this->cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->sessionConfig = $this->_objectManager->get(\Magento\Backend\Model\Session\AdminConfig::class);
        $this->cookieMetadataFactory = $this->_objectManager->get(\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class);
        $this->adminSessionManager = $this->_objectManager->get(\Magento\Security\Model\AdminSessionsManager::class);
        $this->redirect = $this->_objectManager->get(\Magento\Framework\App\Response\RedirectInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadAdmins
     * @magentoDataFixture loadUiBookmarks
     */
    public function testItRemovesUiBookmarks()
    {
        $this->state->setAreaCode('adminhtml');
        $this->_objectManager->configure($this->configLoader->load('adminhtml'));

        /** @var \Magento\User\Model\User $user */
        $admin1 = $this->_objectManager->create(\Magento\User\Model\User::class)->loadByUsername('TestAdmin1');
        $this->session->setUser($admin1);
        $this->session->processLogin();

        if ($this->session->isLoggedIn()) {
            $cookieValue = $this->session->getSessionId();
            if ($cookieValue) {
                $cookiePath = str_replace('autologin.php', 'index.php', $this->sessionConfig->getCookiePath());
                $cookieMetadata = $this->cookieMetadataFactory
                    ->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath($cookiePath)
                    ->setDomain($this->sessionConfig->getCookieDomain())
                    ->setSecure($this->sessionConfig->getCookieSecure())
                    ->setHttpOnly($this->sessionConfig->getCookieHttpOnly());
                $this->cookieManager->setPublicCookie($this->session->getName(), $cookieValue, $cookieMetadata);
                $this->adminSessionManager->processLogin();
            }

            $loggedUserId = (int) $this->session->getUser()->getId();

            /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $admin1BookmarksList */
            $admin1BookmarksList = $this->_objectManager
                ->create(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class)
                ->create()
                ->addFieldToFilter(
                    \MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset\Index::UI_BOOKMARK_USER_ID_FIELD,
                    ['eq' => $loggedUserId]
                );

            $this->assertNotEmpty($admin1BookmarksList->getItems());

            /** @var \Magento\User\Model\User $user */
            $admin2 = $this->_objectManager->create(\Magento\User\Model\User::class)->loadByUsername('TestAdmin2');

            /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $admin2BookmarksList */
            $admin2BookmarksList = $this->_objectManager
                ->create(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class)
                ->create()
                ->addFieldToFilter(
                    \MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset\Index::UI_BOOKMARK_USER_ID_FIELD,
                    ['eq' => $admin2->getId()]
                );

            $this->assertNotEmpty($admin2BookmarksList->getItems());

            $this->dispatch('backend/bookmark/reset/index');

            $this->assertSessionMessages(
                $this->stringStartsWith('Grid Search has been reset successfully'),
                \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
            );

            $this->assertRedirect($this->stringContains($this->redirect->getRefererUrl()));

            /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $admin1AfterResetBookmarksList */
            $admin1AfterResetBookmarksList = $this->_objectManager
                ->create(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class)
                ->create()
                ->addFieldToFilter(
                    \MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset\Index::UI_BOOKMARK_USER_ID_FIELD,
                    ['eq' => $loggedUserId]
                );

            $this->assertEmpty($admin1AfterResetBookmarksList->getItems());

            /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $admin2AfterResetBookmarksList */
            $admin2AfterResetBookmarksList = $this->_objectManager
                ->create(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class)
                ->create()
                ->addFieldToFilter(
                    \MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset\Index::UI_BOOKMARK_USER_ID_FIELD,
                    ['eq' => $admin2->getId()]
                );

            $this->assertNotEmpty($admin2AfterResetBookmarksList->getItems());
        }
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

    public static function loadAdmins()
    {
        include __DIR__.'/../../../../_files/admin_users.php';
    }

    public static function loadUiBookmarks()
    {
        include __DIR__.'/../../../../_files/ui_bookmarks.php';
    }
}
