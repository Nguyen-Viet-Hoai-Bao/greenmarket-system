@foreach ($processedFilterProducts as $product)
    <div class="col-md-3 col-sm-6 mb-4 pb-2">
        <div class="list-card bg-white rounded overflow-hidden position-relative shadow-sm d-flex flex-column">
            <div class="list-card-image">
                <div class="star position-absolute">
                    @if ($product->best_seller == 1)
                        <span class="badge badge-success"><i class="icofont-star"></i></span>
                    @endif
                </div>
                <div class="favourite-heart text-danger position-absolute">
                    @if ($product->most_popular == 1)
                        <a href="{{ route('product.detail', $product->id) }}"><i class="icofont-heart"></i></a>
                    @endif
                </div>
                <a href="{{ route('product.detail', $product->id) }}">
                    <img src="{{ asset($product->productTemplate->image ?? 'https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}"
                        class="img-fluid item-img">
                </a>
            </div>

            <div class="p-3 d-flex flex-column" style="min-height: 120px;">
                <div class="list-card-body mb-2">
                    <h6 class="mb-2 font-weight-bold" style="font-size: 0.9rem;">
                        <a href="{{ route('product.detail', $product->id) }}" class="text-dark">
                            {{ $product->productTemplate->name ?? $product->name }}
                        </a>
                    </h6>
                    <small class="text-muted d-block mb-1">
                        <i class="icofont-sale-discount text-danger"></i> Đã bán: {{ $product->sold }}
                    </small>

                    @if ($product->display_mode === 'unit')
                        <small class="text-muted">
                            HSD: {{ \Carbon\Carbon::parse($product->expiry_date)->format('d/m/Y') }}
                        </small>
                        @if (isset($product->display_unit_original_price) && $product->display_unit_original_price > 0)
                            <p class="text-success mb-2 d-flex justify-content-between align-items-center fs-6">
                                @if ($product->display_unit_price < $product->display_unit_original_price)
                                    <del class="text-muted mr-1">{{ number_format($product->display_unit_original_price, 0, ',', '.') }} VNĐ</del>
                                    <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                        {{ number_format($product->display_unit_price, 0, ',', '.') }} VNĐ
                                    </span>
                                    @php
                                        $discount = $product->display_unit_original_price - $product->display_unit_price;
                                        $percent = ($product->display_unit_original_price > 0) ? round(($discount / $product->display_unit_original_price) * 100) : 0;
                                    @endphp
                                    @if ($percent > 0)
                                        <span class="badge badge-light text-danger font-weight-bold px-2">
                                            -{{ $percent }}%
                                        </span>
                                    @endif
                                @else
                                    <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                        {{ number_format($product->display_unit_price, 0, ',', '.') }} VNĐ
                                    </span>
                                @endif
                            </p>
                        @else
                                <p class="text-danger mb-2">Hết hàng</p>
                        @endif
                    @elseif ($product->display_mode === 'quantity')
                        <p class="mb-0 text-primary font-weight-bold">
                            Còn: {{ $product->total_available_quantity }} {{ $product->productTemplate->unit ?? 'sản phẩm' }}
                        </p>
                        @if (isset($product->display_original_price) && $product->display_original_price > 0)
                            <p class="text-success mb-2 d-flex justify-content-between align-items-center fs-6">
                                @if ($product->final_display_price < $product->display_original_price)
                                    <del class="text-muted mr-1">{{ number_format($product->display_original_price, 0, ',', '.') }} VNĐ</del>
                                    <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                        {{ number_format($product->final_display_price, 0, ',', '.') }} VNĐ
                                    </span>
                                    @php
                                        $discount = $product->display_original_price - $product->final_display_price;
                                        $percent = ($product->display_original_price > 0) ? round(($discount / $product->display_original_price) * 100) : 0;
                                    @endphp
                                    @if ($percent > 0)
                                        <span class="badge badge-light text-danger font-weight-bold px-2">
                                            -{{ $percent }}%
                                        </span>
                                    @endif
                                @else
                                    <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                        {{ number_format($product->final_display_price, 0, ',', '.') }} VNĐ
                                    </span>
                                @endif
                            </p>
                        @else
                                <p class="text-danger mb-2">Hết hàng</p>
                        @endif
                    @endif
                </div>

                @php
                    $cart = session('cart', []);
                    $cartItem = $cart[$product->id] ?? null;
                @endphp

                <div class="cart-actions mt-auto">
                    @if ($cartItem)
                        <div class="d-flex justify-content-center align-items-center">
                            <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                data-id="{{ $product->id }}"
                                data-qty="{{ $cartItem['quantity'] - 1 }}">
                                <i class="icofont-minus"></i>
                            </button>
                            <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="{{ $product->id }}">
                                {{ $cartItem['quantity'] }}
                            </span>
                            <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                data-id="{{ $product->id }}"
                                data-qty="{{ $cartItem['quantity'] + 1 }}">
                                <i class="icofont-plus"></i>
                            </button>
                        </div>
                    @else
                        <button type="button" class="btn btn-primary btn-sm w-100 btn-add-to-cart" data-id="{{ $product->id }}">
                            <i class="icofont-cart"></i> Thêm vào giỏ hàng
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach