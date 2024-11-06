<div class="x-tabs">
    @php
        $tabs = $this->tabs;
        $tabList = $tabs[$tabId]['links'];
        $tabHeaders = [];
        $tabContents = [];
        $lazy = !empty($lazy) ? true : false;
        
        foreach($tabList as $tabIndex => $tab) {
            if (isset(${"tab_header_{$tabIndex}"})) {
                $tabHeaders[$tabIndex] = (${"tab_header_{$tabIndex}"})->toHtml();
            }

            if (isset(${"tab_content_{$tabIndex}"})) {
                $selectedSlot = ${"tab_content_{$tabIndex}"};
                
                $tabContents[$tabIndex] = [
                    'html' => !empty($selectedSlot) && !$selectedSlot->attributes->get('component') ? $selectedSlot->toHtml() : '',
                    'component' => !empty($selectedSlot) ? $selectedSlot->attributes->get('component') : '',
                    'attributes' => !empty($selectedSlot) ? $selectedSlot->attributes->except('component')->getAttributes() : [],
                ];
            }
        }

        $commonContent = [
            'html' => '',
            'component' => '',
            'attributes' => [],
        ];

        if (!empty($content) && $content->attributes->get('component')) {
            $commonContent['html'] = !empty($content) && !$content->attributes->get('component') ? $content->toHtml() : '';

            if ($content->attributes->get('component')) {
                $commonContent['component'] = !empty($content) ? $content->attributes->get('component') : '';
                $commonContent['attributes'] = !empty($content) ? $content->attributes->except('component')->getAttributes() : [];
            }
        }

    @endphp

    <livewire:component.tabs
        :tabId="$tabId"
        :tabHeaders="$tabHeaders"
        :tabContents="$tabContents"
        :activeTab="$tabs[$tabId]['active']"
        :tabs="$tabs[$tabId]['links']"
        :commonContent="$commonContent"
        parentComponent="{{ $parentComponent ?? $this::class }}"
        wire:key="tab-{{ $tabs[$tabId]['active'] . ($key ?? '') }}"
        lazy="{{ $lazy }}"
    >

    @if(empty($commonContent['component']) && isset($content))
        {{ $content }}
    @endif
</div>