@extends($activeTemplate.'layouts.frontend')
@section('content')
<style>
    /* Add custom CSS to style the row borders */
    .border-row td {
        border-top: 1px solid #ddd; /* Add a top border to each cell in the row */
        border-bottom: 1px solid #ddd; /* Add a bottom border to each cell in the row */
    }
    #product-image{
      height: 40px;
      height: 60px;
    }

</style>

<section class="vendor-profile pb-60">
    <div class="container">
       <div>
        <table class="table">
            <thead>
                <th>Product</th>
                <th></th>
                <th>Total Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr class="border-row">
                    <td>
                        <img id="product-image" src="{{ getImage(imagePath()['product']['path'].'/'.$order->product->image, imagePath()['product']['size']) }}" alt="" class="img-fluid">
                    </td>
                    <td>
                        <span>{{$order->product->productCate->name}}</span><br>

                          @if ($order->type != null && $order->type !== "")
                          <span>Size: {{$order->type}}</span><br>
                          @endif
                    </td>

                    <td class="new-price_{{$order->order_id}}">{{$general->cur_sym . $order->total_price}}</td>
                    <td>
                        <input onchange="quantityChanging({{ $order->order_id }})" style="width: 50px" type="number" id="quantity_{{ $order->order_id }}" value="{{ $order->quantity }}">
                    </td>
                    <td>
                        <button type="button" class="btn btn--danger btn-block removeOrder" data-order-id="{{ $order->order_id }}">@lang('Remove')</button>
                    </td>
                </tr>
            @endforeach



            </tbody>
        </table>

        </table>
       </div>
     <div class="row">
           <div class="col-md-4">
            <label class="font-weight-bold">@lang('Add a note to your order')</label>
            <textarea rows="2" class="form-control border-radius-5 nicEdit" name="note">{{ old('long_description') }}</textarea>
           </div>
           @php
               $totalprice = 0;
           @endphp
           @foreach ($orders as $order)
           @php
               $totalprice +=$order->total_price;
           @endphp
           @endforeach
           <div class="col-md-8 text-center">
              <div class="row">
                    <div class="col-md-4 offset-6">
                   <strong>Subtotal </strong>
                    </div>
                    <div class="col-md-2">
                        {{$general->cur_sym . $totalprice}}
                    </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-7 offset-5">
               <span> Shipping & taxes calculated at checkout</span>
                </div>
                <div class="row mt-2 offset-4">
                    <div class="col-md-4">
                        <a type="submit" href="{{route('merchants')}}" class="btn btn--danger btn-block " >@lang('CONTINUE SHOPPING')</a>

                    </div>

                    <div class="col-md-2">
                        <a type="button" href="{{route('payment.page')}}" class="btn btn--danger btn-block " >@lang('CHECKOUT')</a>

                    </div>
                </div>
            </div>
           </div>
     </div>

    </div>
</section>

@endsection
@push('script')

    <script>
        function quantityChanging(id) {
            var quantity = $('#quantity_' + id).val();
            console.log(quantity);

            $.ajax({
                url: "{{ route('quantity.change') }}/" + id, // Replace with your actual route
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: quantity
                },
                success: function(response) {
                    var newprice = response.new_total_price;
                    $('.new-price_'+id).text('$' + newprice); // Update all prices (you may need more precise targeting)
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error('Error removing order: ' + error);
                }
            });
        }

        $(document).ready(function() {
        $('.removeOrder').on('click', function() {
            // Get the order ID from the data attribute
            var orderId = $(this).data('order-id');




            $.ajax({

                url: "{{route('remove.order')}}/" + orderId, // Replace with your actual route
                type: 'GET',

                success: function(response) {
                    window.location.href = '';
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error('Error removing order: ' + error);
                }
            });
        });
    });
    </script>
@endpush

