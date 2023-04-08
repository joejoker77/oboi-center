@props(['items'])
@foreach($items as $item)
    <li class="nav-item @if(!$item->children->isEmpty())dropdown @endif">
        <a class="nav-link @if(!$item->children->isEmpty())dropdown-toggle @endif"
           href="{{ $item->item_path }}"
            @if(!$item->children->isEmpty()) data-bs-toggle="dropdown" aria-expanded="false" @endif
            @if($item->image) data-image="{{ $item->image }}" @endif
        >
            {{ $item->link_text ?? $item->title }}
        </a>
        @if(!$item->children->isEmpty())
            <ul class="dropdown-menu">
                <x-nav-item :items="$item" />
            </ul>
        @endif
    </li>
@endforeach
