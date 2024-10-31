@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">Er is een reactie onder je post geplaatst!</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, @if($data['reciever_is_dolfijn'])
                {{$data['sender_dolfijnen_name']}}
            @else
                {{$data['sender_full_name']}}
            @endif heeft een reactie achtergelaten onder je post.</p>
        <br>

        @php
            use Illuminate\Support\Str;

            $comment = \App\Models\Comment::findOrFail($data['relevant_id']);
            $post = \App\Models\Post::findOrFail($comment->post_id);

        @endphp

        <a class="no-link comment" href="https://portal.waterscoutingmhg.nl{{ $data['link'] }}">
            <p><strong>@if($data['reciever_is_dolfijn'])
                        {{$data['sender_dolfijnen_name']}}
                    @else
                        {{$data['sender_full_name']}}
                    @endif</strong> {{$comment->created_at->format('d-m-Y H:i')}}</p>
            <div>
                {!! $comment->content !!}
            </div>
        </a>
        <br>
        <a class="action-button" href="https://portal.waterscoutingmhg.nl{{ $data['link'] }}">Klik hier om de reactie te
            bekijken!</a>
    </div>
@endsection

@section('main_footer')
    <td style="padding-top: 30px;">
        <p class="footer-bold">Waarom ontvang jij deze email?</p>
        <p class="footer-text">
            Deze email is automatisch gegenereerd omdat iemand op je post een reactie heeft achtergelaten.
            Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.
        </p>
    </td>
@endsection

