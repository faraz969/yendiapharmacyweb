@php
    $fallbackUrl = asset('dummy.png');
    $hideUntilLoaded = $hideUntilLoaded ?? false;

    if (!empty($src)) {
        $imageSrc = $src;
    } elseif (!empty($imagePath)) {
        $imageSrc = asset('storage/' . $imagePath);
    } elseif (isset($product) && $product->images && is_array($product->images) && count($product->images) > 0) {
        $imageSrc = asset('storage/' . $product->images[0]);
    } else {
        $imageSrc = $fallbackUrl;
    }

    $usesFallback = $imageSrc === $fallbackUrl;
    $altText = $alt ?? (isset($product) ? $product->name : 'Product');
    $imgClass = trim(($class ?? '') . ' js-product-image');
    $imgStyle = $style ?? '';
@endphp
<img
    src="{{ $imageSrc }}"
    alt="{{ $altText }}"
    class="{{ $imgClass }}"
    @if($imgStyle) style="{{ $imgStyle }}" @endif
    data-fallback-url="{{ $fallbackUrl }}"
    @if($hideUntilLoaded) data-hide-until-loaded="1" @endif
    @if($usesFallback) data-fallback-initial="1" @endif
    @if($hideUntilLoaded)
        onload="if(window.onProductImageLoad)window.onProductImageLoad(this)"
        onerror="this.onerror=null;this.src='{{ $fallbackUrl }}';this.dataset.fallbackInitial='1';if(window.onProductImageError)window.onProductImageError(this)"
    @else
        onerror="this.onerror=null;this.src='{{ $fallbackUrl }}';"
    @endif
>
