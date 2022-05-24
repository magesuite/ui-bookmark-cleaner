<?php

namespace MageSuite\UiBookmarkCleaner\Block\Widget\Button;

class SecondarySplitButton extends \Magento\Backend\Block\Widget\Button\SplitButton
{
    public function getButtonAttributesHtml()
    {
        $buttonAttributesHtml = parent::getButtonAttributesHtml();

        return str_replace('primary', 'secondary', $buttonAttributesHtml);
    }

    public function getToggleAttributesHtml()
    {
        $toggleAttributesHtml = parent::getToggleAttributesHtml();

        return str_replace('primary', 'secondary', $toggleAttributesHtml);
    }
}
