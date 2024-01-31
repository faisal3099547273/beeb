
@extends($activeTemplate.'layouts.frontend')
@section('content')
<style>
    .gallery img{
        width:130px;
        cursor:pointer;
    }
</style>
<section class="vendor-profile pb-60">
    <div class="contianer">

            <div class="row">

                <div class="col-md-4 offset-2">
                    <div>
                        <input type="hidden" name="" id="singleimage" value="{{$product->images}}">
                        <img src="{{ getImage(imagePath()['product']['path'].'/'.$product->image, imagePath()['product']['size']) }}" alt="" class="img-fluid mainImage" id="mainImage">
                    </div>
                        <div class="row">
                        @foreach ($images as $image)
                        <div class="col-sm-3 mt-5 gallery">
                    <!--<div class="">-->
                        <img class="ns-img img-fluid thumbnail" src="{{ asset($image->image) }}">
                        

                    <!--</div>-->
                    </div>
                        @endforeach
                        <div class="col-sm-3 mt-5 gallery">
                    <!--<div class="">-->
                        <img class="ns-img img-fluid thumbnail" src="{{ getImage(imagePath()['product']['path'].'/'.$product->image) }}">
                    <!--</div>-->
                    </div>
                    </div>

                </div>

                <div class="col-md-6">
                <div>
                        <h4 style="margin: 0 0 17.5px; font-family: Open Sans,HelveticaNeue,Helvetica Neue,sans-serif; font-weight: 400; line-height: 1.2; overflow-wrap: break-word; word-wrap: break-word;color:black"  id="cate">{{$product->name }}</h3>
                        <p style="margin: 0 0 17.5px; font-family: Open Sans,HelveticaNeue,Helvetica Neue,sans-serif; font-weight: 400; line-height: 1.2; overflow-wrap: break-word; word-wrap: break-word;color:black"  id="cate">{{$product->short_description }}</p>
                        <!--<h3 style="margin: 0 0 17.5px; font-family: Open Sans,HelveticaNeue,Helvetica Neue,sans-serif; font-weight: 400; line-height: 1.2; overflow-wrap: break-word; word-wrap: break-word;color:black"  id="cate">{{$product->name ." " .$product->productCate->name}}</h3>-->
                        <h5 id = "price" style="margin: 0 0 17.5px; font-family: Open Sans, HelveticaNeue, Helvetica Neue, sans-serif; font-weight: 400; line-height: 1.2; overflow-wrap: break-word; word-wrap: break-word; color: black">
                            {{ $general->cur_sym }}<span id="displayedPrice">{{ showAmount($product->price) }}</span>
                        </h5>
                </div>
                <div class="row">
                        @if ($product->variation->isNotEmpty())
                            <div class="col-md-6">
                                <label for="SortTags" class="font-weight-bold m-0">Size</label>
                                <select name="product" class="form-control" id="productSelect" onchange="price(this.value)">
                                    <option>Select</option>
                                    @foreach ($product->variation as $variation)
                                    <option value="{{$variation->id}}" >{{$variation->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                        @else
                        <div class="col-md-2">
                            <label for="SortTags" class="font-weight-bold m-0 quantity">Quantity</label>
                            <input type="number" name="quantity" onchange="quantity(this.value, '{{$product->id}}')" id="{{$product->id}}" class="form-control" value="1"/>
                        </div>
                        @endif
                        @if ($product->variation->isNotEmpty())
                            <div class="col-md-2">
                                    <label for="SortTags" class="font-weight-bold m-0 quantity">Quantity</label>
                                    <input type="number" name="quantity" onchange="quantity(this.value, '{{$product->id}}')" id="{{$product->id}}" class="form-control" value="1"/>
                                </div>
                        @endif
                </div>
                <form id="addToCartForm" action="{{ route('show.card') }}" method="POST">
                    @csrf
                    <input type="hidden" name="data" id="formData" value="">
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <button type="button" id="addToCartButton" style="width: 100%" class="btn btn--danger btn-block">@lang('Add To Cart')</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>

    </div>
</section>
@stop
<style>
   h1{

   }
</style>
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var mainImage = document.getElementById('mainImage');
        var thumbnails = document.querySelectorAll('.thumbnail');

        thumbnails.forEach(function(thumbnail) {
            thumbnail.addEventListener('click', function() {
                // Get the source URL of the clicked thumbnail
                var thumbnailImageURL = this.src;

                // Set the main image's src attribute to the clicked thumbnail's source URL
                mainImage.src = thumbnailImageURL;
            });
        });
    });

    function price(id) {
        $('#{{$product->id}}').val(1);

        var product_id = id;
        var quantity = 1; // Default quantity is 1

        // Check if the #quantity element exists and get its value if it does
        if ($('#quantity').length) {
            quantity = parseFloat($('#quantity').val());
        }

        $.ajax({
            url: "{{ route('product.price')}}/" + product_id,
            type: 'GET',
            datatype: "json",
            success: function (response) {
                if (response.success) {
                    var price = parseFloat(response.variation.price);
                    var total_price = price * quantity;
                    var formattedPrice = '$' + total_price.toFixed(2);
                    $('#price').text(formattedPrice);
                }
            },
            error: function () {
            }
        });
    }

    $(document).ready(function() {
        var initialQuantity = parseFloat($('#{{$product->id}}').val()); // Get the initial quantity
        quantity(initialQuantity, '{{$product->id}}');
    });

    function quantity(quantity, inputId) {
        console.log(quantity);
        var price = parseFloat("{{ $product->price }}"); // Parse the price from the PHP variable
        var total_price = price * parseFloat(quantity);

        // Format the total price and update the displayed price
        var formattedPrice = '{{ $general->cur_sym }}' + total_price.toFixed(2);
        $('#price').text(formattedPrice);
    }
    $(document).ready(function() {
        // Add a click event handler to the "Add to Cart" button
        $("#addToCartButton").click(function() {
            // Get the product data you want to send (e.g., product ID, quantity, etc.)
            var productId = "{{$product->id}}"; // Replace with your product ID
            var quantity = parseInt($("#{{$product->id}}").val());
            var price = parseFloat($("#price").text().replace(/[^0-9.]/g, ''));
            var size = $('#productSelect').val();
            var cate = $('#cate').text();
            var data = {
                productId: productId,
                quantity: quantity,
                price: price,
                size: size,
                cate: cate,
                _token: "{{ csrf_token() }}" // Add CSRF token for security
            };
            // Set the JSON-encoded data in the hidden input field
            $("#formData").val(JSON.stringify(data));

            // Submit the form with a POST request
            $("#addToCartForm").submit();
        });
    });
</script>

@endpush
