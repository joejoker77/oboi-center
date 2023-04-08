<x-mail::message>
# Introduction

The body of your message. @if($tmpPassword) Ваш временный пароль: {{ $tmpPassword }} @endif

<x-mail::button :url="$url">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
