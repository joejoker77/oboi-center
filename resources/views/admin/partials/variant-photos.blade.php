@php
    /** @var \App\Entities\Shop\Variant $variant */
    $photos = $variant->photos;
@endphp
<div class="gallery" id="modalGallery" data-variant-id="{{ $variant->id }}">
    <div class="col-10">
        @foreach($photos as $index => $photo)
            <div class=@if($index == 0)"main-photo active"@else"main-photo"@endif data-photo-id="{{ $photo->id }}">
                <img src="{{ asset($photo->getPhoto('full')) }}" alt="{{ $photo->alt_tag }}">
                @if($photo->description)
                    <p class="text-white">{{ $photo->description }}</p>
                @endif
            </div>
        @endforeach
        <form action="{{ route('admin.photos.update-variant-photo') }}" id="variantForm-{{ $variant->id }}" style="display: contents" enctype="multipart/form-data">
            <input type="hidden" name="variant" value="{{ $variant->id }}">
            <div class="thumbs-container">
                @foreach($photos as $index => $photo)
                    <div class=@if($index == 0) "thumb-item active" @else "thumb-item" @endif data-photo-id="{{ $photo->id }}">
                        <div class="thumb-photo">
                            <img src="{{ asset($photo->getPhoto('small')) }}" alt="{{ $photo->alt_tag }}">
                        </div>
                        <label class="plus-minus-checkbox">
                            <input type="checkbox" name="variantsPhoto[{{ $photo->id }}]" value="{{ $photo->id }}"
                                   @checked($variant->photos[0]->id === $photo->id)>
                            <span class="value"></span>
                        </label>
                    </div>
                @endforeach
                <div class="button-item">
                    <label for="photosForVariant--{{ $variant->id }}" type="button" data-variant-id="{{ $variant->id }}">
                        <i data-feather="upload"></i>
                        <input id="photosForVariant--{{ $variant->id }}" type="file" multiple style="display: none" name="add-photos[]">
                    </label>
                </div>
            </div>
        </form>
    </div>
    <div id="leftSide" class="col-2">
        @foreach($photos as $index => $photo)
            @if($photo->variant_id && $photo->variant_id !== $variant->id) @continue @endif
            <div id="photo_{{ $photo->id }}" class=@if($index == 0)"photo-form active"@else"photo-form"@endif data-photo-id="{{ $photo->id }}">
                <input form="variantForm-{{ $variant->id }}" type="hidden" name="id[]" value="{{ $photo->id }}">
                <div class="form-floating mb-3">
                    <input form="variantForm-{{ $variant->id }}" id="altTag-{{ $photo->id }}" class="form-control" value="{{ $photo->alt_tag }}"
                           name="alt_tag[{{ $photo->id }}]" type="text" placeholder="Alt атрибут">
                    <label for="altTag-{{ $photo->id }}" class="form-label">Alt атрибут</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea form="variantForm-{{ $variant->id }}" id="description-{{ $photo->id }}" name="description[{{ $photo->id }}]" class="form-control"
                              placeholder="Описание изображения">{{ $photo->description }}</textarea>
                    <label for="description-{{ $photo->id }}">Описание изображения</label>
                </div>
                <div class="form-text">
                    <button type="submit" form="variantForm-{{ $variant->id }}" class="btn btn-success w-100">Сохранить</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
