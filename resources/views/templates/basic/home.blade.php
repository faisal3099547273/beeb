@extends($activeTemplate.'layouts.frontend')
@php
	$banner = getContent('banner.content', true);
    $features = getContent('feature.element');
@endphp
@section('content')


<video style="position: absolute;" autoplay muted loop id="myVideo">
  <source src="{{ asset('assets/video/BeepVintageCar.mp4') }}" type="video/mp4">
  Your browser does not support HTML5 video.
</video>

<section class="banner-section bg--overlay " >
	<div class="banner__inner">
		<div class="container">
			<div class="banner__content">
				<h2 class="banner__title cd-headline letters type">
					<span>{{ __($banner->data_values->heading) }}</span>
				</h2>
				<p class="banner__content-txt">{{ __($banner->data_values->subheading) }}</p>
				<div class="btn__grp">
					<a href="{{ $banner->data_values->button_url }}" class="cmn--btn">{{ __($banner->data_values->button) }}</a>
					<a href="{{ $banner->data_values->link_url }}" class="cmn--btn active">{{ __($banner->data_values->link) }}</a>
				</div>
			</div>
		</div>
	</div>
</section>

@push('script')
    <script type="text/javascript" src="{{ asset('assets/js/slide.js') }}"></script>
@endpush

<!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}" >
<section class="banner-section bg--overlay bg_img" >
    <div class="banner__inner" style="padding: 0px 0 100px;" >
        <div class="container">
            <section id="gallery" >
                <section id="buttons" > <a href='#'class='prev'>&laquo;</a> <a href='#'class='next'>&raquo;</a> </section>
                <ul>
                  <li>
                    <span> This 1981 Ferrari 512 BB is one of 929 carbureted examples produced between 1976 and 1981, and it was imported to the US in July 1981. The car was registered in Oregon through the late 2000s and was acquired by the selling dealer in 2022 before being offered on BaT in May 2023. It has been refinished in silver over black leather and is powered by… </span>

                     <img src='{{ asset('assets/img/1.jpg') }}' /> </li>
                  <li> <span> This 1989 911 Carrera is one of 340 Club Sport models produced between August 1987 and September 1989, of which only 28 were manufactured for the US market. Built in May 1989, the car spent time in Massachusetts, Maine, and Arizona before being exported to Austria in 2012. It is finished in black over Linen pinstripe velour upholstery and is… </span> <img src='{{ asset('assets/img/2.jpg') }}' /> </li>
                  <li> <span> This 2001 Ferrari 550 Maranello is finished in Blu Pozzi over Natural leather and is powered by a 5.5-liter V12 linked to a six-speed manual transaxle and a limited-slip differential. It is equipped with the Fiorano handling package, 19" multi-piece wheels, and Scuderia Ferrari fender shields in addition to power-adjustable Daytona-style seats, a… </span> <img src='{{ asset('assets/img/3.webp') }}' /> </li>
                  <li> <span> This 1962 Alfa Romeo Giulietta Sprint Zagato is one of fewer than 50 “Coda Tronca” examples built and was completed on February 9, 1962, on order for Swiss privateer Armand Schaefer. Chassis 00181 was reportedly delivered to Virgil Conrero when new to receive modifications and be preparation for endurance racing. </span> <img src='{{ asset('assets/img/4.jpg') }}' /> </li>
                  <li> <span> This 1973 Chevrolet K5 Blazer was acquired by the selling dealer in 2021 and subsequently refurbished with a body-off repaint in green and white, a re-trim of the cabin with white vinyl and green plaid upholstery, and the installation of a 6.2-liter LS3 V8, a 4L65E four-speed automatic transmission, and a rebuilt dual-range transfer case. </span> <img src='{{ asset('assets/img/5.avif') }}' /> </li>
                  <li> <span> This 1967 Maserati Ghibli is one of 779 examples produced between 1967 and 1972 equipped with a 4.7-liter V8. It was reportedly sold new in Europe and was imported to the US under previous ownership before it was purchased by the seller in 2005. The car is finished in red over tan leather upholstery and features a ZF five-speed manual… </span> <img src='{{ asset('assets/img/6.avif') }}' /> </li>

                </ul>
              </section>
        </div>
    </div>
</section> -->

 {{-- <section class="feature-section pb-60 ">
    <div class="container">
        <div class="feature__wrapper">
            <div class="row g-4">
                @foreach ($features as $feature)
                <div class="col-lg-3 col-sm-6">
                    <div class="feature__item bg--section">
                        <div class="feature__item-icon">
                           @php
                               echo $feature->data_values->feature_icon
                           @endphp
                        </div>
                        <h6 class="feature__item-title">{{ __($feature->data_values->title) }}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section> --}}

    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif

@endsection
