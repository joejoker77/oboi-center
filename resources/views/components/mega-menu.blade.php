<div class="{{ $menu_class }}" id="{{ $menu_id }}">
    <ul class="nav nav-tabs" role="tablist">
        @foreach($navItems as $key => $navItem)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($key == 0)active @endif" id="menu-tab-{{$key}}" data-bs-toggle="tab"
                        data-bs-target="#mainMenuTab-{{$key}}" type="button" role="tab"
                        aria-controls="menu-tab-pane-{{$key}}" aria-selected="false">
                    {{ $navItem->link_text ?? $navItem->title }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" id="myTabContent">
        @foreach($navItems as $key => $navItem)
            <div class="tab-pane fade @if($key == 0)active show @endif" id="mainMenuTab-{{$key}}" role="tabpanel"
                 aria-labelledby="menu-tab-{{$key}}" tabindex="0">
                @php $firstImage = $firstImageAlt = null @endphp
                @foreach($navItem->children as $keyItem => $item)
                    @if($keyItem == 0 and $item->image)
                        @php $firstImage = $item->image; $firstImageAlt = $item->title @endphp
                    @endif
                    <div class="head-submenu">
                        <a class="btn btn-link" href="{{$item->item_path}}"
                           @if($item->image) data-image="{{ $item->image }}" @endif
                        >
                            {{$item->link_text ?? $item->title}}
                        </a>
                        <ul>
                            <x-nav-item :items="$item->children"/>
                        </ul>
                    </div>
                @endforeach
                @if($firstImage)
                    <div class="menu-image">
                        <img src="{{$firstImage}}" alt="{{ $firstImageAlt }} menu image">
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>


