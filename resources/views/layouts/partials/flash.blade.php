@if (session('status'))
    <div class="alert alert-success @if(Route::current()->getName() == 'home')position-absolute container @endif" @if(Route::current()->getName() == 'home')js-autofade="true" @endif>
        {!! session('status') !!}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success @if(Route::current()->getName() == 'home')position-absolute container @endif" @if(Route::current()->getName() == 'home')js-autofade="true" @endif>
        {!! session('success') !!}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger @if(Route::current()->getName() == 'home')position-absolute container @endif" @if(Route::current()->getName() == 'home')js-autofade="true" @endif>
        {!! session('error') !!}
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info @if(Route::current()->getName() == 'home')position-absolute container @endif" @if(Route::current()->getName() == 'home')js-autofade="true" @endif>
        {!! session('info') !!}
    </div>
@endif
