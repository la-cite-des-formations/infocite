<ul class="nav nav-tabs @if(!$tabsSystem['withMarge']) border-0 @endif" id="{{ $tabsSystem['name'] }}" role="tablist">
    @foreach($tabsSystem['tabs'] as $id => $tab)
    <li wire:click="setCurrentTab('{{ $tabsSystem['name'] }}', '{{ $id }}')"
        class="nav-item admin-nav-hover" role="presentation" title="{{ $tab['title'] }}"
        @if($tab['hidden']) hidden @endif>
        <a class="d-flex nav-link admin-nav-icons border-0 @if($tabsSystem['currentTab'] === $id) active @endif" id="{{ $id }}Tab"
            data-toggle="tab" href="#{{ $id }}Pane" role="tab" aria-controls="{{ $id }}"
            aria-selected="@if($tabsSystem['currentTab'] === $id) true @else false @endif">
            <span class="material-icons">{{ $tab['icon'] }}</span>
        </a>
    </li>
    @endforeach
</ul>
<div class="tab-content @if($tabsSystem['withMarge']) mt-2 @endif" id="{{ $tabsSystem['name'] }}Content">
    @foreach($tabsSystem['tabs'] as $id => $tab)
    <div class="tab-pane @if($tabsSystem['currentTab'] === $id) show active @endif"
            id="{{ $id }}Pane" role="tabpanel" aria-labelledby="{{ $id }}Tab">
        @include("{$tabsSystem['panesPath']}.{$tabsSystem['name']}.{$id}")
    </div>
    @endforeach
</div>
