@extends('emails.layouts.mail')

@section('title')
    <h1 class="email-title">@if($data['reciever_is_dolfijn'])
            {{$data['sender_dolfijnen_name']}}
        @else
            {{$data['sender_full_name']}}
        @endif Heeft op je gereageerd!</h1>
@endsection

@section('info')
    <div>
        <p>Beste {{ $data['reciever_name'] }}, @if($data['reciever_is_dolfijn'])
                {{$data['sender_dolfijnen_name']}}
            @else
                {{$data['sender_full_name']}}
            @endif heeft op je reactie gereageerd.</p>
        <br>

        @php
            use Illuminate\Support\Str;

            $reaction = \App\Models\Comment::findOrFail($data['relevant_id']);
            $comment = \App\Models\Comment::findOrFail($reaction->comment_id);

        @endphp

        <a class="no-link comment" href="https://portal.waterscoutingmhg.nl{{ $data['link'] }}">
            <p><strong class="own-comment">Jij</strong> {{$comment->created_at->format('d-m-Y H:i')}}</p>
            <div>
                {!! $comment->content !!}
            </div>

            <div class="reaction">
                <p><strong>@if($data['reciever_is_dolfijn'])
                            {{$data['sender_dolfijnen_name']}}
                        @else
                            {{$data['sender_full_name']}}
                        @endif</strong> {{$reaction->created_at->format('d-m-Y H:i')}}</p>
                <div>
                    {!! $reaction->content !!}
                </div>
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
            Deze email is automatisch gegenereerd omdat iemand op je reactie heeft gereageerd.
            Als je deze notificaties niet meer wilt ontvangen, wijzig dan je instellingen op de instellingen pagina.
        </p>
    </td>
@endsection

