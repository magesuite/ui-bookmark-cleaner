<?php

namespace MageSuite\UiBookmarkCleaner\Plugin\Backend\Block\System\Account\Edit;

class ResetBookmarksButton
{
    const ON_CLICK_ACTION_MARKUP = "deleteConfirm('%s', '%s')";

    protected \Magento\Framework\Escaper $escaper;

    public function __construct(\Magento\Framework\Escaper $escaper)
    {
        $this->escaper = $escaper;
    }

    public function beforeSetLayout(\Magento\Backend\Block\System\Account\Edit $subject)
    {
        $resetCurrentConfirmMessage = __('Are you sure you want to reset the current bookmarks?');
        $urlCurrent = $subject->getUrl('bookmark/reset/current');
        $onClickActionCurrent = sprintf(self::ON_CLICK_ACTION_MARKUP, $this->escaper->escapeJs($this->escaper->escapeHtml($resetCurrentConfirmMessage)), $urlCurrent);

        $resetAllConfirmMessage = __('Are you sure you want to reset the all bookmarks?');
        $urlAll = $subject->getUrl('bookmark/reset/all');
        $onClickActionAll = sprintf(self::ON_CLICK_ACTION_MARKUP, $this->escaper->escapeJs($this->escaper->escapeHtml($resetAllConfirmMessage)), $urlAll);

        $options = [
            'reset_current' => ['label' => __('Reset Only Current'), 'onclick' => $onClickActionCurrent, 'default' => true],
            'reset_all' => ['label' => __('Reset All'), 'onclick' => $onClickActionAll]
        ];

        $addButtonProps = [
            'id' => 'reset_bookmarks',
            'label' => __('Reset Bookmarks'),
            'class' => 'reset-bookmarks',
            'button_class' => '',
            'class_name' => \MageSuite\UiBookmarkCleaner\Block\Widget\Button\SecondarySplitButton::class,
            'options' => $options
        ];

        $subject->addButton('reset_bookmarks', $addButtonProps);
    }
}
