@extends('layouts.contact')

@include('partials.editor')

@section('content')
    <div class="bg-white">
        <script>
            function sendHeight() {
                const height = document.body.getBoundingClientRect().height + 100;
                console.log('Sending height:', height);
                window.parent.postMessage(height, 'https://waterscoutingmhg.nl'); // Ensure this matches the parent origin
            }

            window.addEventListener('message', (event) => {
                console.log('Message received from:', event.origin);
                if (event.origin === 'https://waterscoutingmhg.nl' && event.data === 'getHeight') {
                    console.log('Valid request for height');
                    sendHeight();
                }
            });


            if (window.top === window.self) {
                // Redirect to the parent page if the child page is accessed directly
                window.location.href = `https://waterscoutingmhg.nl/over-onze-club/nieuws`;
            } else {
                sendHeight();  // Send height on load in case of initial message issue
            }

        </script>


        @if($news->count() > 0)
            <script>
                function breakOut(id) {
                    window.parent.location.href = `https://waterscoutingmhg.nl/over-onze-club/nieuws-item?news=${id}`;
                }
            </script>
            @if($items > 3)
            <form id="auto-submit" method="GET" class="user-select-forum-submit mt-5 w-100 d-flex justify-content-center">
                <div class="d-flex w-75">
                    <div class="d-flex flex-row-responsive gap-2 align-items-center mb-3 w-100">
                        <div class="input-group">
                            <label for="search" class="input-group-text" id="basic-addon1">
                                <span class="material-symbols-rounded">search</span></label>
                            <input id="search" name="search" type="text" class="form-control"
                                   placeholder="Zoeken op nieuws."
                                   aria-label="Zoeken" aria-describedby="basic-addon1" value="{{ $search }}" onchange="this.form.submit();">
                        </div>
                    </div>
                </div>
            </form>
            @endif
            <div class="d-flex flex-row-responsive flex-wrap gap-4 justify-content-center" style="margin: 30px; padding: 5px">
                @foreach($news as $news_item)
                    <a onclick="breakOut({{ $news_item->id }})" class="text-black text-decoration-none">

                        <div class="card" style="cursor: pointer; margin: 0 auto">
                            <p class="badge rounded-pill bg-info text-black"
                               style="position: absolute; top: 15px; right: 15px; font-size: 1rem">{{ $news_item->category }}</p>
                            <img alt="Nieuws afbeelding" class="card-img-top"
                                 src="{{ asset('/files/news/news_images/'.$news_item->image.' ') }}">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <p style="font-weight: bolder">{{ $news_item->title }}</p>
                                    <p>{{ $news_item->description }}</p>
                                </div>
                                <div>
                                    <a onclick="breakOut({{ $news_item->id }})"
                                       class="d-flex flex-row gap-2 align-items-center text-decoration-none text-black"><span
                                            class="material-symbols-rounded me-2">chevron_right</span>Lees verder!</a>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-column gap-1">
                                <p>{{ $news_item->date->format('d-m-Y') }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            @if($items > 3)
        <div class="d-flex flex-row w-100 align-items-center justify-content-center">
            {{ $news->appends(request()->query())->links() }}
        </div>
            @endif
        @else
            <div class="alert alert-warning d-flex align-items-center mt-4" role="alert">
                <span class="material-symbols-rounded me-2">unsubscribe</span>Geen nieuws gevonden...
            </div>
        @endif
    </div>
@endsection
