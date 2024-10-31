@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Er is een nieuwe {{$data['location']}} post geplaatst!</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, @if($data['reciever_is_dolfijn']) {{$data['sender_dolfijnen_name']}} @else {{$data['sender_full_name']}} @endif heeft een post geplaatst.</p>
        <br>
        <p>Deze post kun je het beste op het <a href="https://portal.waterscoutingmhg.nl{{ $data['link'] }}">ledenportaal</a> bekijken, daar zal altijd de beste versie staan.</p>
        <br>
        <p>In je mail kun je de video's van de post niet bekijken.</p>
        <br>

        @php
            use Illuminate\Support\Str;

            $post = \App\Models\Post::findOrFail($data['relevant_id']);

        @endphp

        <a class="no-link" href="https://portal.waterscoutingmhg.nl{{ $data['link'] }}">
        <div class="post">{!!   $post->content !!}</div>
        </a>

    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat iemand binnen jouw speltak een post heeft geplaatst.
            Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.
        </p>
    </td>
@endsection

