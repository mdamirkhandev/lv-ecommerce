@extends('user.layouts.app')
@section('content')
    <main>
        <section class="section-5 pt-3 pb-3 mb-3 bg-white">
            <div class="container">
                <div class="light-font">
                    <ol class="breadcrumb primary-color mb-0">
                        <li class="breadcrumb-item"><a class="white-text" href="{{ route('user.index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="white-text" href="{{ route('user.shop') }}">Shop</a></li>
                        <li class="breadcrumb-item">Cart</li>
                    </ol>
                </div>
            </div>
        </section>

        <section class=" section-9 pt-4">
            <div class="container">
                <div class="row">
                    @if (Cart::count() > 0)
                        <div class="col-md-8">
                            @if (session('success'))
                                <x-alert type="success" :message="session('success')" />
                            @elseif(session('error'))
                                <x-alert type="danger" :message="session('error')" />
                            @endif
                            <div class="table-responsive">
                                <table class="table" id="cart">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($cartContent))
                                            @foreach ($cartContent as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            @if (!empty($item->options->productImage->image))
                                                                <img class="thumbnail"
                                                                    src="{{ asset('uploads/product/small/' . $item->options->productImage->image) }}"
                                                                    class="img-thumbnail" width="50" />
                                                            @else
                                                                <img class="thumbnail"
                                                                    src="{{ asset('uploads/product/default_image150x150.png') }}"
                                                                    class="img-thumbnail" width="50" />
                                                            @endif
                                                            <h2>{{ $item->name }}</h2>
                                                        </div>
                                                    </td>
                                                    <td>${{ $item->price }}</td>
                                                    <td>
                                                        <div class="input-group quantity mx-auto" style="width: 100px;">
                                                            <div class="input-group-btn">
                                                                <button
                                                                    class="btn btn-sm btn-dark btn-minus p-2 pt-1 pb-1 sub"
                                                                    data-id="{{ $item->rowId }}">
                                                                    <i class="fa fa-minus"></i>
                                                                </button>
                                                            </div>
                                                            <input type="text"
                                                                class="form-control form-control-sm  border-0 text-center"
                                                                value="{{ $item->qty }}">
                                                            <div class="input-group-btn">
                                                                <button
                                                                    class="btn btn-sm btn-dark btn-plus p-2 pt-1 pb-1 add"
                                                                    data-id="{{ $item->rowId }}">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        ${{ $item->price * $item->qty }}
                                                    </td>
                                                    <td>
                                                        <button onclick="removeFromCart('{{ $item->rowId }}')"
                                                            class="btn btn-sm btn-danger"><i
                                                                class="fa fa-times"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card cart-summery">
                                <div class="sub-title">
                                    <h2 class="bg-white">Cart Summery</h3>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <div>Subtotal</div>
                                        <div>${{ Cart::subtotal() }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <div>Shipping</div>
                                        <div>$0</div>
                                    </div>
                                    <div class="d-flex justify-content-between summery-end">
                                        <div>Total</div>
                                        <div>${{ Cart::subtotal() }} </div>
                                    </div>
                                    <div class="pt-5">
                                        <a href="{{ route('user.checkout') }}" class="btn-dark btn btn-block w-100">Proceed
                                            to Checkout</a>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group apply-coupan mt-4">
                                <input type="text" placeholder="Coupon Code" class="form-control">
                                <button class="btn btn-dark" type="button" id="button-addon2">Apply Coupon</button>
                            </div>
                        </div>
                    @else
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="text-center">Your cart is empty</h4>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>
    </main>
@endsection
@section('customJS')
    <script>
        $('.add').click(function() {
            var qtyElement = $(this).parent().prev(); // Qty Input
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue < 10) {
                var rowId = $(this).data('id');
                qtyElement.val(qtyValue + 1);
                var newQty = qtyElement.val();
                updateCart(rowId, newQty)
            }
        });

        $('.sub').click(function() {
            var qtyElement = $(this).parent().next();
            var qtyValue = parseInt(qtyElement.val());
            if (qtyValue > 1) {
                var rowId = $(this).data('id');
                qtyElement.val(qtyValue - 1);
                var newQty = qtyElement.val();
                updateCart(rowId, newQty)
            }
        });
        //update cart

        function updateCart(rowId, qty) {
            $.ajax({
                url: "{{ route('updateCart') }}",
                type: 'post',
                dataType: 'json',
                data: {
                    rowId: rowId,
                    qty: qty
                },
                success: function(data) {
                    location.reload();
                }
            });
        }

        function removeFromCart(rowId) {
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: "{{ route('removeFromCart') }}",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        rowId: rowId,
                    },
                    success: function(data) {
                        location.reload();
                    }
                });
            }
        }
    </script>
@endsection
