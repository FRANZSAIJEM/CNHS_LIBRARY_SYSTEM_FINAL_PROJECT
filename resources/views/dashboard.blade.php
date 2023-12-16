<x-app-layout>
    <x-slot name="header" >
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="rounded-md shadow-md bg-white dark:bg-dark-eval-1 p-3 text-xl font-semibold leading-tight">
                <i class="fa-solid fa-chart-line"></i> {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div style="display: grid; place-items: center;">
            @if(session('success'))
               <div class="success-message-container">
                   <div class="success-message bg-white rounded-lg shadow-md text-green-700 p-4">
                    <span class="rounded-full p-1 ps-2 pe-2 bg-green-200 text-slate-500" ><i class="fa-solid fa-check"></i></span> {{ session('success') }}
                        <div class="loadingBar"></div>
                   </div>
               </div>
           @endif
       </div>
        <div class="text-center ">
            @if (!Auth::user()->is_admin && !Auth::user()->is_assistant)
            <h1><b><i class="fa-solid fa-book"></i> Total Available Books</b></h1>

            <h3 style="font-size: 50px; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif">{{$availableBooks}}</h3>
            <x-nav-link  :href="route('bookList')" :active="request()->routeIs('bookList')">
                <b style="box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
                    width: 200px" class="bg-slate-500 text-white p-5 rounded-xl"><i class="fa-solid fa-handshake-angle"></i> {{ __('Borrow Book') }}</b>
            </x-nav-link>
            @endif

            @if (Auth::user()->is_admin || Auth::user()->is_assistant)
            <h1><b><i class="fa-solid fa-book"></i> Total Books</b></h1>

            <h3 style="font-size: 50px; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif">{{$totalBooks}}</h3>
            <x-nav-link  :href="route('bookList')" :active="request()->routeIs('bookList')">
                <b style="box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
                    width: 200px" class="bg-slate-500 text-white p-5 rounded-xl"><i class="fa-solid fa-eye"></i> {{ __('View All Books') }}</b>
            </x-nav-link>
            @endif
        </div>
    </div>
    <br>


    <div style="display: none;">
        <h1><b><i class="fa-solid fa-hourglass-start"></i> Time Remaining</b></h1>
        @if ($acceptedRequest)
        <div class="countdown-timer" data-target="{{ $acceptedRequest->timeDuration->date_return_seconds }}">
            <!-- Countdown timer will be updated here using JavaScript -->
        </div>
        @else
        <p>No accepted request found.</p>
        @endif
    </div>



    @if (!Auth::user()->is_admin && !Auth::user()->is_assistant)
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="text-center ">
            <h1><b><i class="fa-solid fa-clock"></i> Late Returns</b></h1>
            {{-- <h3 style="font-size: 50px; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif"><div class="flex justify-center">₱ &nbsp; <div id="fines-container" style="display: none;">{{ number_format($totalFines, 2)}}</div></div></h3> --}}
            <h3 style="font-size: 50px; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif">
                <div class="flex justify-center">
                    {{-- ₱ &nbsp; --}}
                    @if ($totalFine !== null)
                        {{ number_format($totalFine) }}
                    @else
                        0.00
                    @endif

{{--
                    <div id="fines-container" style="display: none;">





                    </div> --}}

                    <span style="font-size: 17px;" class="mt-3 ms-2">Instances of <br> late returns.</span>
                </div>
            </h3>

{{--
            {{$date_pickup}}
            {{$date_return}} --}}

            <x-nav-link  :href="route('notifications')" :active="request()->routeIs('notifications')">
                <b style="box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
                    width: 200px" class="bg-slate-500 text-white p-5 rounded-xl"><i class="fa-solid fa-circle-info"></i> {{ __('Details') }}</b>
            </x-nav-link>
        </div>
    </div>
    @endif

    @if (Auth::user()->is_admin)
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="text-center ">
            <h1><b><i class="fa-solid fa-bell"></i> Total Requests</b></h1>
            <h3 style="font-size: 50px; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif">{{$totalRequests}}</h3>
            <x-nav-link  :href="route('requests')" :active="request()->routeIs('requests')">
                <b style="box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
                    width: 200px" class="bg-slate-500 text-white p-5 rounded-xl"><i class="fa-solid fa-code-pull-request"></i> {{ __('Requests') }}</b>
            </x-nav-link>
        </div>
    </div>
    @endif
    <br>

    @if (Auth::user()->is_admin)
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="text-center ">
            <h1><b><i class="fa-solid fa-users"></i> Total Students</b></h1>
            <h3 style="font-size: 50px; font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif">{{$totalStudents}}</h3>
            <x-nav-link  :href="route('student')" :active="request()->routeIs('student')">
                <b style="box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset;
                    width: 200px" class="bg-slate-500 text-white p-5 rounded-xl"><i class="fa-solid fa-graduation-cap"></i> {{ __('Students') }}</b>
            </x-nav-link>
        </div>
    </div>
    @endif

       {{-- Loading Screen --}}
       <div id="loading-bar" class="loading-bar"></div>
      <style>
  .success-message-container {
        position: fixed;
    }

    .success-message {
        text-align: right;
        margin-bottom: 150px;
        opacity: 0;
        transition: opacity 0.3s, transform 0.3s;
    }

    .loadingBar{
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background-color: #00af2cab;
        transition: width 3s linear;
    }





.loading-bar {
  width: 0;
  height: 5px; /* You can adjust the height as needed */
  background-color: #5fadff; /* Loading bar color */
  position: fixed;
  top: 0;
  left: 0;
  z-index: 9999;
  transition: width 0.3s ease; /* Adjust the animation speed as needed */
}

        </style>
<script>
// JavaScript to show and hide the loading bar
window.addEventListener('beforeunload', function () {
  document.getElementById('loading-bar').style.width = '100%';
});

window.addEventListener('load', function () {
  document.getElementById('loading-bar').style.width = '0';
});



function initializeCountdown() {
    const countdownElements = document.querySelectorAll('.countdown-timer');
    countdownElements.forEach(element => {
        const targetTimestamp = parseInt(element.getAttribute('data-target'), 10);
        updateCountdown(element, targetTimestamp);
    });
}

function updateCountdown(element, targetTimestamp) {
    const currentTimestamp = Math.floor(Date.now() / 1000);
    const remainingTime = targetTimestamp - currentTimestamp;

    if (remainingTime > 0) {
        const hours = Math.floor(remainingTime / 3600);
        const minutes = Math.floor((remainingTime % 3600) / 60);
        const seconds = remainingTime % 60;
        element.innerHTML = hours + "h " + minutes + "m " + seconds + "s";

        // Show "0.00" in the fines container
        const finesContainer = document.getElementById('fines-container');
        finesContainer.style.display = 'block';
        finesContainer.innerHTML = '0';

        setTimeout(() => updateCountdown(element, targetTimestamp), 1000);
    } else {
        element.innerHTML = "Expired";

        // Show the fines container when the countdown expires
        const finesContainer = document.getElementById('fines-container');
        finesContainer.style.display = 'block';
    }
}

// Initialize the countdown timers when the page loads
window.addEventListener('DOMContentLoaded', initializeCountdown);


window.addEventListener('DOMContentLoaded', (event) => {
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '1';
                successMessage.style.transform = 'translateY(0)';
            }, 100);
        }
    });

    window.addEventListener('DOMContentLoaded', (event) => {
        const successMessageContainer = document.querySelector('.success-message-container');
        const successMessage = document.querySelector('.success-message');
        const loadingBar = document.querySelector('.loadingBar');

        if (successMessage) {
            setTimeout(() => {
                loadingBar.style.width = '100%';
            }, 5000);

            setTimeout(() => {
                loadingBar.style.opacity = '0';
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    successMessageContainer.remove();
                }, 5000);
            }, 5000); // 3 seconds for the loading bar to animate, then 100 milliseconds for the success message to disappear
        }
    });

    </script>
</x-app-layout>
