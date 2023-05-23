@foreach($navItems as $i => $navItem)
    <div class="category-item item-{{ $i }} fadein">
        <div class="category-image" style="height: auto">
            <img src="{{ $navItem->image }}" width="100%" height="auto">
        </div>
        <div class="category-info card-body">
            <span class="abbr">{{ substr($navItem->title, 0, 2) }}</span>
            <span class="text">{{ $navItem->title }}</span>
            <a href="{{ $navItem->item_path }}" class="btn btn-link stretched-link" style="z-index: 3"></a>
        </div>
    </div>
@endforeach
