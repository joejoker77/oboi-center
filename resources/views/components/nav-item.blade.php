@props(['items'])
@foreach($items as $item)
    @if($item->item_path)
        @php
            $classes = 'nav-link';
            if (request()->is(trim($item->item_path, '/').'*')) {
                $classes .= ' active';
            }

            if (!$item->children->isEmpty()) {
                $classes .= ' dropdown-toggle';
            }
        @endphp
        <li class="nav-item @if(!$item->children->isEmpty())dropdown @endif">
            <a class="{{ $classes }}"
               href="{{ $item->item_path }}"
               @if(!$item->children->isEmpty()) data-bs-toggle="dropdown" aria-expanded="false" @endif
               @if($item->image) data-image="{{ $item->image }}" @endif
               @if($item->entity_type == 'external') target="_blank" @endif
            >
                {{ $item->link_text ?? $item->title }}
            </a>
            @if(!$item->children->isEmpty())
                <ul class="dropdown-menu">
                    <x-nav-item :items="$item" />
                </ul>
            @endif
        </li>
    @endif
@endforeach
