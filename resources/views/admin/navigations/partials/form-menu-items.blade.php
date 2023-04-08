@php
    /** @var App\Entities\Site\Navigations\Menu $menu */
    use App\Entities\Site\Navigations\NavItem;
    $navItems = NavItem::treeOf(function ($query) use ($menu) {
        $query->whereNull('parent_id')->where('menu_id', $menu->id);
    })->orderBy('sort')->get()->toTree();
@endphp
<h5 class="mt-5 d-flex justify-content-between">{{ $menu->title }}
    <button type="button" class="btn btn-warning btn-sm js-add-item">Добавить</button>
</h5>
<form method="POST" action="{{ route('admin.navigations.update', $menu) }}">
    @method('PATCH')
    @csrf
    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
    <input type="hidden" name="title" value="{{ $menu->title }}">
    <input type="hidden" name="handler" value="{{ $menu->handler }}">
    @if($menu->show_title)
        <input type="hidden" name="show_title" value="1">
    @endif
    <div id="draggable" class="list-group nested-sortable mb-3">
        @if(!$navItems->isEmpty())
            @foreach($navItems as $navItem)
                <x-admin.nav-item :navItem="$navItem" />
            @endforeach
        @endif
    </div>
    <button id="formMenuItems" type="submit" class="btn btn-success w-100">Сохранить</button>
</form>

