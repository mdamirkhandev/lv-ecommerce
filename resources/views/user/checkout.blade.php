@extends('user.layouts.app')
@section('content')
    <main>
        <section class="section-5 pt-3 pb-3 mb-3 bg-white">
            <div class="container">
                <div class="light-font">
                    <ol class="breadcrumb primary-color mb-0">
                        <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                        <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                        <li class="breadcrumb-item">Checkout</li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="section-9 pt-4">
            <div class="container">
                <form action="" id="orderForm" name="orderForm" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="sub-title">
                                <h2>Shipping Address</h2>
                            </div>
                            <div class="card shadow-lg border-0">
                                <div class="card-body checkout-form">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="first_name" id="first_name" class="form-control"
                                                    placeholder="First Name"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->first_name : '' }}">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="last_name" id="last_name" class="form-control"
                                                    placeholder="Last Name"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->last_name : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="email" id="email" class="form-control"
                                                    placeholder="Email"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->email : '' }}">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="number" name="mobile" id="mobile" class="form-control"
                                                    placeholder="Mobile No."
                                                    value="{{ !empty($customerDetails) ? $customerDetails->mobile : '' }}">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <select name="country" id="country" class="form-control">
                                                    <option value="">Select a Country</option>
                                                    @if (!empty($countries))
                                                        @foreach ($countries as $country)
                                                            <option
                                                                {{ !empty($customerDetails && $customerDetails->country_id == $country->id) ? 'selected' : '' }}
                                                                value="{{ $country->id }}">{{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <textarea name="address" id="address" cols="30" rows="3" placeholder="Full Address" class="form-control">{{ !empty($customerDetails) ? $customerDetails->address : '' }}</textarea>
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="apartment" id="apartment" class="form-control"
                                                    placeholder="Apartment, suite, unit, etc. (optional)"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->apartment : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" name="city" id="city" class="form-control"
                                                    placeholder="City"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->city : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" name="state" id="state" class="form-control"
                                                    placeholder="State"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->state : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="number" name="zip" id="zip" class="form-control"
                                                    placeholder="Zip"
                                                    value="{{ !empty($customerDetails) ? $customerDetails->zip : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <textarea name="order_notes" id="order_notes" cols="30" rows="2" placeholder="Order Notes (optional)"
                                                    class="form-control"></textarea>
                                                <p></p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sub-title">
                                <h2>Order Summery</h3>
                            </div>
                            <div class="card cart-summery">
                                <div class="card-body">
                                    @foreach (Cart::content() as $item)
                                        <div class="d-flex justify-content-between pb-2">
                                            <div class="h6">{{ $item->name }} X {{ $item->qty }}</div>
                                            <div class="h6">${{ $item->price * $item->qty }}</div>
                                        </div>
                                    @endforeach

                                </div>
                                <div class="d-flex justify-content-between summery-end">
                                    <div class="h6"><strong>Subtotal</strong></div>
                                    <div class="h6"><strong>${{ Cart::subtotal() }}</strong></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="h6"><strong>Shipping</strong></div>
                                    <div class="h6"><strong>$0</strong></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 summery-end">
                                    <div class="h5"><strong>Total</strong></div>
                                    <div class="h5"><strong>${{ Cart::subtotal() }}</strong></div>
                                </div>
                            </div>


                            <div class="card payment-form ">
                                <h3 class="card-title h5 mb-3">Payment Details</h3>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="payment_method_one" value="cod" checked>
                                    <label class="form-check-label" for="payment_1">
                                        Cash on delivery
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="payment_method_two" value="card">
                                    <label class="form-check-label" for="payment_2">
                                        Credit/Debit Card
                                    </label>
                                </div>
                                <div class="card-body p-0 d-none" id="card-payment-form">
                                    <div class="mb-3">
                                        <label for="card_number" class="mb-2">Card Number</label>
                                        <input type="text" name="card_number" id="card_number"
                                            placeholder="Valid Card Number" class="form-control">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="expiry_date" class="mb-2">Expiry Date</label>
                                            <input type="text" name="expiry_date" id="expiry_date"
                                                placeholder="MM/YYYY" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="expiry_date" class="mb-2">CVV Code</label>
                                            <input type="text" name="expiry_date" id="expiry_date" placeholder="123"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    {{-- <a href="#" class="btn-dark btn btn-block w-100">Proceed</a> --}}
                                    <button type="submit" class="btn-dark btn btn-block w-100">Proceed</button>
                                </div>
                            </div>
                        </div>

                        <!-- CREDIT CARD FORM ENDS HERE -->

                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection
@section('customJS')
    <script>
        $(document).ready(function() {
            $('#payment_method_one').on('change', function() {
                $('#card-payment-form').addClass('d-none');
            });
            $('#payment_method_two').on('change', function() {
                $('#card-payment-form').removeClass('d-none');
            });
        });
        $('#orderForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $('button[type=submit]').prop('disabled', true);
            $.ajax({
                url: "{{ route('user.orderSubmit') }}",
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    var errors = res['errors'];
                    $('button[type=submit]').prop('disabled', false);
                    if (res.status == false) {
                        if (errors) {
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid')
                                    .siblings('p')
                                    .addClass('invalid-feedback')
                                    .html(value)
                            });
                        } else {
                            //remove class
                            $.each(errors, function(key, value) {
                                $('#' + key).removeClass('is-invalid')
                                    .siblings('p')
                                    .removeClass('invalid-feedback')
                                    .html('')
                            });
                        }
                    } else {
                        window.location.href = "{{ url('/thankyou/') }}/" + res.orderID;
                    }

                }
            });
        });
    </script>
@endsection
