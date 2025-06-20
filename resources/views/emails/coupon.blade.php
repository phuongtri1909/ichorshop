@component('mail::message')
# Hello {{ $userName }}!

We would like to give you a special discount code!

## Your discount code: <span style="color: #D1A66E; font-weight: bold;">{{ $couponCode }}</span>

**Discount:** {{ $discount }}

@if($minOrder)
**Minimum order:** ${{ $minOrder }}
@endif

**Valid until:** {{ $expiry }}

@if($description)
**Note:** {{ $description }}
@endif

Use this code at checkout to enjoy your special offer. We hope you have a great shopping experience!

@component('mail::button', ['url' => route('home')])
Shop Now
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
