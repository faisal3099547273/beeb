@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.model.update', $csvs->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-header">


                                <div class="content">
                                    <div class="row mb-none-15">

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



                                    </div>
                                </div>
                            </div>



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
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('admin.model.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush


@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>

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
