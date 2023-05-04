@php
    /**
    * @var App\Entities\Shop\FilterGroup|null $group
    * @var App\Entities\Shop\Tag[] $tags
    * @var App\Entities\Shop\Attribute[] $attributes
    * @var int $key
    */
    $group = $group ?? null;
    $key   = $key ?? null;
@endphp
<div class="group__item accordion-item">
    <h2 class="accordion-header" id="heading-@if($group){{ $group->id }}@endif">
        <button class="accordion-button @if($key > 0)collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-@if($group){{ $group->id }}@endif" aria-expanded="true" aria-controls="collapse-@if($group){{ $group->id }}@endif">
            @if($group){{ $group->name }}@elseНовая группа@endif
        </button>
    </h2>
    <div id="collapse-@if($group){{ $group->id }}@endif" class="accordion-collapse collapse @if($key === 0)show @endif" aria-labelledby="heading-@if($group){{ $group->id }}@endif" data-bs-parent="#accordionGroups">
        <div class="accordion-body">
            <div class="form-floating mb-3">
                <input id="groupName-@if($group){{ $group->id }}@endif" class="form-control @error('group_name') is-invalid @enderror"
                       name="group_name[@if($group){{ $group->id }}@endif]" value="@if($group){{ $group->name }}@endif" type="text" placeholder="Имя группы" required>
                <label for="groupName-@if($group){{ $group->id }}@endif" class="form-label">Имя группы</label>
                @error('group_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-floating mb-3">
                <h5>Категории в группе</h5>
                <select name="group_categories[@if($group){{ $group->id }}@endif][]" class="js-choices" multiple>
                    <option placeholder>-=Выбрать категорию=-</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                        @if($group && $group->categories)
                            @foreach($group->categories as $catId)
                                @selected((int)$catId === $category->id)
                                @endforeach
                            @endif
                        >
                            {{ html_entity_decode(str_repeat('&mdash;', (int)$category->depth)) }}{{ $category->title ?: $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-floating mb-3">
                <h5>Теги в группе</h5>
                <select name="tags[@if($group){{ $group->id }}@endif][]" class="js-choices" multiple>
                    <option value="">-=Выбрать теги=-</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}"
                        @if($group && $group->tags)
                            @foreach($group->tags as $tagId)
                                @selected((int)$tagId === $tag->id)
                            @endforeach
                        @endif
                        >
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-floating mb-3">
                <h5>Атрибуты в группе</h5>
                <select name="attributes[@if($group){{ $group->id }}@endif][]" class="js-choices" multiple>
                    <option value="">-=Выбрать атрибуты=-</option>
                    @foreach($attributes as $attribute)
                        <option value="{{ $attribute->id }}"
                        @if($group && $group->attributes)
                            @foreach($group->attributes as $attrId)
                                @selected((int)$attrId === $attribute->id)
                                @endforeach
                            @endif
                        >
                            {{ $attribute->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="display_head[@if($group){{ $group->id }}@endif]" value="" id="groupDisplayHead-@if($group){{ $group->id }}@endif" @checked($group && $group->display_header)>
                <label class="form-check-label" for="groupDisplayHead">
                    Показать имя группы
                </label>
            </div>
        </div>
    </div>
</div>
