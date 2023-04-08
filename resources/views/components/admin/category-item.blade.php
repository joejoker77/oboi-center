@props(['category', 'tag'])

@if($tag === 'td')
    <tr>
        <td><a href="{{ route('admin.shop.categories.show', $category) }}">{{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title }}</a></td>
        <td>{{ $category->slug }}</td>
        <td>
            <a href="{{ route('admin.shop.categories.edit', $category) }}" class="list-inline-item" type="button" id="editCategory">
                <span data-feather="edit"></span>
            </a>|
            <a href="{{ route('admin.shop.categories.create') }}" class="list-inline-item" type="button" id="addAttributes">
                <span data-feather="plus-square"></span>
            </a>|
            <form class="list-inline-item" action="{{ route('admin.shop.categories.destroy', $category) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn p-0 align-baseline js-confirm" type="submit"><span data-feather="trash-2"></span></button>
            </form>
        </td>
    </tr>
@endif

@if($tag === 'option')
    <option value="{{ $category->id }}">{{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->name }}</option>
@endif

@foreach($category->children as $children)
    <x-admin.category-item :category="$children" :tag="$tag" />
@endforeach

