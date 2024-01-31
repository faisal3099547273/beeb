@extends('merchant.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('merchant.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
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
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Name')" name="name" value="{{ $product->name }}" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Plate Form') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Plate Form')" name="Plateform" value="{{ $product->Plateform }}" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Notes') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Notes')" name="Notes" value="{{ $product->Notes }}" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Category') <span class="text-danger">*</span></label>
                                                <select name="category" class="form-control" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'Selected':'' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="price" value="{{ getAmount($product->price) }}" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Schedule') <span class="text-danger">*</span></label>
                                                <select name="schedule" class="form-control" required>
                                                    <option value="1" {{ $product->started_at > now() ? 'Selected' : '' }}>@lang('Yes')</option>
                                                    <option value="0">@lang('No')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Year') <span class="text-danger">*</span></label>
                                                <select name="Year" class="form-control" required id="select_box">
                                                     <option value="">@lang('Select One')</option>
                                                    @foreach ($distinctYears as $distinctYear)

 <option value="{{ $distinctYear->Year}}" {{ $product->Year == $distinctYear->Year ? 'Selected':'' }}>{{ $distinctYear->Year}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

 <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Model') <span class="text-danger">*</span></label>
                                                <select name="Model" class="form-control" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($distinctModels as $distinctModel)
<option value="{{ $distinctModel->Model}}" {{ $product->Model == $distinctModel->Model? 'Selected':'' }}>{{ $distinctModel->Model}}</option>

                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Class') <span class="text-danger">*</span></label>
                                                <select name="Class" class="form-control" required>
                                                    <option value="">@lang('Select Class')</option>
                                                    @foreach ($distinctClass as $distinctClasses)

<option value="{{ $distinctClasses->Class}}" {{ $product->Class == $distinctClasses->Class? 'Selected':'' }}>{{ $distinctClasses->Class}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group">
                                                <label class="w-100 font-weight-bold">@lang('Make') <span class="text-danger">*</span></label>
                                                <select name="Make" class="form-control" required>
                                                    <option value="">@lang('Select Make')</option>
                                                    @foreach ($distinctMake as $distinctMakes)
                                                    <option value="{{ $distinctMakes->Make}}" {{ $product->Make == $distinctMakes->Make? 'Selected':'' }}>{{ $distinctMakes->Make}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15 started_at">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Started_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="started_at" placeholder="@lang('Select Date & Time')" id="startDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ $product->started_at }}" autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Expired_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="expired_at" placeholder="@lang('Select Date & Time')" id="endDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ $product->expired_at }}" autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="input-group has_append">
                                                <div class="form-group">
                                                  <label class="w-100 font-weight-bold">@lang('Longitude Location') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control"  id="locationInputs" placeholder="@lang('Current Longitude')" name="longitude" value="{{ $product->longitude }}" required/>
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
                                                <input type="text" class="form-control"  id="locationInput" placeholder="@lang('Current Latitude')" name="latitude" value="{{ $product->latitude }}" required/>
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
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Short Description') <span class="text-danger">*</span></label>
                                                <textarea rows="4" class="form-control border-radius-5" name="short_description">{{ $product->short_description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">@lang('Long Description') <span class="text-danger">*</span></label>
                                <textarea rows="8" class="form-control border-radius-5 nicEdit" name="long_description">{{ $product->long_description }}</textarea>
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
                                                    @if ($product->specification)
                                                        @foreach ($product->specification as $spec)
                                                            <div class="col-md-12 user-data">
                                                                <div class="form-group">
                                                                    <div class="input-group mb-md-0 mb-4">
                                                                        <div class="col-md-4">
                                                                            <input name="specification[{{ $loop->iteration }}][name]" class="form-control" type="text" value="{{ $spec['name'] }}" required placeholder="@lang('Field Name')">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input name="specification[{{ $loop->iteration }}][value]" class="form-control" type="text" value="{{ $spec['value'] }}" required placeholder="@lang('Field Value')">
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
                                                            </div>
                                                        @endforeach
                                                    @endif
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

@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('merchant.product.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('style')
    <style>
        .payment-method-item .payment-method-header .thumb .avatar-edit{
            bottom: auto;
            top: 175px;
        }
    </style>
@endpush

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>



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
