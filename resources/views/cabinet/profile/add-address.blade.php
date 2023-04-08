@php
 /**
 * @var App\Entities\User\User $user
 * @var App\Entities\User\DeliveryAddress|null $address
 */
@endphp
<div class="container">
    <div class="row">
        <div class="col-md-12 mx-auto">
            <form action="{{ route('cabinet.profile.store-delivery-address') }}" method="post">
                <h5 class="mb-md-3">@if(!$address)Заполните форму@elseРедактирование адреса@endif</h5>
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input id="postIndex" type="text" placeholder="Почтовый индекс" class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" name="postal_code" value="@if($address){{ $address->postal_code }}@endif" required>
                            <label for="name">Почтовый индекс</label>
                            @if ($errors->has('postal_code'))<div class="invalid-feedback">{{ $errors->first('postal_code') }}</div>@endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input id="city" type="text" placeholder="Город" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" name="city" value="@if($address){{ $address->city }}@endif">
                            <label for="city">Город</label>
                            @if ($errors->has('city'))<div class="invalid-feedback">{{ $errors->first('city') }}</div>@endif
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input id="street" type="text" placeholder="Улица" class="form-control{{ $errors->has('street') ? ' is-invalid' : '' }}" name="street" value="@if($address){{ $address->street }}@endif" required>
                    <label for="street">Улица</label>
                    @if ($errors->has('street'))<div class="invalid-feedback">{{ $errors->first('street') }}</div>@endif
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input id="house" type="text" placeholder="Дом" class="form-control{{ $errors->has('house') ? ' is-invalid' : '' }}" name="house" value="@if($address){{ $address->house }}@endif" required>
                            <label for="house">Дом</label>
                            @if ($errors->has('house'))<div class="invalid-feedback">{{ $errors->first('house') }}</div>@endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input id="housePart" type="text" placeholder="Корпус" class="form-control{{ $errors->has('house_part') ? ' is-invalid' : '' }}" name="house_part" value="@if($address){{ $address->house_part }}@endif" >
                            <label for="housePart">Корпус</label>
                            @if ($errors->has('house_part'))<div class="invalid-feedback">{{ $errors->first('house_part') }}</div>@endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input id="flat" type="text" placeholder="Квартира" class="form-control{{ $errors->has('flat') ? ' is-invalid' : '' }}" name="flat" value="@if($address){{ $address->flat }}@endif">
                            <label for="flat">Квартира</label>
                            @if ($errors->has('flat'))<div class="invalid-feedback">{{ $errors->first('flat') }}</div>@endif
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <button class="btn btn-lg btn-blue-dark w-100" type="submit">Сохранить адрес</button>
                </div>
            </form>
        </div>
    </div>
</div>
