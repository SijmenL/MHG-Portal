@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Je nieuwsitem staat online!</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, je hebt laatst een nieuwsitem ingezonden voor op de website en deze hebben we online gezet!.</p>
        <br>
        <p>Je kunt dit nieuwsitem nu niet meer bewerken.</p>
        <br>
        @php
            use Illuminate\Support\Str;

            $news = \App\Models\News::findOrFail($data['relevant_id']);

        @endphp

        <div class="post">
            <h1>{{$news->title}}</h1>
            <img class="forum-image" src="{{ asset('files/news/news_images/' . $news->image) }}" alt="coverafbeelding">
            <p>{{ $news->description }}</p>
        </div>

        <a class="action-button" href="https://portal.waterscoutingmhg.nl{{ $data['link'] }}">Klik hier om dit nieuws
            bekijken!</a>
    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat je nieuwsitem is gepubliceerd.
            Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.
        </p>
    </td>
@endsection

