@extends($activeTemplate.'layouts.frontend')

@section('content')
          <!-- Product -->
          <link href=" {{ asset('assets/ninja-slider.css') }}" rel="stylesheet" type="text/css" />
          <script src=" {{ asset('assets/ninja-slider.js') }}" type="text/javascript"></script>
            <style>
                .gallery img{
                    width:130px;
                    cursor:pointer;
                    
                }
                .badge {
  background-color: #d1dade;
  color: #5e5e5e;
  font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
  font-size: 11px;
  font-weight: 600;
  padding-bottom: 4px;
  padding-left: 6px;
  padding-right: 6px;
  border-radius: 50%;
  text-shadow: none;
  white-space: nowrap;
}
.badge-success {
  background-color: #42d697;
  color: #42d697;
}
.badge-warning {
  background-color: #f8ac59;
  color: #f8ac59;
}
.badge-danger {
  background-color: #ed5565;
  color: #ed5565;
}
.badge-info {
  background-color: #23c6c8;
  color: #23c6c8;
}
            </style>
          <section class="product-section pt-120 pb-120">
            <div class="container">
                <div class="row gy-5 justify-content-between">
                    <div class="col-lg-8">
                        <div class="product__single-item">
                            <div class="product-thumb-area mb-5">
                                 <div class="product-thumb pe-md-4"">
                                    <h6 class="title mt-2 mb-2 text--danger">Note :</h6>
                                    <span class="badge badge-danger" >0</span> Amount is minimun then actual price<br>
                                    <span class="badge badge-warning">0</span> Amount is minimun then latest bid amount<br>
                                    <span class="badge badge-success">0</span> Ready to go
                                   
                                </div>
                                <div class="product-thumb pe-md-4 mb-5">
                                </div>
                                <div class="product-thumb pe-md-4 ">
                                    <div class="mb-3">
                                        <span class="pe-3"  style="color: black;font-size: 14px;">Current Bid: <strong>{{ $latestBid ? number_format($latestBid->amount, 2) : "No bidding" }}</strong></span>
                                         <span class="pe-3"  style="color: black;font-size: 14px;">Ends In: <strong>{{ $product->expired_at->diffInDays(now()) }}Day</strong>
                                        </span>
                                        |
                                        <span  class="fas fa-comment text-danger"></span>

                                          <span  style="color: black;font-size: 14px;">{{$product_comments->count()}}</span>
                                    </div>


                                    <img src="{{getImage(imagePath()['product']['path'].'/'.$product->image,imagePath()['product']['size'])}}" alt="product">
                                    <div class="meta-post mt-4">
                                        <div class="meta-item me-sm-auto">
                                            <span class="text--base"><i class="las la-gavel"></i></span> {{ __($product->total_bid) }}
                                        </div>
                                        <div class="meta-item me-0">
                                            <!-- <span class="text--base"><i class="lar la-share-square"></i></span> -->
                                            <ul class="social-share">
                                                <li>
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" title="@lang('Facebook')" target="blank"><i class="fab fa-facebook"></i></a>
                                                </li>

                                                <li>
                                                    <a href="http://pinterest.com/pin/create/button/?url={{urlencode(url()->current()) }}&description={{ __($product->name) }}&media={{ getImage('assets/images/product/'. @$product->main_image) }}" title="@lang('Pinterest')" target="blank"><i class="fab fa-pinterest-p"></i></a>
                                                </li>

                                                <li>
                                                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{urlencode(url()->current()) }}&amp;title={{ __($product->name) }}&amp;summary={{ shortDescription(__($product->summary)) }}" title="@lang('Linkedin')" target="blank"><i class="fab fa-linkedin"></i></a>
                                                </li>

                                                <li>
                                                    <a href="https://twitter.com/intent/tweet?text={{ __($product->name) }}%0A{{ url()->current() }}" title="@lang('Twitter')" target="blank">
                                                        <i class="fab fa-twitter"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" title="@lang('Map')">
                                                        <i class="fas fa-map-marker-alt" id="showLocationButton"></i>
                                                    </a>
                                                </li>
                                            </ul>

                                        </div>

                                    </div>
                                </div>

                                <div class="product-content">
                                    <div>
                                    <h5 class="title mt-0 mb-2" style="display: inline-block;">{{ __($product->name) }}</h5>
                                    <span class="badge badge-danger" id="Minimum_Amount" title="Amount is minimun then actual price">0</span>
                                            <span class="badge badge-warning" id="Bid_Amount" title="Amount is minimun then latest bid amount">0</span>
                                            <span class="badge badge-success" title=""id="Ready_togo">0</span>
                                            </div>
                                    <div class="ratings mb-4">
                                        @php echo displayAvgRating($product->avg_rating); @endphp
                                        ({{ $product->review_count }})
                                    </div>
                                    <p class="mb-4 mt-0" style="color: #565656;">
                                        {{ __(shortDescription($product->short_description)) }}
                                    </p>
                                    <div class="product-price">
                                        <div class="text-dark fw-bold">
                                            {{ showAmount($product->price) }} <span class="text--base">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                    
                                            
                                    @if ($product->status == 1 && $product->started_at < now() && $product->expired_at > now())
                                        <div class="btn__area">
                                            <div class="cart-plus-minus input-group w-auto">
                                                <span class="input-group-text bg--base border-0 text-white">{{ $general->cur_sym }}</span>
                                                <input type="number" placeholder="@lang('Enter your amount')" class="form-control" id="amount" min="{{$product->price}}" step="any">
                                            </div>
                                            
                                            <div>
                                                @auth

                                                <button class="cmn--btn btn--sm bid_now" id="Bid-Now"data-cur_sym="{{ $general->cur_sym }}">@lang('Bid Now')</button>
                                                 @else
                                                 <a type="submit" data-cur_sym="{{ $general->cur_sym }}" href="{{route('user.login')}}" class="cmn--btn btn--sm">Login</a>
                                                @endauth

                                            </div>
                                            <span class="text--danger empty-message">@lang('Please enter an amount to bid')</span>
                                        </div>
                                    @endif
                                </div>
                               
                            </div>
                                  <div style="border-bottom: 1px solid rgba(0,0,0,.125);;"></div>
                            <div class="row mt-3">
                                <div class="col-md-4 col-sm-6 mb-3 ">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <p class="pt-1" style="color: #000;">  Make:
                                                <span class="watch-bell ps-2">{{ $product->Make }}</span></p>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-4 col-sm-6 mb-3 ">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <p class="pt-1" style="color: #000;">   Model:
                                                <span class="ps-2">{{ $product->Model }}</span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3 ">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <p class="pt-1" style="color: #000;">  Year:
                                                <span class="ps-2">{{ $product->Year }}</span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                                  <div style="border-bottom: 1px solid rgba(0,0,0,.125);;"></div>

                    <!-- START Photo Gallery -->
                    <div class="container-fluid mt-4">
                        @if ($images->isNotEmpty())
                            <h4 class="mb-3 mt-4"   style="color: black;">Photo Gallery</h4>
                            <div class="mb-4" style="border-bottom: 1px solid rgba(0,0,0,.125);;"></div>

                            <div class="row">
                                <!--  -->
                                <div style="display:none;">
                                    <div id="ninja-slider">
                                        <div class="slider-inner">
                                            <ul>
                                                @foreach ($images as $image)
                                                    <li>
                                                        {{-- <a class="ns-img" href="{{asset($image->image) }}"></a> --}}
                                                        <img class="ns-img" src="{{ asset($image->image) }}"/>

                                                    </li>
                                                @endforeach
                                                {{-- <li>
                                                    <a class="ns-img" href="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg"></a>
                                                </li>
                                                <li>
                                                    <a class="ns-img" href="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg"></a>
                                                </li>
                                                <li>
                                                    <a class="ns-img" href="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg"></a>
                                                </li>
                                                <li>
                                                    <a class="ns-img" href="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg"></a>
                                                </li>
                                                <li>
                                                    <a class="ns-img" href="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg"></a>
                                                </li>
                                                <li>
                                                    <a class="ns-img" href="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg"></a>
                                                </li> --}}
                                            </ul>
                                            <div id="fsBtn" class="fs-icon" title="Expand/Close"></div>
                                        </div>
                                    </div>
                                </div>
                                <div >

                                    <div class="gallery">
                                        @foreach ($images as $image)
                                        <img class="pt-3 pe-2" src="{{ asset($image->image) }}" onclick="lightbox({{ $image->images_id  }})" />

                                        {{-- <img class="pt-3 pe-2" src="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg" onclick="lightbox(1)" />
                                        <img class="pt-3 pe-2" src="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg" onclick="lightbox(2)" />
                                        <img class="pt-3 pe-2" src="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg" onclick="lightbox(3)" />
                                        <img class="pt-3 pe-2" src="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg" onclick="lightbox(4)" />
                                        <img class="pt-3 pe-2" src="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg" onclick="lightbox(5)" />
                                        <img class="pt-3 pe-2" src="https://beep.hssolsdemos.com/public/assets/product/car2.jpeg" onclick="lightbox(6)" /> --}}
                                        @endforeach
                                    </div>
                                </div>
                                <!--  -->



                                @foreach ($images as $image)
                                <!-- <div class="col-md-2">
                                    <div class="card" >
                                        <a href="#">
                                            <img src="{{ asset($image->image) }}" alt="Image 1" class="card-img-top">
                                        </a>
                                    </div>
                                </div> -->
                                @endforeach
                            </div>
                        @else
                            <p></p>
                        @endif
                    </div>

                    <!-- END Photo Gallery -->


                            <div class="max-banner mb-4">
                                @php
                                    showAd('780x80')
                                @endphp
                            </div>

                            <div class="content mt-5" style="">
                                <ul class="nav nav-tabs nav--tabs">
                                    <li>
                                        <a href="#description" class="active" style="color: black" data-bs-toggle="tab">@lang('Description')</a>
                                    </li>
                                    <li>
                                        <a href="#specification" data-bs-toggle="tab" style="color: black">@lang('Specification')</a>
                                    </li>
                                    <li>
                                        <a href="#reviews" data-bs-toggle="tab" style="color: black">@lang('Reviews')({{ $product->reviews->count() }})</a>
                                    </li>
                                    <li>
                                        <a href="#related-products" data-bs-toggle="tab" style="color: black">@lang('Related Products')</a>
                                    </li>
                                </ul>
                                <div class="tab-content" style="">
                                    <div class="tab-pane fade fade  show active" id="description" style="color: #565656;">
                                        @php
                                            echo $product->long_description
                                        @endphp
                                    </div>
                                    <div class="tab-pane fade" id="specification" style="color: #565656;">
                                        <div class="specification-wrapper">
                                            <h5 class="title">@lang('Specification')</h5>
                                            <div class="table-wrapper">
                                                <table class="specification-table">
                                                    <tr>
                                                        <th>@lang('Category')</td>
                                                        <td>{{ __($product->category->name) }}</td>
                                                    </tr>
                                                    @if ($product->specification)
                                                        @foreach ($product->specification as $spec)
                                                            <tr>
                                                                <th>{{ __($spec['name']) }}</th>
                                                                <td>{{ __($spec['value']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="reviews" style="color: #565656;">
                                        <div class="review-area"></div>

                                        @if($product->bids->where('user_id', auth()->id())->count())
                                            @php $review = $product->reviews->where('user_id', auth()->id())->first(); @endphp
                                            <div class="add-review pt-4 pt-sm-5">
                                                <h5 class="title bold mb-3 mb-lg-4">
                                                    @if($review)
                                                        @lang('Update Your Review')
                                                    @else
                                                        @lang('Add Review')
                                                    @endif
                                                </h5>
                                                <form action="{{ route('user.product.review.store') }}" method="POST" class="review-form rating row">
                                                    @csrf
                                                    <input type="hidden" value="{{ $product->id }}" name="product_id">


                                                    <div class="review-form-group mb-20 col-md-6 d-flex flex-wrap">
                                                        <label class="review-label mb-0 me-3">@lang('Your Rating') :</label>
                                                        <div class="rating-form-group">
                                                            <label class="star-label">
                                                                <input type="radio" name="rating" value="1" {{ ($review && $review->rating ==1) ? 'checked': ''  }} />
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                            </label>
                                                            <label class="star-label">
                                                                <input type="radio" name="rating" value="2" {{ ($review && $review->rating ==2) ? 'checked': ''  }} />
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                            </label>
                                                            <label class="star-label">
                                                                <input type="radio" name="rating" value="3" {{ ($review && $review->rating ==3) ? 'checked': ''  }} />
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                            </label>
                                                            <label class="star-label">
                                                                <input type="radio" name="rating" value="4" {{ ($review && $review->rating ==4) ? 'checked': ''  }} />
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                            </label>
                                                            <label class="star-label">
                                                                <input type="radio" name="rating" value="5" {{ ($review && $review->rating ==5) ? 'checked': ''  }} />
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                                <span class="icon"><i class="las la-star"></i></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="review-form-group mb-20 col-12 d-flex flex-wrap">

                                                        <textarea name="description" placeholder="@lang('Write your review')..." class="form-control form--control" id="review-comments">{{ $review ? __($review->description) : old('description') }}</textarea>
                                                    </div>

                                                    <div class="review-form-group mb-20 col-12 d-flex flex-wrap">
                                                        <button type="submit" class="cmn--btn w-100">@lang('Submit Review')</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="tab-pane" id="related-products" style="color: #565656;">
                                        <div class="slide-wrapper">
                                            <div class="related-slider owl-theme owl-carousel">
                                                @foreach ($relatedProducts as $relatedProduct)
                                                    <div class="slide-item">
                                                        <div class="auction__item bg--body">
                                                            <div class="auction__item-thumb">
                                                                <a href="{{ route('product.details', [$relatedProduct->id, slug($relatedProduct->name)]) }}">
                                                                    <img src="{{getImage(imagePath()['product']['path'].'/thumb_'.$relatedProduct->image,imagePath()['product']['thumb'])}}" alt="auction">
                                                                </a>
                                                                <span class="total-bids">
                                                                    <span><i class="las la-gavel"></i></span>
                                                                    <span>@lang('x') {{ ($relatedProduct->total_bid) }} @lang('Bids')</span>
                                                                </span>
                                                            </div>
                                                            <div class="auction__item-content">
                                                                <h6 class="auction__item-title">
                                                                    <a href="{{ route('product.details', [$relatedProduct->id, slug($relatedProduct->name)]) }}">{{ __($relatedProduct->name) }}</a>
                                                                </h6>
                                                                <div class="auction__item-countdown">
                                                                    <div class="inner__grp">
                                                                        <ul class="countdown" data-date="{{ showDateTime($relatedProduct->expired_at, 'm/d/Y H:i:s') }}">
                                                                            <li>
                                                                                <span class="days">@lang('00')</span>
                                                                            </li>
                                                                            <li>
                                                                                <span class="hours">@lang('00')</span>
                                                                            </li>
                                                                            <li>
                                                                                <span class="minutes">@lang('00')</span>
                                                                            </li>
                                                                            <li>
                                                                                <span class="seconds">@lang('00')</span>
                                                                            </li>
                                                                        </ul>
                                                                        <div class="total-price">
                                                                            {{ $general->cur_sym }}{{ showAmount($relatedProduct->price) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="auction__item-footer">
                                                                    <a href="{{ route('product.details', [$relatedProduct->id, slug($relatedProduct->name)]) }}" class="cmn--btn w-100">@lang('Details')</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="max-banner mt-5">
                                    @php
                                        showAd('780x80')
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <aside class="product-single-sidebar ms-xl-3 ms-xxl-5">

                            <div class="countdown-area bg--section mb-4 shadow" style="">
                                <li class="pt-2 " style="list-style: none; text-align: center; ">
                                    <span style="color: #000;"> @lang('Auction Time') </span>
                                 </li>
                                 <hr>
                                <ul class="countdown sidebar-countdown pt-3" data-date="{{ showDateTime($product->expired_at, 'm/d/Y H:i:s') }}">

                                    <li>
                                        <span class="days text-dark">@lang('00')</span>
                                    </li>
                                    <li>
                                        <span class="hours text-dark">@lang('00')</span>
                                    </li>
                                    <li>
                                        <span class="minutes text-dark">@lang('00')</span>
                                    </li>
                                    <li>
                                        <span class="seconds text-dark">@lang('00')</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="seller-area bg--section mb-4 shadow">
                                <h6 class="about-seller mb-4">
                                    @lang('About Seller')
                                </h6>
                                @php
                                    $admin = $product->admin_id != 0 ? true : false;
                                @endphp
                                <a href="{{ $admin ? route('admin.profile.view', [$product->admin->id, slug(@$general->merchant_profile->name)]) : route('merchant.profile.view', [$product->merchant->id, slug($product->merchant->fullname)]) }}" class="seller">
                                    <div class="thumb">
                                        @if ($admin)
                                            <img src="{{getImage(imagePath()['profile']['admin']['path'].'/'.$general->merchant_profile->image, null, true)}}" alt="winner">
                                        @else
                                            <img src="{{getImage(imagePath()['profile']['merchant']['path'].'/'.$product->merchant->image, null, true)}}" alt="winner">
                                        @endif

                                    </div>
                                    <div class="cont">
                                        <h6 class="title">{{ __($admin ? @$general->merchant_profile->name : $product->merchant->fullname) }}</h6>
                                    </div>
                                   </a>
                                   <hr>
                                <ul class="seller-info mt-4">
                                    <li style="color: #000;">
                                        @lang('Since'): <span class="text-secondary">{{ showDateTime($admin ? $product->admin->created_at : $product->merchant->created_at, 'd M Y') }}</span>
                                    </li>

                                    @if(!$admin)
                                    <li style="color: #000;">
                                        <div class="ratings">
                                            @php
                                                echo displayAvgRating($star = $admin ? $product->admin->avg_rating : $product->merchant->avg_rating)
                                            @endphp
                                            ({{ __($admin ? $product->admin->review_count : $product->merchant->review_count) }})
                                        </div>
                                    </li>
                                    @endif
                                    <li style="color: #000;">
                                        @lang('Total Products') : <span class="text-secondary">{{ $admin ? $product->admin->products->count() : $product->merchant->products->count() }}</span>
                                    </li>


                                    <li style="color: #000;">
                                        @lang('Total Sale') : <span class="text-secondary">{{ $admin ? $product->admin->products->sum('total_bid') : $product->merchant->products->sum('total_bid') }}</span>
                                    </li>

                                </ul>
                            </div>
                            <div class="mini-banner">
                                @php
                                    showAd('370x670')
                                @endphp
                            </div>
                        </aside>
                    </div>

                    <div class="container-fluid" style="color: black;">
                       <div class="row">
                              <div class="col-md-6">
                                <div class="card no-border-card">
                                    <div class="card-header">
                                        <h6 class="card-title" style="color: black">Bid on This Listing</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="col-md-1">Current Bid</th>
                                                    <td class="col-md-4">
                                                        USD <strong>{{$latestBid ? number_format($latestBid->amount, 2) : "No biding" }}</strong> by {{ $userName }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="col-md-1">Time Left</th>
                                                    <td class="col-md-4">
                                                        {{ $product->expired_at->diff(now())->format('%d days, %h hours, %i minutes, %s seconds') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="col-md-1">Ends On</th>
                                                    <td class="col-md-4">
                                                        {{ $product->expired_at->format('l, F j \a\t g:ia') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="col-md-1">Bids</th>
                                                    <td class="col-md-4">{{ $product->bids->count() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                              </div>
                       </div>
                    </div>
                    <div class="container-fluid" style="color:black;">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Submit button with margin -->
                                        @auth
                                        <span style="color: black; font-size: 18px;">{{$product_comments->count()}}</span>
                                        <span style="color: black;"><h6 style="color: black; display: inline-block; margin-left: 5px;">Comments</h6></span>


                                            <form id="addComment"  method="POST">
                                                @csrf
                                                <textarea style="background-color: white; color:black;" name="comment" placeholder="Add a comment here" id="comment" rows="3" class="form-control mt-3"></textarea>
                                                <input type="hidden" name="product_id" value="{{$product->id}}">
                                                <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                                <button type="submit" class="btn btn-outline-danger mt-3">Submit</button>
                                            </form>
                                            @endauth
                                    @guest
                                    <span style="color: black; font-size: 18px;">{{$product_comments->count()}}</span>
                                    <span style="color: black;"><h6 style="color: black; display: inline-block; margin-left: 5px;">Comments</h6></span>

                                        <textarea style="background-color: white; color:black;" name="comment" placeholder="Add a comment here" id="comment" rows="3" class="form-control mt-3"></textarea>
                                        <!-- Submit button with margin -->
                                        <a type="submit" href="{{route('user.login')}}" class="btn btn-outline-danger mt-3">Submit</a>
                                    @endguest
                                    </div>
                                </div>
                            </div>
                        </div>


                   </div>
                   <div class="container-fluid" style="color: black">

                                <div class="row mt-2">
                                    @foreach ($product_comments as $comment)
                                    <div class="col-md-6 mt-3">
                                     <div class="card">
                                        <div class="card-body p-4">
                                            <p><span><strong><i class="fas la-user"></i> {{$comment->user->firstname}}</strong></span></p>
                                            <p>  <span style="color: black;font-size: 12px;"><i class="far fa-calendar"></i> {{ \Carbon\Carbon::parse($comment->created_at)->format('M d, Y \a\t h:i A') }}</span></p>
                                                <p class="mt-2">{{$comment->comment}}</p>
                                        </div>
                                     </div>

                                        </div>
                                        @endforeach
                                </div>



                   </div>
                    <div class="container-fluid" style="color: black">
                        <div class="row">
                         @foreach ($product->bids as $bids)
                         <div class="col-md-3">
                             {{$bids->created_at }}
                         </div>
                         <div class="col-md-4" style="font-weight: bold; color: black; background-color:#faf3b6;">
                            <span>${{number_format($bids->amount,2) ?? "No biding"}}</span>
                            <span >bid placed by</span>
                            {{ $bids->user->firstname}}
                        </div>

                       </div>
                         @endforeach
                       </div>
                 </div>

            </div>

        </section>
        <!-- Product -->
        <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                     <div class="modal-header">
                        {{-- <h5 class="modal-title" id="locationModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                    </div>
                    <div class="modal-body">
                        <div id="map" style="width: 100%; height: 300px;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="closeButton" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>




        <div class="modal fade" id="bidModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Confirmation Alert')</h5>
                        <button class="btn text--danger modal-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    @auth
                    <form action="{{ route('user.bid') }}" method="POST">
                        @csrf
                        <input type="hidden" class="amount" name="amount" required>
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="modal-body">
                            <h6 class="message text-dark"></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--danger" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--base">@lang('Yes')</button>
                        </div>
                    </form>
                    @else
                    <form action="" method="POST">
                        @csrf
                        <input type="hidden" class="amount" name="amount" required>
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="modal-body">
                            <h6 class="message"></h6>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--danger" data-bs-dismiss="modal">@lang('No')</button>
                            <a type="submit" href="{{route('user.login')}}" class="btn btn-danger mt-2">Yes</a>
                        </div>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
@endsection

@push('style')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }
        .no-border-card {
        border: none;
    }
        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
    </style>
@endpush

@push('script')

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>

<script>
$(document).ready(function () {
    $("#addComment").on("submit", function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Serialize the form data
        var formData = $(this).serialize();

        // Send an AJAX POST request to the server
        $.ajax({
            url: "{{ route('comment.create')}}", // Replace with the actual URL where you want to submit the form
            type: "POST",
            data: formData,
            success: function (response) {
                // Reload the current page after a successful AJAX request
                window.location.reload();
            },
            error: function (error) {
                // Handle errors here
            }
        });
    });
});

    var closeButton = document.getElementById('closeButton');
    closeButton.addEventListener('click', function() {
       $('#locationModal').modal('hide');
    });

// Function to initialize the map
function initialize() {
    var latitude = {{ $product->latitude }};
    var longitude = {{ $product->longitude }};

    // Create a LatLng object
    var latlng = new google.maps.LatLng(latitude, longitude);

    var map = new google.maps.Map(document.getElementById('map'), {
        center: latlng,
        zoom: 13
    });

    var marker = new google.maps.Marker({
        map: map,
        position: latlng,
        draggable: false,
        anchorPoint: new google.maps.Point(0, -29)
    });

    var infowindow = new google.maps.InfoWindow();

    google.maps.event.addListener(marker, 'click', function() {
        // Use the Geocoder to fetch the location name
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'location': latlng }, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    var locationName = results[0].formatted_address;
                    var iwContent = '<div id="iw_container">' +
                        '<div class="iw_title"><b>Location:</b> ' + locationName + '</div></div>';

                    infowindow.setContent(iwContent);
                    infowindow.open(map, marker);
                }
            } else {
                console.error('Geocoder failed due to: ' + status);
            }
        });
    });
}


    // Listen for the button click event and trigger the modal
    document.getElementById('showLocationButton').addEventListener('click', function () {
        // Show the modal
        $('#locationModal').modal('show');

        // Initialize the map
        initialize();
    });
        (function ($) {
        "use strict";
        var pid = '{{ $product->id }}';
        loadData(pid);

        function loadData(pid, url="{{ route('product.review.load') }}") {
            $.ajax({
                url: url,
                method: "GET",
                data: { pid: pid },
                success: function (data) {
                    $('#load_more_button').remove();
                    $('.review-area').append(data);
                }
            });
        }
        $(document).on('click', '#load_more_button', function () {
            var id  = $(this).data('id');
            var url = $(this).data('url');
            $('#load_more_button').html(`<b>{{ __('Loading') }} <i class="fa fa-spinner fa-spin"></i> </b>`);
            loadData(pid, url);
        });

</script>
<script>
 $(document).ready(function() {
     var minimumbid='{{$product->price}}';
     var highestbid = '{{$highestBid?->amount}}';
     var roundedMinimumBid = Math.round(minimumbid * 100) / 100;
    var roundedHighestBid = Math.round(highestbid * 100) / 100;
    //  console.log(highestbid);
    //  console.log(minimumbid);
        $("#Minimum_Amount").hide();
        $("#Bid_Amount").hide();
        $("#Ready_togo").hide();
   
         $('.empty-message').hide();
        $('#Bid-Now').on('click', function () {
            var modal = $('#bidModal');
            var cur_sym = $(this).data('cur_sym');
            var amount = $('#amount').val();
     //alert(amount);
            // alert(minimumbid);
            
            if(!amount){
               // console.log(amount);
                modal.find('.message').html('@lang("Please enter an amount to bid")');
                $('.empty-message').show();
            }
            else if(amount <= roundedMinimumBid ){
                //console.log('Amount is less than or equal to minimumbid');
                $('#amount').val('');
                $("#Minimum_Amount").show();
                $("#Bid_Amount").hide();
                $("#Ready_togo").hide();
            }
            else if(amount <= roundedHighestBid ){
                //console.log('Amount is less than or equal to highestbid');
                $('#amount').val('');
                $("#Bid_Amount").show();
                 $("#Minimum_Amount").hide();
                    $("#Ready_togo").hide();
            }
            else{
            modal.find('.message').html('@lang("Are you sure to bid on this product?")');
            console.log('Amount is greater than both minimumbid and highestbid');
                $('.empty-message').hide();
                modal.find('.amount').val(amount);
                $("#Ready_togo").show();
                modal.modal('show');
            }
        });
 });
    // })(jQuery);
</script>
<script>
    function lightbox(idx) {
        console.log(idx);
        var ninjaSldr = document.getElementById("ninja-slider");
        ninjaSldr.parentNode.style.display = "block";

        var i = nslider.init(idx);
console.log(i);
        var fsBtn = document.getElementById("fsBtn");
        fsBtn.click();
    }

    function fsIconClick(isFullscreen, ninjaSldr) { //fsIconClick is the default event handler of the fullscreen button
        if (isFullscreen) {
            ninjaSldr.parentNode.style.display = "none";
        }
    }
</script>
@endpush


