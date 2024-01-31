@php
    $header = getContent('header.content', true);
@endphp


 <!-- Header -->
 <div class="header-top bg--section">
    <div class="container">
        <div class="header__top__wrapper">
            <ul>
                <li>
                    <span class="name">@lang('Email'): </span><a href="mailto:{{ $header->data_values->email }}" class="text--base">{{ __($header->data_values->email) }}</a>
                </li>
               {{-- <li>
                    <span class="name">@lang('Call Us'): </span><a href="tel:{{ $header->data_values->mobile }}" class="text--base">{{ __($header->data_values->mobile) }}</a>
                </li>--}}
            </ul>
            <form action="{{ route('product.search') }}" class="search-form">
                <div class="input-group input--group">
                    <input type="text" class="form-control" name="search_key" value="{{ request()->search_key }}" placeholder="@lang('Vehicle Name')">
                    <button type="submit" class="cmn--btn"><i class="las la-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="header-bottom">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo me-lg-4">
                <a href="{{ route('home') }}">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}" alt="logo">
                </a>
            </div>
            <div class="menu-area">
                <div class="menu-close">
                    <i class="las la-times"></i>
                </div>
                <ul class="menu">
                    <li>
                        <a href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    <li>
                        <a href="{{ route('product.all') }}">@lang('Car Auction')</a>
                        {{-- <a href="{{ route('product.all') }}">@lang('Products')</a> --}}

                    </li>
                    <li>
                        <a href="{{ route('merchants') }}">@lang('Open Mall')</a>
                    </li>
                    <li>
                        <a href="{{ route('about.us') }}">@lang('About Us')</a>

                    </li>
                    <li>
                        <a href="{{ route('blog') }}">@lang('Blog')</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>
                </ul>
                <div class="change-language d-md-none mt-4 justify-content-center">
                    <div class="sign-in-up">
                        <span><i class="fas la-user"></i></span>
                        <a href="{{ route('user.login') }}">@lang('Login')</a>
                        <a href="{{ route('merchant.login') }}">@lang('Merchant Login')</a>
                    </div>
                </div>
            </div>
            <div class="change-language ms-auto me-3 me-lg-0">
                <div class="sign-in-up d-none d-sm-block">
                    {{-- <span><i class="fas la-user"></i></span> --}}
                    @auth('merchant')


                        <button class="btn dropdown-toggle text-white" type="button" id="loginButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <a href="{{ route('user.home') }}">{{ auth()->guard('merchant')->user()->firstname }}</a>

                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown" id="loginDropdown">
                            <li><a href="{{ route('merchant.logout') }}" class="dropdown-item" style="color: black">Logout</a></li>
                            <li><a href="{{ route('user.login') }}" class="dropdown-item" style="color: black">Switch to User</a></li>
                        </ul>

                  @else
                    @guest
                        <button type="button" class="btn dropdown-toggle text-white" id="loginButton" aria-haspopup="true" aria-expanded="false">
                            <i class="fas la-user"></i>
                        </button>
                        <ul class="dropdown-menu" id="loginDropdown" aria-labelledby="loginButton">
                            <li><a href="{{ route('user.login') }}" class="dropdown-item" style="color: black">User Login</a></li>
                            <li><a href="{{ route('merchant.login') }}" class="dropdown-item" style="color: black">Submit Vehicle</a></li>
                        </ul>
                    @else
                        <button type="button" class="btn dropdown-toggle text-white" id="loginButton" aria-haspopup="true" aria-expanded="false">
                            <a href="{{ route('user.home') }}">{{ Auth::user()->firstname }}</a>
                        </button>
                        <ul class="dropdown-menu" id="loginDropdown" aria-labelledby="loginButton">
                            <li><a href="{{ route('user.logout') }}" class="dropdown-item" style="color: black">Logout</a></li>
                            <li><a href="{{ route('merchant.login') }}" class="dropdown-item" style="color: black">Switch to Merchant</a></li>
                        </ul>
                    @endguest
                @endauth

                    {{-- @authP
                        <a href="{{ route('user.home') }}">@lang('User Dashboard')</a>
                    @endauth

                    @auth('merchant')
                        <a href="{{ route('merchant.dashboard') }}">@lang('Merchant Dashboard')</a>
                    @endauth

                    @if (!auth()->check() && !auth()->guard('merchant')->check())
                    <button type="button" class="btn dropdown-toggle text-white" id="loginButton" aria-haspopup="true" aria-expanded="false">
                        Login
                    </button>
                    <ul class="dropdown-menu" id="loginDropdown" aria-labelledby="loginButton">
                        <li><a href="{{ route('user.login') }}" class="dropdown-item" style="color: black">@lang('User Login')</a></li>
                        <li><a href="{{ route('merchant.login') }}" class="dropdown-item" style="color: black">@lang('Merchant Login')</a></li>
                    </ul>
                    @endif   --}}
                </div>
                {{-- <select> --}}

                    <a href="#" class="btn " id="cartButton">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge badge-danger" id="cartItemCount"></span>
                    </a>


                {{-- </select> --}}
            </div>

        </div>
    </div>
</div>
<!-- Header -->
@push('script')
<script>
      $(document).ready(function() {
        // Function to update the cart count
        function updateCartCount() {
        $.ajax({
            url: "{{route('count.order')}}", // Replace with the actual URL of your server-side script
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                    let count = response.count;
                        //  console.log(count);
                    if (count === 0) {
                        // If 'count' is zero, hide the cart button and badge
                        $('#cartButton').hide();
                    } else {
                        // If 'count' is not zero, display the cart button and badge
                        $('#cartItemCount').text(count);
                    }

            },
            error: function(xhr, status, error) {
            console.error('Error fetching cart count: ' + error);
            }
        });
        }

        // Initial call to update cart count
        updateCartCount();

      });
    // Get references to the button and dropdown elements
    const loginButton = document.getElementById("loginButton");
    const loginDropdown = document.getElementById("loginDropdown");

    // Add a click event listener to the button
    loginButton.addEventListener("click", function () {
        // Toggle the "show" class on the dropdown element
        loginDropdown.classList.toggle("show");
    });

    // Close the dropdown when clicking outside of it
    document.addEventListener("click", function (event) {
        if (!loginDropdown.contains(event.target) && !loginButton.contains(event.target)) {
            loginDropdown.classList.remove("show");
        }
    });

</script>
@endpush
