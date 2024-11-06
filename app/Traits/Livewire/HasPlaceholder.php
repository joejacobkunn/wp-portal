<?php

namespace App\Traits\Livewire;

trait HasPlaceholder
{
    public function placeholder()
    {
        $rows = $this->getPlaceholderRowLength();
        $html = '<div class="loading-skeleton">';

        for ($i = 0; $i < $rows; $i++) {
            $html .= '<p style="height: ' . rand(3, 5) . 'rem;">dummy text</p>';
        }

        $html .= '</div>';

        return $html;
    }

    public function getPlaceholderRowLength()
    {
        return isset($this->placeholderRow) ? $this->placeholderRow : 3;
    }
}
