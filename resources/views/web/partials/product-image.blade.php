@php
    $fallbackUrl = asset('dummy.png');

    if (!empty($src)) {
        $imageSrc = $src;
    } elseif (!empty($imagePath)) {
        $imageSrc = asset('storage/' . $imagePath);
    } elseif (isset($product) && $product->images && is_array($product->images) && count($product->images) > 0) {
        $imageSrc = asset('storage/' . $product->images[0]);
    } else {
        $imageSrc = $fallbackUrl;
    }

    $altText = $alt ?? (isset($product) ? $product->name : 'Product');
    $imgClass = $class ?? '';
    $imgStyle = $style ?? '';
@endphp
<img
    src="{{ $imageSrc }}"
    alt="{{ $altText }}"
    @if($imgClass) class="{{ $imgClass }}" @endif
    @if($imgStyle) style="{{ $imgStyle }}" @endif
    onerror="this.onerror=null;this.src='{{ $fallbackUrl }}';"
>
