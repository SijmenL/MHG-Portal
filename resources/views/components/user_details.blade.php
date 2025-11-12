@props([
    'hide',
    'user'
])

<div class="overflow-scroll no-scrolbar" style="max-width: 100vw">
    <table class="table table-striped">
        <tbody>
        @if(!in_array('name', $hide))
            <tr>
                <th>Volledige naam</th>
                <td>{{ $user->name }} {{ $user->infix }} {{ $user->last_name }}</td>
            </tr>
        @endif

        @if(!in_array('dolphin_name', $hide))
            <tr>
                <th>Dolfijnen Naam</th>
                <td>{{ $user->dolfijnen_name }}</td>
            </tr>
        @endif


        @if($user->profile_picture && !in_array('profile_picture', $hide))
            <tr>
                <th>Profielfoto</th>
                <td>
                    <img alt="profielfoto" class="w-25 zoomable-image"
                         src="{{ asset('/profile_pictures/' . $user->profile_picture) }}">
                <th>
            </tr>
        @endif

        @if(!in_array('sex', $hide))
            <tr>
                <th>Geslacht</th>
                <td>{{ $user->sex }}</td>
            </tr>
        @endif

        @if(!in_array('birth_date', $hide))
            @if(!isset($user->birth_date))
                <tr>
                    <th>Geboortedatum</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">grid_off</span>Geen geboortedatum gevonden...
                        </div>
                    </td>
                </tr>
            @else
                <tr>
                    <th>Geboortedatum</th>
                    <td>{{ \Carbon\Carbon::parse($user->birth_date)->format('d-m-Y') }},
                        @php
                            $birthDate = \Carbon\Carbon::parse($user->birth_date);
                            $age = $birthDate->age;

                            $nextBirthday = $birthDate->copy()->year(now()->year);
                            if ($nextBirthday->isPast()) {
                                $nextBirthday->addYear();
                            }
                            $daysUntilBirthday = now()->diffInDays($nextBirthday);
                        @endphp

                        {{ $age }} jaar
                        ({{ $daysUntilBirthday }} dagen tot volgende verjaardag)
                    </td>
                </tr>

            @endif
        @endif

        @if(!in_array('adress', $hide))
            @if(!isset($user->street) && !isset($user->postal_code) && !isset($user->city))
                <tr>
                    <th>Adres</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">location_off</span>Geen adresgegevens
                            gevonden...
                        </div>
                    </td>
                </tr>
            @else
                <tr>
                    <th>Adres</th>
                    <td>
                        {{ collect([$user->street, $user->postal_code, $user->city])
                            ->filter()
                        ->implode(', ')
                        }}
                    </td>

                </tr>
            @endif
        @endif

        @if(!in_array('phone', $hide))
            @if(!isset($user->phone))
                <tr>
                    <th>Telefoonnummer</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">phone_disabled</span>Geen telefoonnummer
                            gevonden...
                        </div>
                    </td>
                </tr>
            @else
                <tr>
                    <th>Telefoonnummer</th>
                    <td><a href="tel:{{ $user->phone }}">{{ $user->phone }}</a></td>
                </tr>
            @endif
        @endif

        @if(!in_array('email', $hide))
            @if(!isset($user->email))
                <tr>
                    <th>E-mail</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">mail_off</span>Geen e-mailadres gevonden...
                        </div>
                    </td>
                </tr>
            @else
                <tr>
                    <th>E-mail</th>
                    <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                </tr>
            @endif
        @endif

        @if(!in_array('avg', $hide))
            <tr>
                <th>AVG Toestemming</th>
                <td>@if($user->avg)
                        Ja
                    @else
                        Nee
                    @endif</td>
            </tr>
        @endif

        @if(!in_array('roles', $hide))
            @if($user->roles->count() > 0)
            <tr>
                <th>Rollen</th>
                <td>
                    @if($user->is_associate === 1)
                        <span title="Relatie"
                              class="badge rounded-pill bg-danger text-white fs-6 p-2">Relatie</span>
                    @endif
                    @foreach ($user->roles as $role)
                        <span title="{{ $role->description }}"
                              class="badge rounded-pill text-bg-primary text-white fs-6 p-2">{{ $role->role }}</span>
                    @endforeach
                </td>
            </tr>
            @else
                <tr>
                    <th>Rollen</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">remove_moderator</span>Geen rollen gevonden...
                        </div>
                    </td>
                </tr>
            @endif
        @endif


        @php
            use App\Models\Lesson;

            // Get all lessons the user is linked to OR has created
            $lessons = Lesson::whereHas('users', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orWhere('user_id', $user->id)
                ->with('users') // eager load to prevent N+1
                ->get();
        @endphp
        @if($lessons->count() > 0 && !in_array('lessons', $hide))
            <tr>
                <th>Lessen</th>
                <td class="d-flex flex-wrap flex-column gap-2">

                    @foreach ($lessons as $lesson)
                        @php
                            // If the user created the lesson, always Praktijkbegeleider
                            $isTeacher = $lesson->user_id == $user->id ||
                                $lesson->users()
                                    ->where('user_id', $user->id)
                                    ->wherePivot('teacher', true)
                                    ->exists();
                        @endphp

                        <div class="d-flex flex-row gap-1 align-items-center bg-light p-2 rounded">
                            @if($lesson->image)
                                <img alt="les afbeelding" class="rounded"
                                     style="width: 100px; aspect-ratio: 1/1; object-fit: cover;"
                                     src="{{ asset('/files/lessons/lesson-images/' . $lesson->image) }}">
                            @endif

                            <div class="d-flex flex-column m-lg-3">
                                <p class="d-flex flex-row gap-2 align-items-center">
                                    <span><strong>{{ $lesson->title }}</strong></span>
                                    @if($isTeacher)
                                        <span class="badge rounded-pill bg-dark">Praktijkbegeleider</span>
                                    @else
                                        <span class="badge rounded-pill bg-primary">Leerling</span>
                                    @endif
                                </p>
                                <p>{{ preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($lesson->description))) }}</p>
                            </div>
                        </div>
                    @endforeach

                </td>
            </tr>
        @endif



        @if(!in_array('parents', $hide))
            @if($user->parents->count() > 0)
                <tr>
                    <th>Ouders</th>
                    <td class="d-flex flex-wrap flex-row gap-2">
                        @foreach ($user->parents as $parent)
                            @if(Route::is('admin.account-management.details'))
                                <a href="{{ route('admin.account-management.details', ['id' => $parent->id]) }}"
                                   class="d-flex flex-column gap-1 align-items-center m-2 bg-light p-2 rounded"
                                   target="_blank">
                                    @if($parent->profile_picture)
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('/profile_pictures/' . $parent->profile_picture) }}">
                                    @else
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('img/no_profile_picture.webp') }}">
                                    @endif
                                    {{ $parent->name.' '.$parent->infix.' '.$parent->last_name }}
                                </a>
                            @else
                                <div class="d-flex flex-column gap-1 align-items-center m-2 bg-light p-2 rounded">
                                    @if($parent->profile_picture)
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('/profile_pictures/' . $parent->profile_picture) }}">
                                    @else
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('img/no_profile_picture.webp') }}">
                                    @endif
                                    {{ $parent->name.' '.$parent->infix.' '.$parent->last_name }}
                                </div>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @else
                <tr>
                    <th>Ouders</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">supervised_user_circle_off</span>Geen gekoppelde
                            ouders gevonden...
                        </div>
                    </td>
                </tr>
            @endif
        @endif

        @if(!in_array('children', $hide))
            @if($user->children->count() > 0)
                <tr>
                    <th>Kinderen</th>
                    <td class="d-flex flex-wrap flex-row gap-2">
                        @foreach ($user->children as $child)
                            @if(Route::is('admin.account-management.details'))
                                <a href="{{ route('admin.account-management.details', ['id' => $child->id]) }}"
                                   class="d-flex flex-column gap-1 align-items-center m-2 bg-light p-2 rounded"
                                   target="_blank">
                                    @if($child->profile_picture)
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('/profile_pictures/' . $child->profile_picture) }}">
                                    @else
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('img/no_profile_picture.webp') }}">
                                    @endif
                                    {{ $child->name.' '.$child->infix.' '.$child->last_name }}
                                </a>
                            @else
                                <div class="d-flex flex-column gap-1 align-items-center m-2 bg-light p-2 rounded">
                                    @if($child->profile_picture)
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('/profile_pictures/' . $child->profile_picture) }}">
                                    @else
                                        <img alt="profielfoto" class="profle-picture"
                                             src="{{ asset('img/no_profile_picture.webp') }}">
                                    @endif
                                    {{ $child->name.' '.$child->infix.' '.$child->last_name }}
                                </div>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @else
                <tr>
                    <th>Kinderen</th>
                    <td>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded me-2">supervised_user_circle_off</span>Geen gekoppelde
                            kinderen gevonden...
                        </div>
                    </td>
                </tr>
            @endif
        @endif

        @if(!in_array('updated_at', $hide))
            <tr>
                <th>Aangepast op</th>
                <td>{{ \Carbon\Carbon::parse($user->updated_at)->format('d-m-Y H:i:s') }}</td>
            </tr>
        @endif

        @if(!in_array('created_at', $hide))
            <tr>
                <th>Aangemaakt op</th>
                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s') }}</td>
            </tr>
        @endif

        @if(isset($user->member_date) && !in_array('member_date', $hide))
            <tr>
                <th>Lid vanaf</th>
                <td> @if($user->member_date)
                        {{ Carbon\Carbon::parse($user->member_date)->format('d-m-Y') }}
                    @endif
                </td>
            </tr>
        @endif

        @if(isset($user->member_date_end) && !in_array('member_date', $hide))
            <tr>
                <th>Uitgeschreven op</th>
                <td> @if($user->member_date_end)
                        {{ Carbon\Carbon::parse($user->member_date_end)->format('d-m-Y') }}
                    @endif
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
