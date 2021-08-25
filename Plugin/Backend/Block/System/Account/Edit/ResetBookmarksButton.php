<?php

namespace MageSuite\UiBookmarkCleaner\Plugin\Backend\Block\System\Account\Edit;

class ResetBookmarksButton
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper
    )
    {
        $this->escaper = $escaper;
    }

    /**
     * @param \Magento\Backend\Block\System\Account\Edit $layout
     */
    public function beforeSetLayout(\Magento\Backend\Block\System\Account\Edit $layout)
    {
        $deleteConfirmMessage = __('Are you sure you want to reset Grid Search?');
        $url = $layout->getUrl('bookmark/reset/index');
        $onClickAction = sprintf(
            "deleteConfirm('%s', '%s')",
            $this->escaper->escapeJs($this->escaper->escapeHtml($deleteConfirmMessage)),
            $url
        );
        $layout->addButton('reset_bookmarks', [
            'label' => __('Reset Grid Search'),
            'onclick' => $onClickAction,
            'class' => 'reset-bookmarks'
        ]);
    }
}
