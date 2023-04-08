@php /** @var \App\Entities\Shop\Photo[] $photos */ @endphp

<div class="gallery" id="modalGallery">
    <div class="col-10">
        @foreach($photos as $photo)
            <div class=@if($photo->id == $photo_id)"main-photo active"@else"main-photo"@endif data-photo-id="{{ $photo->id }}">
                <img src="{{ asset($photo->getPhoto('full')) }}" alt="{{ $photo->alt_tag }}">
                @if($photo->description)
                    <p class="text-white">{{ $photo->description }}</p>
                @endif
            </div>
        @endforeach
        <div class="thumbs-container">
            @foreach($photos as $photo)
                <div class=@if($photo->id == $photo_id)"thumb-item active"@else"thumb-item"@endif data-photo-id="{{ $photo->id }}">
                    <div class="thumb-photo">
                        <img src="{{ asset($photo->getPhoto('small')) }}" alt="{{ $photo->alt_tag }}">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-2">
        @foreach($photos as $photo)
            <form id="photo_{{ $photo->id }}" action="{{ route('admin.photos.update-photo') }}"
                  class=@if($photo->id == $photo_id)"photo-form js-photo-form active"@else"photo-form"@endif data-photo-id="{{ $photo->id }}">
                <input type="hidden" name="id" value="{{ $photo->id }}">
                <div class="form-floating mb-3">
                    <input id="altTag" class="form-control" value="{{ $photo->alt_tag }}"
                           name="alt_tag" type="text" placeholder="Alt атрибут">
                    <label for="altTag" class="form-label">Alt атрибут</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea id="description" name="description" class="form-control"
                              placeholder="Описание изображения">{{ $photo->description }}</textarea>
                    <label for="description">Описание изображения</label>
                </div>
                <div class="form-text">
                    <button type="submit" class="btn btn-success w-100">Сохранить</button>
                </div>
            </form>
        @endforeach
    </div>
</div>
