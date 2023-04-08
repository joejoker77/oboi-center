@php /** @var \App\Entities\Shop\Attribute[] $attributes */ @endphp
@foreach($attributes as $attribute)
    @foreach($attribute->variants as $variant)
        @if(strpbrk($variant, '|'))
            @php $customTemplate = true @endphp
        @else
            @php $customTemplate = false @endphp
        @endif
    @endforeach
    <div class="row">
        <div class="col-2 mb-3 align-self-center">
            <h6>{{ $attribute->name }}</h6>
        </div>
        <div class="col-10 mb-3">
            @error('product.attributes')<div class="is-invalid"></div>@enderror
            <select
                name="product.attributes[{{ $attribute->id }}]@if($attribute->as_option)[]@endif"
                class="js-choices"
                data-attribute-id="{{ $attribute->id }}"
                @if($attribute->as_option) multiple @endif
                @if($customTemplate) data-custom-template @endif
            >
                <option value="">-=Выбрать {{ $attribute->name }}=-</option>
                @foreach($attribute->variants as $variant)
                    <option value="{{ $variant }}"
                        @foreach($product->values as $val)

                            @selected($val->attribute_id == $attribute->id && $val->value === $variant)
                        @endforeach
                    >
                        @if(strpbrk($variant, '|'))
                            @php
                                $arrayValue = explode('|', $variant);
                                $variant    = $arrayValue[0];
                                $value      = $arrayValue[1];
                            @endphp
                        @endif
                        {{ $variant }}
                    </option>
                @endforeach
            </select>
            @error('product.attributes')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
@endforeach
