@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="vendor-profile pb-60">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
    integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<div class="container">
    <div class="row">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-body">
                    <ol class="activity-checkout mb-0 px-4 mt-3">
                        <li class="checkout-item">
                            <div class="avatar checkout-icon p-1">
                                <div class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bxs-receipt text-white font-size-20"></i>
                                </div>
                            </div>
                            <div class="feed-item-list">
                                <div>
                                    <h5 class="font-size-16 mb-1">Delivery</h5>
                                    <p class="text-muted text-truncate mb-4">Sed ut perspiciatis unde omnis iste</p>
                                    <div class="mb-3">
                                        <form action="{{ route('payment.transition') }}" method="POST">
                            @csrf
                                        <!--<form>-->
                                            <div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="billing-name">Name:@error('name')<span class="text-danger">*</span>@enderror</label>
                                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                                id="billing-name" placeholder="Enter name" name="name" value="{{old('name')}}">
                                                        </div>
                                                                <!-- @error('name')-->
                                                                <!-- <p class="text-danger">{{ $message }}</p>-->
                                                                <!--@enderror-->
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="billing-email-address">Email Address:@error('email')<span class="text-danger">*</span>@enderror</label>
                                                            <input type="email" class="form-control @error('name') is-invalid @enderror"
                                                                id="billing-email-address"
                                                                placeholder="Enter email" name="email" value="{{old('email')}}">
                                                        </div>
                                                        <!--@error('email')-->
                                                        <!--         <p class="text-danger">{{ $message }}</p>-->
                                                        <!--        @enderror-->
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"
                                                                for="billing-phone">Phone:@error('phone_no')<span class="text-danger">*</span>@enderror</label>
                                                            <input type="text" class="form-control @error('phone_no') is-invalid @enderror"
                                                                id="billing-phone" value="{{old('phone_no')}}" placeholder="Enter Phone no." name="phone_no">
                                                                <!--@error('phone_no')-->
                                                                <!-- <p class="text-danger">{{ $message }}</p>-->
                                                                <!--@enderror-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="billing-address">Address:@error('address')<span class="text-danger">*</span>@enderror</label>
                                                    <textarea class="form-control @error('address') is-invalid @enderror" id="billing-address" rows="3"
                                                        placeholder="Enter full address" name="address" value="{{old('address')}}"></textarea>
                                                        <!--@error('address')-->
                                                        <!--         <p class="text-danger">{{ $message }}</p>-->
                                                        <!--        @enderror-->
                                                        
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-4 mb-lg-0">
                                                            <label class="form-label">Country:@error('country')<span class="text-danger">*</span>@enderror</label>
                                                            <select class="form-control form-select @error('country') is-invalid @enderror"
                                                                title="Country" name="country" value="{{old('country')}}">
                                                                <option value="0" disabled>Select Country</option>
                                                                <option value="AF">Afghanistan</option>
                                                                <option value="AL">Albania</option>
                                                                <option value="DZ">Algeria</option>
                                                                <option value="AS">American Samoa</option>
                                                                <option value="AD">Andorra</option>
                                                                <option value="AO">Angola</option>
                                                                <option value="AI">Anguilla</option>
                                                            </select>
                                                            <!--@error('country')-->
                                                            <!--     <p class="text-danger">{{ $message }}</p>-->
                                                            <!--    @enderror-->
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-4 mb-lg-0">
                                                            <label class="form-label"
                                                                for="billing-city">City:@error('city')<span class="text-danger">*</span>@enderror</label>
                                                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                                id="billing-city" placeholder="Enter City" name="city" value="{{old('city')}}">
                                                                <!--@error('city')-->
                                                                <!-- <p class="text-danger">{{ $message }}</p>-->
                                                                <!--@enderror-->
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-0">
                                                            <label class="form-label" for="zip-code">Zip / Postal
                                                                code:@error('postal_code')<span class="text-danger">*</span>@enderror</label>
                                                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="zip-code"
                                                                placeholder="Enter Postal code" name="postal_code" value="{{old('postal_code')}}">
                                                                <!--@error('postal_code')-->
                                                                <!-- <p class="text-danger">{{ $message }}</p>-->
                                                                <!--@enderror-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!--</form>-->
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="checkout-item">
                            <div class="avatar checkout-icon p-1">
                                <div class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bxs-truck text-white font-size-20"></i>
                                </div>
                            </div>
                            <div class="feed-item-list">
                                <div>
                                    <h5 class="font-size-16 mb-1">Delivery Info</h5>
                                    <p class="text-muted text-truncate mb-4">Neque porro quisquam est</p>
                                    <div class="mb-3">
                                        <div class="row">
                                            @if(isset($address_details))
                                            @php
                                        $counter = 1; 
                                            @endphp
                                            @foreach ($address_details as $details)
                                            <div class="col-lg-4 col-sm-6">
                                                <div data-bs-toggle="collapse">
                                                    <label class="card-radio-label mb-0">
                                                        <input type="radio" name="delivery_address" id="info-address1"
                                                            class="card-radio-input" value="{{ $details->id }}">
                                                        <div class="card-radio text-truncate p-3">
                                                            <span class="fs-14 mb-4 d-block">Address {{$counter}}</span>
                                                            <span class="fs-14 mb-2 d-block">{{$details->name}}</span>
                                                            <span
                                                                class="text-muted fw-normal text-wrap mb-1 d-block">
                                                                {{$details->address}}-{{$details->city}}-{{$details->country}}</span>
                                                            <span class="text-muted fw-normal d-block">Mo.
                                                                {{$details->phone_no}}</span>
                                                        </div>
                                                    </label>
                                                    <div class="edit-btn bg-light  rounded">
                                                        <a href="#" data-bs-toggle="tooltip" data-placement="top"
                                                            title data-bs-original-title="Edit">
                                                            <i class="bx bx-pencil font-size-16"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--<div class="col-lg-4 col-sm-6">-->
                                            <!--    <div>-->
                                            <!--        <label class="card-radio-label mb-0">-->
                                            <!--            <input type="radio" name="address" id="info-address2"-->
                                            <!--                class="card-radio-input">-->
                                            <!--            <div class="card-radio text-truncate p-3">-->
                                            <!--                <span class="fs-14 mb-4 d-block">Address 2</span>-->
                                            <!--                <span class="fs-14 mb-2 d-block">Bradley-->
                                            <!--                    McMillian</span>-->
                                            <!--                <span-->
                                            <!--                    class="text-muted fw-normal text-wrap mb-1 d-block">109-->
                                            <!--                    Clarksburg Park Road Show Low, AZ 85901</span>-->
                                            <!--                <span class="text-muted fw-normal d-block">Mo.-->
                                            <!--                    012-345-6789</span>-->
                                            <!--            </div>-->
                                            <!--        </label>-->
                                            <!--        <div class="edit-btn bg-light  rounded">-->
                                            <!--            <a href="#" data-bs-toggle="tooltip" data-placement="top"-->
                                            <!--                title data-bs-original-title="Edit">-->
                                            <!--                <i class="bx bx-pencil font-size-16"></i>-->
                                            <!--            </a>-->
                                            <!--        </div>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            @php
                                             $counter++; 
                                             @endphp
                                        @endforeach
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="checkout-item">
                            <div class="avatar checkout-icon p-1">
                                <div class="avatar-title rounded-circle bg-primary">
                                    <i class="bx bxs-wallet-alt text-white font-size-20"></i>
                                </div>
                            </div>
                            <div class="feed-item-list">
                                <div>
                                    <h5 class="font-size-16 mb-1">Payment Info</h5>
                                    <p class="text-muted text-truncate mb-4">Duis arcu tortor, suscipit eget</p>
                                </div>
                                <div>
                                    <h5 class="font-size-14 mb-3">Payment method :</h5>
                                    <div class="row">
                                        <div class="col-lg-3 col-sm-6">
                                            <div data-bs-toggle="collapse">
                                                <label class="card-radio-label">
                                                    <input type="radio" value="credit_debit_card" name="pay_method" id="pay-methodoption1"
                                                        class="card-radio-input" >
                                                    <span class="card-radio py-3 text-center text-truncate">
                                                        <i class="bx bx-credit-card d-block h2 mb-3"></i>
                                                        Credit / Debit Card
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div>
                                                <label class="card-radio-label">
                                                    <input type="radio" name="pay_method" id="pay-methodoption3"
                                                        class="card-radio-input" value="cash_on_delivery" checked>
                                                    <span class="card-radio py-3 text-center text-truncate">
                                                        <i class="bx bx-money d-block h2 mb-3"></i>
                                                        <span>Cash on Delivery</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div>
                                                <label class="card-radio-label">
                                                    <input type="radio" value="paypal" name="pay_method" id="pay-methodoption2"
                                                        class="card-radio-input" >
                                                    <span class="card-radio py-3 text-center text-truncate">
                                                        <i class="bx bxl-paypal d-block h2 mb-3"></i>
                                                        Paypal
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row my-4">
                <div class="col">
                    <a href="{{route('merchants')}}" class="btn btn-link text-muted">
                        <i class="mdi mdi-arrow-left me-1"></i> Continue Shopping </a>
                </div>
                <div class="col">
                    <div class="text-end mt-2 mt-sm-0">
                        
                            <input type="hidden" name="total_price" value="{{ $totalPrice }}">
                            @foreach ($orders as $order)
                            <input type="hidden" name="product_id[]" value="{{ $order->product_id }}">
                            <input type="hidden" name="quantity[]" value="{{ $order->quantity }}">
                            <input type="hidden" name="price[]" id="" value="{{$order->product->price}}">
                        @endforeach
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-cart-outline me-1"></i> Proceed
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card checkout-order-summary">
                <div class="card-body">
                    <div class="p-3 bg-light mb-3">
                        <h5 class="font-size-16 mb-0  text-dark ">Order Summary <span class="float-end ms-2">#MN0124</span></h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0 table-nowrap">
                            <thead>
                                <tr>
                                    <th class="border-top-0" style="width: 110px;" scope="col">Product</th>
                                    <th class="border-top-0" scope="col">Product Desc</th>
                                    <th class="border-top-0" scope="col">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                     @foreach ($orders as $order)
                                     <th scope="row"><img src="{{ getImage(imagePath()['product']['path'].'/'.$order->product->image, imagePath()['product']['size']) }}"
                                        alt="product-img" title="product-img" class="avatar-lg rounded"></th>
                                <td>
                                    <h5 class="font-size-16 text-truncate text-dark">{{$order->product->name . ' ' .$order->product->productCate->name}}</h5>
                                    {{-- <p class="text-muted mb-0">
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star text-warning"></i>
                                        <i class="bx bxs-star-half text-warning"></i>
                                    </p> --}}
                                    <p class="text-muted mb-0 mt-1">{{$general->cur_sym .number_format($order->product->price, 2) .' ' .'X'.' ' .$order->quantity}}</p>
                                </td>
                                <td>{{$general->cur_sym .$order->total_price}}</td>
                            </tr>

                                     @endforeach

                                <tr>
                                    <td colspan="2">
                                        <h5 class="font-size-14 m-0 text-dark">Sub Total :</h5>
                                    </td>
                                    <td>
                                       {{$general->cur_sym . $totalPrice}}
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="2">
                                        <h5 class="font-size-14 m-0  text-dark">Discount :</h5>
                                    </td>
                                    <td>
                                        - $ 78
                                    </td>
                                </tr> --}}
                                <tr>
                                    <td colspan="2">
                                        <h5 class="font-size-14 m-0  text-dark">Shipping Charge :</h5>
                                    </td>
                                    <td>
                                        $ 25
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="2">
                                        <h5 class="font-size-14 m-0  text-dark">Estimated Tax :</h5>
                                    </td>
                                    <td>
                                        $ 18.20
                                    </td>
                                </tr> --}}
                                <tr class="bg-light">
                                    <td colspan="2">
                                        <h5 class="font-size-14 m-0  text-dark">Total:</h5>
                                    </td>
                                    <td>
                                        $ {{$totalPrice+25}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>



<style type="text/css">


    .activity-checkout {
        list-style: none
    }

    .activity-checkout .checkout-icon {
        position: absolute;
        top: -4px;
        left: -24px
    }

    .activity-checkout .checkout-item {
        position: relative;
        padding-bottom: 24px;
        padding-left: 35px;
        border-left: 2px solid #f5f6f8
    }

    .activity-checkout .checkout-item:first-child {
        border-color: #3b76e1
    }

    .activity-checkout .checkout-item:first-child:after {
        background-color: #3b76e1
    }

    .activity-checkout .checkout-item:last-child {
        border-color: transparent
    }

    .activity-checkout .checkout-item.crypto-activity {
        margin-left: 50px
    }

    .activity-checkout .checkout-item .crypto-date {
        position: absolute;
        top: 3px;
        left: -65px
    }



    .avatar-xs {
        height: 1rem;
        width: 1rem
    }

    .avatar-sm {
        height: 2rem;
        width: 2rem
    }

    .avatar {
        height: 3rem;
        width: 3rem
    }

    .avatar-md {
        height: 4rem;
        width: 4rem
    }

    .avatar-lg {
        height: 5rem;
        width: 5rem
    }

    .avatar-xl {
        height: 6rem;
        width: 6rem
    }

    .avatar-title {
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        background-color: #3b76e1;
        color: #fff;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        font-weight: 500;
        height: 100%;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 100%
    }

    .avatar-group {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        padding-left: 8px
    }

    .avatar-group .avatar-group-item {
        margin-left: -8px;
        border: 2px solid #fff;
        border-radius: 50%;
        -webkit-transition: all .2s;
        transition: all .2s
    }

    .avatar-group .avatar-group-item:hover {
        position: relative;
        -webkit-transform: translateY(-2px);
        transform: translateY(-2px)
    }

    .card-radio {
        background-color: #fff;
        border: 2px solid #eff0f2;
        border-radius: .75rem;
        padding: .5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block
    }

    .card-radio:hover {
        cursor: pointer
    }

    .card-radio-label {
        display: block
    }

    .edit-btn {
        width: 35px;
        height: 35px;
        line-height: 40px;
        text-align: center;
        position: absolute;
        right: 25px;
        margin-top: -50px
    }

    .card-radio-input {
        display: none
    }

    .card-radio-input:checked+.card-radio {
        border-color: #3b76e1 !important
    }


    .font-size-16 {
        font-size: 16px !important;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    a {
        text-decoration: none !important;
    }


    .form-control {
        display: block;
        width: 100%;
        padding: 0.47rem 0.75rem;
        font-size: .875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #545965;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #e2e5e8;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: 0.75rem;
        -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
    }

    .edit-btn {
        width: 35px;
        height: 35px;
        line-height: 40px;
        text-align: center;
        position: absolute;
        right: 25px;
        margin-top: -50px;
    }

    .ribbon {
        position: absolute;
        right: -26px;
        top: 20px;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        padding: 1px 22px;
        font-size: 13px;
        font-weight: 500
    }
</style>

</section>
@endsection

