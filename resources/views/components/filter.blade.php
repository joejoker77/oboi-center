@php /** @var App\Entities\Shop\Filter $filter */ @endphp

@if(request()->get('attributes') || request()->get('tags') || request()->get('categories') || request()->get('colors') || request()->get('price'))
    <a href="{{route('shop.filter')}}" type="button" class="btn btn-blue-dark w-100 mb-3">Сбросить фильтр</a>
@endif
<form action="{{ route('shop.filter') }}" method="get" class="form-filter">
    @foreach($filter as $groupName => $group)
        @if(!empty($group['prices']))
            <div class="filter-group-item price">
                @if($group['displayHeader'])
                    <div class="filter-group-heading">
                        {{ $groupName }}
                    </div>
                @endif
                <div class="filter-item">
                    <input type="hidden" id="filterMinPrice" name="price[min]" value="@if(!empty(request()->get('price'))){{str_replace('"', '', request()->get('price')['min'])}}@else{{ get_min_from_string($group['prices'], '₽').' ₽' }}@endif" js-name="minValue">
                    <input type="hidden" id="filterMaxPrice" name="price[max]" value="@if(!empty(request()->get('price'))){{str_replace('"', '', request()->get('price')['max'])}}@else{{ get_max_from_string($group['prices'], '₽'). ' ₽' }}@endif" js-name="maxValue">
                    <div class="slider-styled" data-min="{{ get_min_from_string($group['prices'], '₽').' ₽' }}" data-max="{{ get_max_from_string($group['prices'], '₽'). ' ₽' }}" data-steps="{{json_encode($group['prices'])}}" @if(!empty(request()->get('price'))) data-fact-min="{{str_replace('"', '', request()->get('price')['min'])}}" data-fact-max="{{str_replace('"', '', request()->get('price')['max'])}}" @endif></div>
                    <div class="filter-values d-flex justify-content-between">
                        <div class="min-value-display" js-name="minValueDisplayPrice">
                            @if(!empty(request()->get('price'))){{str_replace('"', '', request()->get('price')['min'])}}@else{{ get_min_from_string($group['prices'], '₽').' ₽' }}@endif
                        </div>
                        <div class="max-value-display" js-name="maxValueDisplayPrice">
                            @if(!empty(request()->get('price'))){{str_replace('"', '', request()->get('price')['max'])}}@else{{ get_max_from_string($group['prices'], '₽'). ' ₽' }}@endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(!empty($group['attributes']))
            @foreach($group['attributes'] as $attribute)
                @if((empty($restAttributes) || array_key_exists($attribute->id, $restAttributes)) && ($attribute->type === App\Entities\Shop\Attribute::TYPE_FLOAT || $attribute->type === App\Entities\Shop\Attribute::TYPE_INTEGER))
                    <div class="filter-group-item">
                        @if($group['displayHeader'])
                            <div class="filter-group-heading">
                                {{ $groupName }}
                            </div>
                        @endif
                        <div class="filter-item">
                            <div class="attribute-header">
                                {{ $attribute->name }}
                            </div>
                            <div class="attribute-item">
                                @php
                                    $sortedVariants = $attribute->variants;
                                @endphp

                                @if(!empty($attribute->newEquals))
                                    @foreach($attribute->newEquals as $key => $equalValue)
                                        <div class="variant form-check custom-checkbox">
                                            <label for="variant-{{$group['id']}}-{{$attribute->id}}-{{$key}}">{{ $equalValue }}
                                                <input id="variant-{{$group['id']}}-{{$attribute->id}}-{{$key}}" type="checkbox" class="form-check-input" value="{{$equalValue}}" name="attributes[{{$attribute->id}}][equals][]" @if(isset(request()->get('attributes')[$attribute->id]['equals'][$key])) @checked(request()->get('attributes')[$attribute->id]['equals'][$key] == $equalValue) @endif >
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                <input type="hidden" id="attributeMin-{{$attribute->id}}" name="attributes[{{ $attribute->id }}][min]" value="@if(!empty(request()->get('attributes')[$attribute->id]['min'])){{request()->get('attributes')[$attribute->id]['min']}}@else{{get_min_from_string($sortedVariants, $attribute->unit).' '.$attribute->unit}}@endif" js-name="minValue">
                                <input type="hidden" id="attributeMax-{{$attribute->id}}" name="attributes[{{ $attribute->id }}][max]" value="@if(!empty(request()->get('attributes')[$attribute->id]['max'])){{request()->get('attributes')[$attribute->id]['max']}}@else{{get_max_from_string($sortedVariants, $attribute->unit).' '.$attribute->unit}}@endif" js-name="maxValue">
                                <div class="slider-styled" data-min={{ $attribute->min }} data-max={{ $attribute->max }} data-steps="[{{ trim(implode(',', $sortedVariants), ',') }}]" @if(!empty(request()->get('attributes'))) data-fact-min="{{request()->get('attributes')[$attribute->id]['min']}}" data-fact-max="{{request()->get('attributes')[$attribute->id]['max']}}" @endif></div>
                                <div class="filter-values d-flex justify-content-between">
                                    <div class="min-value-display" js-name="minValueDisplay">
                                        @if(!empty(request()->get('attributes')[$attribute->id]['min'])){{request()->get('attributes')[$attribute->id]['min']}}@else{{get_min_from_string($sortedVariants, $attribute->unit).' '.$attribute->unit}}@endif
                                    </div>
                                    <div class="max-value-display" js-name="maxValueDisplay">
                                        @if(!empty(request()->get('attributes')[$attribute->id]['max'])){{request()->get('attributes')[$attribute->id]['max']}}@else{{get_max_from_string($sortedVariants, $attribute->unit).' '.$attribute->unit}}@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    @endforeach
    @foreach($filter as $groupName => $group)
        @if($groupName == 'Цвет')
            <div class="filter-group-item color">
                @if($group['displayHeader'])
                    <div class="filter-group-heading">
                        {{ $groupName }}
                    </div>
                @endif
                <div class="filter-item">
                    @php /** @var App\Entities\Shop\Tag $color */ @endphp
                    <div class="grid">
                        @foreach($group['tags'] as $color)
                            @php $textColor = $color->slug == 'black' || $color->slug == 'darkblue' ? 'white' : 'black'; @endphp
                            <div
                                class="color-item p-1"
                                style="border-color: @if($color->slug !== 'white'){{ $color->slug }}@else black @endif"
                                data-bs-toggle="tooltip"
                                data-bs-placement="bottom"
                                data-bs-custom-class="custom-tooltip" data-bs-title="{{ $color->name }}"
                            >
                                <label for="color-{{ $color->id }}" style="background: {{ $color->slug }};color: {{ $textColor }};@if($color->slug == 'white')border:2px solid black @endif">
                                    <input id="color-{{ $color->id }}" type="checkbox" name="colors[]" @checked(!empty(request()->get('colors')) && in_array($color->id, request()->get('colors'))) value="{{ $color->id }}" />
                                    <span class="material-symbols-outlined">done</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    @foreach($filter as $groupName => $group)
        @if($groupName !== 'Цвет' && !isset($group['prices']))
            <div class="filter-group-item">
                @if($group['displayHeader'])
                    <div class="filter-group-heading">
                        {{ $groupName }}
                    </div>
                @endif
                <div class="filter-inputs">
                    @if(!empty($group['tags']))
                        @foreach($group['tags'] as $tag)
                            <div class="filter-item tag-item form-check custom-checkbox">
                                <label for="tag-{{$group['id']}}-{{$tag->id}}">{{ $tag->name }}
                                    <input id="tag-{{$group['id']}}-{{$tag->id}}" type="checkbox" class="form-check-input" value="{{$tag->id}}" name="tags[]">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        @endforeach
                    @endif

                    @if(!empty($group['attributes']))
                        @foreach($group['attributes'] as $attribute)
                            @if((empty($restAttributes) && $attribute->type !== App\Entities\Shop\Attribute::TYPE_FLOAT && $attribute->type !== App\Entities\Shop\Attribute::TYPE_INTEGER) || (array_key_exists($attribute->id, $restAttributes)) && ($attribute->type !== App\Entities\Shop\Attribute::TYPE_FLOAT && $attribute->type !== App\Entities\Shop\Attribute::TYPE_INTEGER && (isset($restAttributes[$attribute->id]) && count($restAttributes[$attribute->id]) > 1 || $attribute->selected)))
                                <div class="filter-item @if((empty($restAttributes[$attribute->id]) && count($attribute->variants) > 3) || (isset($restAttributes[$attribute->id]) && count($restAttributes[$attribute->id]) > 3 || $attribute->selected))collapsed @endif">
                                    <div class="attribute-header">
                                        {{ $attribute->name }}
                                        @if((empty($restAttributes[$attribute->id]) && count($attribute->variants) > 3) || (isset($restAttributes[$attribute->id]) && count($restAttributes[$attribute->id]) > 3 || $attribute->selected))
                                            <button type="button" class="btn btn-link">Показать все</button>
                                        @endif
                                    </div>
                                    <div class="attribute-item">
                                        @foreach($attribute->variants as $key => $variant)
                                            @if(empty($restAttributes) || in_array($variant, $restAttributes[$attribute->id]) || $attribute->selected)
                                                <div class="variant form-check custom-checkbox">
                                                    <label for="variant-{{$group['id']}}-{{$attribute->id}}-{{$key}}">{{ $variant }}
                                                        <input id="variant-{{$group['id']}}-{{$attribute->id}}-{{$key}}" type="checkbox" class="form-check-input" value="{{$variant}}" @checked($attribute->selected && in_array($variant, $attribute->selected['equals'])) name="attributes[{{$attribute->id}}][equals][]">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</form>
@if(request()->get('attributes') || request()->get('tags') || request()->get('categories') || request()->get('colors') || request()->get('price'))
    <a href="{{route('shop.filter')}}" type="button" class="btn btn-blue-dark w-100 mb-3">Сбросить фильтр</a>
@endif
