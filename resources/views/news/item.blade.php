<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if($news !== null && $news->accepted === 1)

        <title>{{ $news->title }}</title>


        <meta property="og:url" content="{{ request()->fullUrl() }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="{{ $news->title }}"/>
        <meta property="og:description" content="{{ $news->description }}"/>
        <meta property="og:image" content="{{ asset('files/news/news_images/' . $news->image) }}"/>
    @endif


    <link rel="apple-touch-icon" sizes="180x180" href="/public/apple-touch-icon.png">
    <link rel="manifest" href="/public/site.webmanifest">
    <link rel="mask-icon" href="/public/safari-pinned-tab.svg" color="#0092df">
    <meta name="msapplication-TileColor" content="#1c244b">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"/>
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])


</head>
<body class="bg-white">


@if($news !== null && $news->accepted === 1)
    <script>
        function sendHeight() {
            const height = document.body.getBoundingClientRect().height + 100;
            window.parent.postMessage(height, 'https://waterscoutingmhg.nl'); // Ensure this matches the parent origin
        }

        window.addEventListener('message', (event) => {
            if (event.origin === 'https://waterscoutingmhg.nl' && event.data === 'getHeight') {
                sendHeight();
            }
        });


        if (window.top === window.self) {
            // Redirect to the parent page if the child page is accessed directly
            window.location.href = `https://waterscoutingmhg.nl/over-onze-club/nieuws-item/?news={{ $news->id }}`;
        } else {
            sendHeight();  // Send height on load in case of initial message issue
        }

    </script>



    <div id="fb-root"></div>
    <script>
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <div class="header" style="background-image: url({{ asset('/files/news/news_images/'.$news->image) }})">
        <div>
            <p class="header-title">{{ $news->title }}</p>
        </div>
    </div>
    <div class="container col-md-11">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <h1>{{ $news->title }}</h1>
        <div class="d-flex gap-2 flex-row-responsive align-items-start mb-3">
            <span>{{ $news->date->format('d-m-Y') }}</span>
            <span class="no-mobile">·</span>
            <span
                class="text-capitalize badge rounded_pill bg-info text-black d-flex align-items-center justify-content-center">{{ $news->category }}</span>
            <span class="no-mobile">·</span>
            <span
                style="font-weight: bolder">{{ $news->user->name . ' ' . $news->user->infix . ' ' . $news->user->last_name }}</span>
            @if(isset($news->speltak))
                <span class="no-mobile">·</span>
                <span>{{ $news->speltak }}</span>
            @endif
        </div>
        <h5 style="font-weight: bolder;">{{ $news->description }}</h5>

        <div class="d-flex flex-row gap-2 align-items-center">
            <div class="fb-share-button"
                 data-href="{{ request()->fullUrl() }}"
                 data-layout="button_count">
            </div>

            <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button"
               data-text="{{ $news->description }}" data-url="{{request()->fullUrl()}}"
               data-show-count="false">Tweet</a>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

        </div>

        <img class="news-image-full p-4" src="{{ asset('files/news/news_images/' . $news->image) }}"
             alt="nieuws afbeelding">
        <div class="news-content">{!! $news->content !!}</div>
    </div>
@else
    <div class="header" style="background-image: url({{ asset('/files/news/DSC00617.JPG') }})">
        <div>
            <p class="header-title">Geen nieuws gevonden</p>
        </div>
    </div>
    <div class="container col-md-11">
        <h1>We hebben geen nieuws gevonden</h1>
        <p>Het item is mogelijk verwijderd of verplaatst.</p>

        <button onclick="breakOut()" class="btn btn-primary text-white">Ga terug naar het overzicht</button>

        <script>
            function breakOut() {
                window.parent.location.href = 'https://waterscoutingmhg.nl/over-onze-club/nieuws';
            }
        </script>
    </div>

@endif
</body>
</html>
