@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-header">
                                <div class="form-group">
                                    <label class="font-weight-bold">@lang('Image') <span class="text-danger">*</span></label>
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url('{{getImage(imagePath()['product']['path'].'/'.$product->image,imagePath()['product']['size'])}}')"></div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                            <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="content">
                                    <div class="row mb-none-15">
                                        <div class="col-sm-12 col-xl-4 col-lg-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Name')" name="name" value="{{ $product->name }}" required/>
                                            </div>
                                        </div>


                                        <div class="col-sm-12 col-xl-4 col-lg-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Category') <span class="text-danger">*</span></label>
                                                <select name="category" class="form-control" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $product->productcate_id == $category->id ? 'Selected':'' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6">
                                            <label class="font-weight-bold">@lang('Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="price" value="{{ getAmount($product->price) }}" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Quantity') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="quantity" value="{{ $product->quantity }}" required/>

                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Short Description')</label>
                                                <textarea rows="4" class="form-control border-radius-5" name="short_description">{{ $product->short_description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="image-list">
                                            @foreach ($images as $image)
                                                @if ($image->product_id == $product->id)
                                                    <div class="image-item">
                                                        <img src="{{ asset($image->image) }}" alt="{{ $image->alt_text }}">
                                                        <span class="remove-image" data-image-id="{{ $image->id }}">X</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <!-- Input field for adding new images -->
                                        <div class="image-item">
                                            <input type="file" name="new_images[]" class="profilePicUpload" accept=".png, .jpg, .jpeg" multiple>
                                            <input type="hidden" name="existing_image_ids" value="{{ implode(',', $existingImageIds) }}">
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('admin.product.all') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush
<style>
    .image-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px; /* Adjust the gap between images */
    }

    .image-item {
        flex: 1;
        max-width: calc(33.33% - 10px); /* Adjust the width based on the number of images per row */
    }
    .image-item {
    position: relative;
    display: inline-block;
}

.remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    cursor: pointer;
}

</style>

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
$(document).ready(function () {
    // Array to store new image IDs
    let newImageIds = [];

    // Function to update the input field's value
    function updateImageIds() {
        // Get the IDs of existing images
        const existingImageIds = $("[name='existing_image_ids']").val().split(',');

        // Combine existing and new image IDs
        const allImageIds = [...existingImageIds, ...newImageIds];

        $("[name='images[]']").val(allImageIds.join(','));
    }

    // Handle image removal for both existing and newly added images
    $('.image-list').on('click', '.remove-image', function () {
        const imageId = $(this).data('image-id');

        // Check if the removed image is an existing image
        if ($.inArray(imageId.toString(), $("[name='existing_image_ids']").val().split(',')) !== -1) {
            // If it's an existing image, remove it from the hidden field
            $("[name='existing_image_ids']").val(function (i, oldVal) {
                return oldVal.replace(imageId + ',', '').replace(',' + imageId, '').replace(imageId, '');
            });
        } else {
            // If it's a newly added image, remove it from the newImageIds array
            newImageIds = newImageIds.filter(id => id !== imageId.toString());
        }

        // Remove the image item from the DOM
        $(this).parent('.image-item').remove();

        // Update the input field's value
        updateImageIds();
    });

    // Handle new image addition
    $("input[name='new_images[]']").on('change', function () {
        const files = $(this).prop('files');
        if (files.length > 0) {
            // Clear the newImageIds array before adding new image IDs
            newImageIds = [];

            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imgSrc = e.target.result;
                    const imageItem = $('<div class="image-item">' +
                        '<img src="' + imgSrc + '" alt="New Image">' +
                        '<span class="remove-image">X</span>' +
                        '</div>');
                    $('.image-list').append(imageItem);

                    // Get the ID of the newly added image (you may need to fetch it from the server)
                    const newImageId = 'new_image_id_' + i; // Replace with actual ID

                    // Add the ID to the newImageIds array
                    newImageIds.push(newImageId);

                    // Update the input field's value with all image IDs (existing and new)
                    updateImageIds();

                    // Handle removal of newly added image when clicking the cross icon
                    imageItem.find('.remove-image').on('click', function () {
                        imageItem.remove();
                        // Remove the ID from the newImageIds array
                        newImageIds = newImageIds.filter(id => id !== newImageId);
                        // Update the input field's value again after removal
                        updateImageIds();
                    });
                };
                reader.readAsDataURL(files[i]);
            }
        }
    });
});

    $(document).ready(function () {
        $('.image-list').on('click', '.remove-image', function () {

            const id = $(this).data('image-id');
            const imageUrl = $(this).data('image-url');

            // Remove the image item from the DOM
            $(this).parent('.image-item').remove();
            console.log(id);
            // Make an AJAX request to delete the image from the database
            $.ajax({
                url: "{{ route('admin.delete-image')}}"+"/"+id,
                type: 'GET',
                datatype: "json",
                success: function (response) {
                    if (response.success) {
                        console.log('ok');
                    } else {
                        // Image not found or deletion failed
                    }
                },
                error: function () {
                    // Handle AJAX error
                }
            });
        });
    });





        (function ($) {
            "use strict";

            var specCount = `{{ $product->specification ? count($product->specification) : 0 }}`;
            specCount = parseInt(specCount);
            specCount = specCount ? specCount + 1 : 1;

            // Create start date
            var start = new Date(),
                    prevDay,
                    startHours = 0;

                // 09:00 AM
                start.setHours(0);
                start.setMinutes(0);

                // If today is Saturday or Sunday set 10:00 AM
                if ([6, 0].indexOf(start.getDay()) != -1) {
                    start.setHours(10);
                    startHours = 10
                }
            // date and time picker
            $('#startDateTime').datepicker({
                timepicker: true,
                language: 'en',
                dateFormat: 'dd-mm-yyyy',
                startDate: start,
                minHours: startHours,
                maxHours: 23,
                onSelect: function (fd, d, picker) {
                    // Do nothing if selection was cleared
                    if (!d) return;

                    var day = d.getDay();

                    // Trigger only if date is changed
                    if (prevDay != undefined && prevDay == day) return;
                    prevDay = day;

                    // If chosen day is Saturday or Sunday when set
                    // hour value for weekends, else restore defaults
                    if (day == 6 || day == 0) {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    } else {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    }
                }
            });

            // date and time picker
            $('#endDateTime').datepicker({
                timepicker: true,
                language: 'en',
                dateFormat: 'dd-mm-yyyy',
                startDate: start,
                minHours: startHours,
                maxHours: 23,
                onSelect: function (fd, d, picker) {
                    // Do nothing if selection was cleared
                    if (!d) return;

                    var day = d.getDay();

                    // Trigger only if date is changed
                    if (prevDay != undefined && prevDay == day) return;
                    prevDay = day;

                    // If chosen day is Saturday or Sunday when set
                    // hour value for weekends, else restore defaults
                    if (day == 6 || day == 0) {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    } else {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    }
                }
            });


            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });
            $('.addUserData').on('click', function () {
                var html = `
                    <div class="col-md-12 user-data">
                        <div class="form-group">
                            <div class="input-group mb-md-0 mb-4">
                                <div class="col-md-4">
                                    <input name="specification[${specCount}][name]" class="form-control" type="text" required placeholder="@lang('Field Name')">
                                </div>
                                <div class="col-md-6">
                                    <input name="specification[${specCount}][value]" class="form-control" type="text" required placeholder="@lang('Field Value')">
                                </div>
                                <div class="col-md-2 mt-md-0 mt-2 text-right">
                                    <span class="input-group-btn">
                                        <button class="btn btn--danger btn-lg removeBtn w-100" type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('.addedField').append(html);
                specCount += 1;
            });

            $(document).on('click', '.removeBtn', function () {
                $(this).closest('.user-data').remove();
            });

            @if(old('currency'))
                $('input[name=currency]').trigger('input');
            @endif

            $("[name=schedule]").on('change', function(e){
                var schedule = e.target.value;

                if(schedule != 1){
                    $("[name=started_at]").attr('disabled', true);
                    $('.started_at').css('display', 'none');
                }else{
                    $("[name=started_at]").attr('disabled', false);
                    $('.started_at').css('display', 'block');
                }
            }).change();

        })(jQuery);
    </script>
@endpush
