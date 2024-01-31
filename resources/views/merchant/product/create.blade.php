@extends('merchant.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{route('merchant.product.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-header">
                                <div class="form-group">
                                    <label class="font-weight-bold">@lang('Image') <span class="text-danger">*</span></label>
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url('{{getImage(imagePath()['product']['path'],imagePath()['product']['size'])}}')"></div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                            <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="content">
                                    <div class="row mb-none-15">
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Name')" name="name" value="{{ old('name') }}" required/>
                                            </div>
                                        </div>


                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Category') <span class="text-danger">*</span></label>
                                                <select name="category" class="form-control" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="price" value="{{ old('price') }}" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Quantity') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="quantity" value="{{ old('quantity') }}" required/>

                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-8 col-lg-8 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Upload Images') <span class="text-danger">*</span></label>
                                                <input type="file" name="images[]" id="imageInput" multiple class="form-control" accept=".png, .jpg, .jpeg" required>
                                                <div id="imagePreview" class="d-flex flex-row flex-wrap justify-content-start align-items-center"></div>
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Short Description')</label>
                                                <textarea rows="4" class="form-control border-radius-5" name="short_description">{{ old('short_description') }}</textarea>
                                            </div>
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


{{-- @push('breadcrumb-plugins')
    <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush --}}

@push('style')
    <style>
        .payment-method-item .payment-method-header .thumb .avatar-edit{
            bottom: auto;
            top: 175px;
        }
        /* Custom CSS for image containers */
.image-container {
    display: inline-block;
    margin-right: 10px;
}

/* Style for the remove icon */
.remove-icon {
    cursor: pointer;
    margin-left: 5px;
}


    </style>
@endpush

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
    $(document).ready(function () {
        var removedImages = []; // Array to store removed image values

        // Handle file selection
        $('#imageInput').on('change', function (e) {
            var files = e.target.files; // Get the selected files
            var imagePreview = $('#imagePreview'); // Get the image preview div

            // Create a container div for all images
            var imagesContainer = $('<div>');

            // Loop through each selected file
            for (var i = 0; i < files.length; i++) {
                (function () {
                    var file = files[i];
                    var reader = new FileReader();

                    // Create a container div for each image
                    var imageContainer = $('<div class="image-container">');

                    // Create an image element for each image
                    var img = $('<img>');

                    reader.onload = function (e) {
                        // Set the image source to the data URL
                        img.attr('src', e.target.result);
                        // Set the CSS style for the thumbnail size
                        img.css({ width: '100px', height: 'auto' });

                        // Create a cross icon to remove the image
                        var removeIcon = $('<span class="remove-icon">×</span>');

                        // Attach a click event handler to the remove icon
                        removeIcon.click(function () {
                            var removedSrc = img.attr('src'); // Get the source of the removed image
                            removedImages.push(removedSrc); // Add the removed image source to the array
                            imageContainer.remove(); // Remove the image container
                        });

                        // Check if the image source already exists in the preview
                        var imageSrc = img.attr('src');
                        if (!imagePreview.find('img[src="' + imageSrc + '"]').length) {
                            // If it doesn't exist, append the image and remove icon
                            imageContainer.append(img).append(removeIcon);

                            // Append a hidden input field with image data
                            var hiddenInput = $('<input type="hidden" name="images[]" value="' + e.target.result + '">');
                            imageContainer.append(hiddenInput);

                            // Append the image container to the images container
                            imagesContainer.append(imageContainer);
                        }
                    };

                    // Read the file as a data URL (to display it as an image)
                    reader.readAsDataURL(file);
                })();
            }

            // Append the new images to the image preview div
            imagePreview.append(imagesContainer);
        });

        // Handle removal of images when clicking the cross icon
        $('#imagePreview').on('click', '.remove-icon', function () {
            $(this).closest('.image-container').remove(); // Remove the clicked image container
        });

        // Example of how to access removed image values
        $('#submitForm').on('click', function () {
            console.log('Removed Images:', removedImages);
            // You can now send the removedImages array to the server when submitting the form
        });
    });














         // start //
// Function to get the current location and populate the input field
function getCurrentLocation() {

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Get the input field element
            const locationInput = document.getElementById("locationInput");

            // Populate the input field with the current coordinates
            locationInput.value = `${latitude}`;
            locationInputs.value = `${longitude}`;

        });
        $('#exampleModal').modal('hide');
    } else {
        alert("Geolocation is not available in your browser.");
    }
}
// Add a click event listener to the button
const getLocationButton = document.getElementById("getLocationButton");
getLocationButton.addEventListener("click", getCurrentLocation);
      // end //




        (function ($) {
            "use strict";

            var specCount = 1;
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
