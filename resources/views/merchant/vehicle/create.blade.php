@extends('merchant.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('vehicle.store') }}" method="POST" enctype="multipart/form-data">
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
                                                <label class="w-100 font-weight-bold">@lang('Plate Form') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Plate Form')" name="Plateform" value="" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Notes') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Notes')" name="Notes" value="" required/>
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
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Schedule') <span class="text-danger">*</span></label>
                                                <select name="schedule" class="form-control" required>
                                                    <option value="1">@lang('Yes')</option>
                                                    <option value="0">@lang('No')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Year') <span class="text-danger">*</span></label>
                                                <select name="Year" class="form-control" required id="select_box">
                                                    <option>Select Year</option>
                                                    @foreach ($distinctYears as $distinctYear)
                                                    <option value="{{$distinctYear->Year}}">{{$distinctYear['Year']}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Model') <span class="text-danger">*</span></label>
                                                <select name="Model" class="form-control" required>
                                                    <option>Select Model</option>
                                                    @foreach ($distinctModels as $distinctModel)
                                                    <option value="{{$distinctModel->Model}}">{{$distinctModel->Model}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Class') <span class="text-danger">*</span></label>
                                                <select name="Class" class="form-control" required>
                                                    <option >Class</option>
                                                    @foreach ($distinctClass as $distinctClasses)
                                                    <option value="{{$distinctClasses->Class}}">{{$distinctClasses->Class}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Make') <span class="text-danger">*</span></label>
                                                <select name="Make" class="form-control" required>
                                                    <option >Select Make</option>
                                                    @foreach ($distinctMake as $distinctMakes)
                                                    <option value="{{$distinctMakes->Make}}">{{$distinctMakes->Make}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15 started_at">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Bid_Started_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="started_at" placeholder="@lang('Select Date & Time')" id="startDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ old('date_time') }}" autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Bid_Expired_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="expired_at" placeholder="@lang('Select Date & Time')" id="endDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ old('date_time') }}" autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15 started_at">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Sale_Started_at') </label>
                                                <input type="text" name="sale_started_at" placeholder="@lang('Select Date & Time')" id="salestartDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ old('date_time') }}" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Sale_Expired_at') </label>
                                                <input type="text" name="sale_expired_at" placeholder="@lang('Select Date & Time')" id="saleendDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ old('date_time') }}" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Upload Images') <span class="text-danger">*</span></label>
                                                <input type="file" name="images[]" id="imageInput" multiple class="form-control" accept=".png, .jpg, .jpeg" required>
                                                <div id="imagePreview"></div>

                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group has_append">
                                                <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Longitude Location') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"  id="locationInputs" placeholder="@lang('Current Longitude')" name="longitude" value="" required/>
                                                <!-- Button trigger modal -->
                                                <button type="button"  class="btn btn--primary" data-toggle="modal" data-target="#exampleModal">
                                                Location
                                                </button>
                                            </div>
                                            </div>
                                        </div>
                                    <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                        <div class="input-group has_append">
                                            <div class="form-group">
                                            <label class="w-100 font-weight-bold">@lang('Latitude Location') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"  id="locationInput" placeholder="@lang('Current Latitude')" name="latitude" value="" required/>
                                        </div>
                                        </div>
                                    </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Short Description') <span class="text-danger">*</span></label>
                                                <textarea rows="4" class="form-control border-radius-5" name="short_description">{{ old('short_description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">@lang('Long Description') <span class="text-danger">*</span></label>
                                <textarea rows="8" class="form-control border-radius-5 nicEdit" name="long_description">{{ old('long_description') }}</textarea>
                            </div>

                            <div class="payment-method-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary  text-white">@lang('Specification')
                                                <button type="button" class="btn btn-sm btn-outline-light float-right addUserData"><i class="la la-fw la-plus"></i>@lang('Add New')
                                                </button>
                                            </h5>

                                            <div class="card-body">
                                                <div class="row addedField">
                                                </div>
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
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Location</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you want to save the location?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" id="getLocationButton" class="btn btn-primary">Yes</button>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('merchant.vehicle.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush


@push('style')
    <style>
        .payment-method-item .payment-method-header .thumb .avatar-edit{
            bottom: auto;
            top: 175px;
            .card-footer{
                margin: 0;
  position: absolute;
  top: 50%;
  center: 50%;
            }
        }
    </style>
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
        var imagesContainer = $('<div class="row">'); // Use a Bootstrap row

        // Loop through each selected file
        for (var i = 0; i < files.length; i++) {
            (function () {
                var file = files[i];
                var reader = new FileReader();

                // Create a container div for each image
                var imageContainer = $('<div class="col-md-3 mb-3">'); // Use Bootstrap columns for a 4-column layout

                // Create an image element for each image
                var img = $('<img>');

                reader.onload = function (e) {
                    // Set the image source to the data URL
                    img.attr('src', e.target.result);
                    // Set the CSS style for the thumbnail size
                    img.css({ width: '100%', height: 'auto' });

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
        imagePreview.html(imagesContainer);
    });

    // Handle removal of images when clicking the cross icon
    $('#imagePreview').on('click', '.remove-icon', function () {
        $(this).closest('.col-md-3').remove(); // Remove the clicked image container
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

                start.setHours(0);
                start.setMinutes(0);

                if ([6, 0].indexOf(start.getDay()) != -1) {
                    start.setHours(0);
                    startHours = 0
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
            $('#salestartDateTime').datepicker({
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
            $('#saleendDateTime').datepicker({
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
