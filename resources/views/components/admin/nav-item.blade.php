@php /** @var App\Entities\Site\Navigations\NavItem $navItem */ @endphp
@props(['navItem'])

<div class="list-group-item nested-{{ $navItem->front_id }}" data-id="{{ $navItem->front_id }}" data-sort="{{ $navItem->sort }}">
    <div class="form-floating">
        <input class="form-control menu-item-input" id="menuItemInput-{{$navItem->front_id}}" name="items[{{ $navItem->front_id }}][title]" value="{{ $navItem->title }}" placeholder="-= Начните набирать текст =-" type="text">
        <label class="form-label" for="menuItemInput-{{$navItem->front_id}}">-= {{ App\Entities\Site\Navigations\NavItem::getType($navItem->entity_type) }} =-</label>
        <input type="hidden" name="items[{{ $navItem->front_id }}][id]" value="{{ $navItem->front_id }}">
        <input type="hidden" name="items[{{ $navItem->front_id }}][sort]" value="{{ $navItem->sort }}">
        <input type="hidden" name="items[{{ $navItem->front_id }}][parent]" value="{{ $navItem->front_parent }}">
    </div>
    <div class="list-group nested-sortable">
        @foreach($navItem->children as $children)
            <x-admin.nav-item :navItem="$children" />
        @endforeach
    </div>
    <input type="hidden" name="items[{{ $navItem->front_id }}][type]" value="{{ $navItem->entity_type }}">
    <input type="hidden" name="items[{{ $navItem->front_id }}][item_id]" value="{{ $navItem->entity_id }}">
    <input id="navImage{{ $navItem->id }}" type="hidden" name="items[{{ $navItem->front_id }}][image]" value="{{ $navItem->image }}">
    <div class="control-item-buttons">
        @if($navItem->image)
            <button type="button" class="remove-item-image" data-item-id="{{ $navItem->id }}"
                    data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Удалить изображение">
                <i data-feather="image"></i>
            </button>
        @else
            <button type="button" class="add-item-image" data-item-id="{{ $navItem->id }}" id="navItemImageBtn"
                    data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Добавить изображение">
                <i data-feather="image"></i>
            </button>
        @endif
        <button type="button" class="edit-item-title" data-item-id="{{ $navItem->id }}"
                data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Текст ссылки">
            <i data-feather="edit"></i>
        </button>
        <button type="button" class="delete-item" data-item-id="{{ $navItem->id }}"
                data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Удалить пункт меню">
            <i data-feather="trash-2"></i>
        </button>
        <div class="new-title @if($navItem->link_text) always-show @endif">
            <div class="form-floating">
                <input type="text" name="items[{{ $navItem->front_id }}][link_text]" placeholder="-= Текст ссылки =-"
                       class="form-control" id="newTitle-{{ $navItem->front_id }}" value="{{ $navItem->link_text }}">
                <label for="newTitle-{{ $navItem->front_id }}" class="form-label">-= Текст ссылки =-</label>
            </div>
        </div>
    </div>
</div>

