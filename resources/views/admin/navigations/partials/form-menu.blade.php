<form method="POST" action="@if($menu){{ route('admin.navigations.update', $menu) }}@else{{ route('admin.navigations.store') }}@endif">
    @if($menu)
        @method('PATCH')
    @endif
    @csrf
    <div class="form-floating mb-3">
        <input class="form-control" type="text" id="menuTitle" name="title" placeholder="Укажите имя" value="{{ old('title', $menu) }}">
        <label class="form-label" for="menuTitle">Укажите имя</label>
    </div>
    <div class="form-floating mb-3">
        <input class="form-control" type="text" id="menuHandler" name="handler" placeholder="Укажите название обработчика" value="{{ old('handler', $menu) }}">
        <label class="form-label" for="menuHandler">Укажите название обработчика</label>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="show_title" id="showTitle" class="form-check-input" value="1" @if($menu) @checked($menu->show_title) @endif>
        <label class="form-label">Пказывать заголовок</label>
    </div>
    <button type="submit" class="btn btn-success w-100">Сохранить</button>
</form>
