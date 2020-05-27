@component('mail::message')
# Introduction

Bu mail geldikden sonra 5 deqiqe erzinde parolu deyismelisiniz.

@component('mail::button', ['url' => $url])
Parolu deyismek ucun bura klikle
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
