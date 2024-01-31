@extends($activeTemplate.'layouts.frontend')

@section('content')
<!-- Product -->
<section class="product-section pt-120 pb-120">
    <div class="container">
        <div class="mb-4 d-lg-none">
            <div class="filter-btn ms-auto">
                <i class="las la-filter"></i>
            </div>
        </div>
        <div class="row flex-wrap-reverse">
            <div class="col-lg-4 col-xl-3">
                <aside class="search-filter">
                    <div class="bg--section pb-5 pb-lg-0">
                        <div class="filter-widget pt-3 pb-2">
                            <h4 class="title m-0"><i class="las la-random"></i>@lang('Filters')</h4>
                            <span class="close-filter-bar d-lg-none">
                                <i class="las la-times"></i>
                            </span>

                        </div>
                        <div class="filter-widget">

                            <form action="{{ route('product.search') }}" class="search-form">
                                <div class="input-group input--group">
                                    <input type="text" class="form-control border border-dark" name="search_key" style="color: #111; background-color:#fff" id="search_key" value="{{request('search_key') }}" placeholder="@lang('Vehicle Name')">
                                    <button type="submit" class="cmn--btn"><i class="las la-search"></i></button>
                                </div>
                            </form>
                        </div>

                     {{--   <div class="filter-widget">
                            <h6 class="sub-title">@lang('Sort by')</h6>
                            <form>
                                <div class="form-check form--check">
                                    <input class="form-check-input sorting" value="created_at" type="radio" name="radio" id="radio1">
                                    <label  for="radio1">@lang('Date')</label>
                                </div>
                                <div class="form-check form--check">
                                    <input class="form-check-input sorting" value="price" type="radio" name="radio" id="radio2">
                                    <label  for="radio2">@lang('Price')</label>
                                </div>
                                <div class="form-check form--check">
                                    <input class="form-check-input sorting" value="name" type="radio" name="radio" id="raqdio3">
                                    <label  for="raqdio3">@lang('Name')</label>
                                </div>
                                <div class="form-check form--check">
                                    <input class="form-check-input sorting" value="individual" type="radio" name="radio" id="raqdio4">
                                    <label  for="raqdio4">@lang('Individual')</label>
                                </div>
                                <div class="form-check form--check">
                                    <input class="form-check-input sorting" value="dealer" type="radio" name="radio" id="raqdio5">
                                    <label  for="raqdio5">@lang('Dealer')</label>
                                </div>
                            </form>
                        </div>--}}

                        {{-- <div class="filter-widget">
                            <h6 class="sub-title">@lang('By Price')</h6>

                            <div class="widget">
                                <div id="slider-range"></div>
                                <div class="price-range">
                                    <label for="amount">@lang('Price') :</label>
                                    <input type="text" id="amount" readonly>
                                    <input type="hidden" name="min_price" >
                                    <input type="hidden" name="max_price">
                                </div>
                            </div>
                        </div> --}}

                        <div class="filter-widget">
                            <h6 class="sub-title">@lang('By Vehicle')</h6>
                            <form>
                                <div class="form-group row">
                                    <label for="year" class="col-sm-3 col-form-label" style="color: black;">Year: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="yearSelect" name="year" style="background-color: white; color:black">
                                            <option value="">Select Year</option>
                                            @foreach ($distinctYears as $distinctYear)
                                                <option value="{{$distinctYear->Year}}" style="background-color: white; color:black">{{$distinctYear['Year']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="make" class="col-sm-3 col-form-label" style="color: black;">Make: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="makeSelect" name="make" style="background-color: white; color:black">
                                            <option value="">Select Make</option>
                                            @foreach ($distinctMake as $distinctMake)
                                                <option value="{{$distinctMake->Make}}" style="background-color: white; color:black">{{$distinctMake->Make}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="model" class="col-sm-3 col-form-label" style="color: black;">Model: </label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="modelSelect" name="model" style="background-color: white; color:black">
                                            <option value="">Select Model</option>
                                            @foreach ($distinctModels as $distinctModel)
                                                <option value="{{$distinctModel->Model}}" style="background-color: white; color:black">{{$distinctModel['Model']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </aside>
                <div class="mini-banner-area mt-4">
                    <div class="mini-banner">
                        @php
                            showAd('370x670');
                        @endphp
                    </div>
                    <div class="mini-banner">
                        @php
                            showAd('300x250');
                        @endphp
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xl-9 search-result">
                @include($activeTemplate.'product.filtered', ['products'=> $products])
            </div>
        </div>
    </div>
</section>
<!-- Product -->

@endsection

@push('style')
    <style>
        .ui-datepicker .ui-datepicker-prev,
        .ui-datepicker .ui-datepicker-next {
            color: #111;
            background-color: #fff;
            z-index: 11;
        }
        #empty_message {
            color: black;
        }
        .ui-datepicker-prev {
            position: relative;
        }

        .ui-datepicker-prev::before {
            position: absolute;
            content: "\f104";
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: "Line Awesome Free";
            font-weight: 900;
        }
        .ui-datepicker-next::before {
            position: absolute;
            content: "\f105";
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: "Line Awesome Free";
            font-weight: 900;
        }

        .price-range {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
        }
        .price-range label {
            margin: 0;
            font-weight: 500;
            color: #171d1c;
        }
        .price-range input {
            height: unset;
            width: unset;
            background: transparent;
            border: none;
            text-align: right;
            font-weight: 500;
            color: #c151cc;
            padding-right: 0;
        }

        .ui-slider-range {
            height: 3px;
            background: $base-color;
            position: relative;
            z-index: 1;
        }

        .widget .ui-state-default {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: block;
            border: none;
            border-radius: 50%;
            background-color: $base-color !important;
            box-shadow: 0 9px 20px 0 rgba(22, 26, 57, 0.36);
            outline: none;
            cursor: pointer;
            top: -9px;
            position: absolute;
            z-index: 1;
        }
        .widget .ui-state-default::after {
            position: absolute;
            content: "";
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: $base-color;
            top: 3px;
            left: 3px;
            display: block;
        }
        .widget .ui-widget.ui-widget-content {
            position: relative;
            height: 3px;
            border: none;
            margin-right: 20px;
            margin-bottom: 25px;
        }
        .widget .ui-widget.ui-widget-content::after {
            position: absolute;
            content: "";
            top: 0;
            left: 0;
            height: 3px;
            background: rgba($base-color, 0.3);
            width: calc(100% + 20px);
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/jquery-ui.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue.'js/jquery-ui.min.js') }}"></script>
@endpush

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
$(document).ready(function() {
    $('#yearSelect').on('change', function() {
        var selectedYear = $(this).val();
        var name_search = $('#search_key').val();
        filterProducts(selectedYear, name_search);
    });

    $('#modelSelect').on('change', function() {
        var selectedModel = $(this).val();
        var name_search = $('#search_key').val();
        filterProducts(selectedModel, name_search);
    });

    $('#makeSelect').on('change', function() {
        var selectedMake = $(this).val();
        var name_search = $('#search_key').val();

        filterProducts(selectedMake, name_search);
    });

   function extractDateComponents(dateString) {
        // Parse the date string into a JavaScript Date object
        const date = new Date(dateString);

        // Extract the date components
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is zero-based
        const day = date.getDate().toString().padStart(2, '0');
        const hour = date.getHours().toString().padStart(2, '0');
        const minute = date.getMinutes().toString().padStart(2, '0');
        const second = date.getSeconds().toString().padStart(2, '0');

        return {
            year,
            month,
            day,
            hour,
            minute,
            second
        };
    }
    function showAmount(amount, decimal = 2, separate = true, exceptZeros = false) {
        const separator = separate ? ',' : '';
        let printAmount = parseFloat(amount).toFixed(decimal).toString();

        if (exceptZeros) {
            const parts = printAmount.split('.');
            if (parseInt(parts[1]) === 0) {
                printAmount = parts[0];
            }
        }

        return printAmount;
    }

    function filterProducts(selectedValue, name_searching) {


        // Send an AJAX request
        $.ajax({
            url: "{{ route('product.search.filter') }}"+'/'+selectedValue+'/'+name_searching, // Laravel route URL
            method: 'get', // HTTP method (can be 'GET' or 'POST')
            success: function(response) {
                var products = response.products.data;
                $('#search-result').empty();
                commponent(products);
        },

            error: function(xhr, status, error) {
                // Handle any errors that occur during the request
                console.error(error);
            }
        });
    }

    function commponent(products){
            var view = ''; // Initialize an empty view
             $('.search-result').empty();
             $('#search-result').empty();
            $('#count-message').empty();
            $('#result-message').empty();

            if (Array.isArray(products)) {
                var imagePath = 'assets/images/product';

                products.forEach(function(product) {
                    var imageName = product.image;
                    var imageUrl = '/' + imagePath + '/' + imageName;
                    var formattedDateTime = extractDateComponents(product.expired_at);
                    view += '<div class="row g-4" id = "vehicle-category">';
                    view += '<div class="col-sm-6 col-xl-4">';
                    view += '<div class="auction__item bg--body">';
                    view += '<div class="auction__item-thumb">';
                    view += '<a href="' + '/product-details/' + product.id + '/' +product.name + '">';
                    view += '<img src="' + imageUrl + '" alt="auction">';
                    // view += '<img src="' + getImage(imagePath().product.path + '/thumb_' + product.image, imagePath().product.thumb) + '" alt="auction">';
                    view += '</a>';
                    view += '<span class="total-bids">';
                    view += '<span>' + product.total_bid + ' Bids</span>';
                    view += '</span>';
                    view += '</div>';
                    view += '<div class="auction__item-content">';
                    view += '<h6 class="auction__item-title">';
                    view += '<a href="' + '/product-details/' + product.id + '/' +product.name + '">'+product.name+'</a>';
                    view += '</h6>';
                    view += '<div class="auction__item-countdown">';
                    view += '<div class="inner__grp">';
                    view += '<ul class="countdown" data-date="' + formattedDateTime + '">';
                    view += '<li><span class="days">'+formattedDateTime.day+'d</span></li>';
                    view += '<li><span class="hours">'+formattedDateTime.hour+'h</span></li>';
                    view += '<li><span class="minutes">'+formattedDateTime.minute+'m</span></li>';
                    view += '<li><span class="seconds">'+formattedDateTime.second+'s</span></li>';
                    view += '</ul>';
                    view += '<div class="total-price" style="color:green">$'+ showAmount(product.price) + '</div>';
                    view += '</div>';
                    view += '</div>';
                    view += '<div class="auction__item-footer">';
                    view += '<a href="' + '/product-details/' + product.id + '/' +product.name + '" class="cmn--btn w-100">Details</a>';
                    view += '</div>';
                    view += '</div>';
                    view += '</div>';
                    view += '</div>';
                });
            }
            else {
                console.error("Products is not an array.");
            }
            // var total = products.total
            var count = products.length;
            // Check if products exist and update the HTML accordingly
            if (products.length > 0) {
                $('#count-message').text(count);
                $('#result-message').text(count);

                $(".search-result").html(view);
                $("#empty_message").text(""); // Clear the empty message
            } else {
                $('#count-message').text(count);
                $('#result-message').text(count);
                // Show the error message and hide the product container
                $(".search-result").html("");
                $("#error-message").html('<div class="text-center"><h6>No vehicle found</h6></div>');
                $("#error-message").show(); // Show the error message div
            }
    }
    (function ($) {
        "use strict";
        var page = 1;
        var search_key = @json(request()->search_key);
        var sorting = '';
        var categories = [];
        var minPrice = parseInt(`{{ $allProducts->min('price') }}`);
        var maxPrice = parseInt(`{{ $allProducts->max('price') }}`);

        $(document).on('click', '.page-link', function(e){
          e.preventDefault();
          page = $(this).attr('href').match(/page=([0-9]+)/)[1];;
          loadSearch();
        });

        $('.sorting').on('click', function(e){
            sorting = e.target.value;
            loadSearch();
        });

        $( "#slider-range" ).slider({
            range: true,
            min: minPrice,
            max: maxPrice,
            values: [minPrice, maxPrice],
            slide: function (event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
                $('input[name=min_price]').val(ui.values[0]);
                $('input[name=max_price]').val(ui.values[1]);
            },

            change: function () {
                minPrice = $('input[name="min_price"]').val();
                maxPrice = $('input[name="max_price"]').val();

                $('.brand-filter input:checked').each(function () {
                    brand.push(parseInt($(this).attr('value')));
                });

                loadSearch();
            }
        });
        $("#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ));

        $('.category-check').click(function(e){
            categories = [];
            var categoryArr = $('.category-check:checked:checked');
                if(e.target.value == 'All'){
                    $('input:checkbox').not(this).prop('checked', false);
                    categories = [];
                    loadSearch();
                    return 0;
                }else{
                    $('#cate-00').prop('checked', false);
                }

            $.each(categoryArr, function (indexInArray, valueOfElement) {
                categories.push(valueOfElement.value);
            });

            loadSearch();
        });


        function loadSearch(){
            $("#overlay, #overlay2").fadeIn(300);

            var url = `{{ route('product.search.filter') }}`;
            var data = {'sorting': sorting, 'minPrice': minPrice, 'maxPrice': maxPrice, 'search_key':search_key, 'categories': categories, 'page': page }

            $.ajax({
                type: "GET",
                url: url,
                data: data,
                success: function (response) {
                var products = response.products;
                // commponent(products);
                var view = ''; // Initialize an empty view
             $('#vehicle-category').empty();
            $('#count-message').empty();
            $('#result-message').empty();
            if (Array.isArray(products)) {

                var imagePath = 'assets/images/product';

                products.forEach(function(product) {

                    var imageName = product.image;
                    var imageUrl = '/' + imagePath + '/' + imageName;
                    var formattedDateTime = extractDateComponents(product.expired_at);
                    view += '<div class="col-sm-6 col-xl-4">';
                    view += '<div class="auction__item bg--body">';
                    view += '<div class="auction__item-thumb">';
                    view += '<a href="' + '/product-details/' + product.id + '/' +product.name + '">';
                    view += '<img src="' + imageUrl + '" alt="auction">';
                    // view += '<img src="' + getImage(imagePath().product.path + '/thumb_' + product.image, imagePath().product.thumb) + '" alt="auction">';
                    view += '</a>';
                    view += '<span class="total-bids">';
                    view += '<span>' + product.total_bid + ' Bids</span>';
                    view += '</span>';
                    view += '</div>';
                    view += '<div class="auction__item-content">';
                    view += '<h6 class="auction__item-title">';
                    view += '<a href="' + '/product-details/' + product.id + '/' +product.name + '">'+product.name+'</a>';
                    view += '</h6>';
                    view += '<div class="auction__item-countdown">';
                    view += '<div class="inner__grp">';
                    view += '<ul class="countdown" data-date="' + formattedDateTime + '">';
                    view += '<li><span class="days">'+formattedDateTime.day+'d</span></li>';
                    view += '<li><span class="hours">'+formattedDateTime.hour+'h</span></li>';
                    view += '<li><span class="minutes">'+formattedDateTime.minute+'m</span></li>';
                    view += '<li><span class="seconds">'+formattedDateTime.second+'s</span></li>';
                    view += '</ul>';
                    view += '<div class="total-price" style="color:green">$'+ showAmount(product.price) + '</div>';
                    view += '</div>';
                    view += '</div>';
                    view += '<div class="auction__item-footer">';
                    view += '<a href="' + '/product-details/' + product.id + '/' +product.name + '" class="cmn--btn w-100">Details</a>';
                    view += '</div>';
                    view += '</div>';
                    view += '</div>';
                    view += '</div>';
                });
            }
            else {
                console.error("Products is not an array.");
            }
            // var total = products.total
            var count = products.length;
            // Check if products exist and update the HTML accordingly
            if (products.length > 0) {

                $('#count-message').text(count);
                $('#result-message').text(count);

                // $("#vehicle-category").html(view);
                $("#empty_message").text(""); // Clear the empty message
            } else {
                $('#count-message').text(count);
                $('#result-message').text(count);
                // Show the error message and hide the product container
                $("#vehicle-category").html("");
                $("#error-message").html('<div class="text-center"><h6>No vehicle found</h6></div>');
                $("#error-message").show(); // Show the error message div
            }
                $('.search-result').html(view);
                $("#overlay, #overlay2").fadeOut(300);
                // runCountDown();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("Status: " + textStatus); alert("Error: " + errorThrown);
                }

            });
        }

        function runCountDown() {
            $('.countdown').each(function(){
            var date = $(this).data('date');
              $(this).countdown({
                date: date,
                offset: +6,
                day: 'Day',
                days: 'Days'
              });
           });
        }

      })(jQuery);


});


  </script>
  @endpush
