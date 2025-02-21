<?php

namespace App\Http\Controllers;

use App\Exports\AgendaExport;
use App\Models\Activity;
use App\Models\ActivityFormElement;
use App\Models\ActivityFormResponses;
use App\Models\Lesson;
use App\Models\Log;
use App\Models\News;
use App\Models\Presence;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use MailerSend\Common\Roles;

class AgendaController extends Controller
{

    public function home()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('agenda.home', ['user' => $user, 'roles' => $roles]);
    }

    // Inschrijvingen & aanwezigheid
    public function agendaSubmissions(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $search = $request->query('search', '');
        $currentDate = now();

        $activities = Activity::query()
            ->with('activityFormElements') // Eager load the formElements relationship
            ->whereHas('activityFormElements') // Ensure activities have related form elements
            ->when(empty($search), function ($query) use ($currentDate) {
                // Include events that are starting today or in the future or that are ongoing
                $query->where(function ($query) use ($currentDate) {
                    $query->where('date_start', '>=', $currentDate->startOfDay())
                        ->orWhere('date_end', '>=', $currentDate->startOfDay());
                });
            })
            ->when($search, function ($query, $search) {
                // Include past activities when searching
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('date_start', 'like', "%{$search}%")
                        ->orWhere('date_end', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('organisator', 'like', "%{$search}%");
                });
            })
            ->orderByRaw(
                'CASE
            WHEN date_end >= ? THEN date_end
            WHEN date_start >= ? THEN date_start
            ELSE NULL
        END ASC,
        date_start ASC',
                [$currentDate->startOfDay(), $currentDate->startOfDay()]
            )
            ->paginate(10);


        return view('agenda.submissions', [
            'user' => $user,
            'roles' => $roles,
            'search' => $search,
            'activities' => $activities
        ]);
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lessonId = $request->query('lessonId');
        $lesson = $lessonId ? Lesson::find($lessonId) : null;

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            ($lesson && $lesson->user_id === $user->id) ||
            ($lesson && $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists());

        if ($isTeacher === false) {
            return redirect()->route('agenda.edit')->with('error', 'Deze activiteit bestaat niet.');
        }

        $search = $request->query('search', '');
        $currentDate = now();

        $activities = Activity::query()
            ->when(empty($search), function ($query) use ($currentDate) {
                // Include activities that are either upcoming or ongoing (date_end is in the future)
                $query->where(function ($query) use ($currentDate) {
                    $query->where('date_start', '>=', $currentDate->startOfDay())
                        ->orWhere('date_end', '>=', $currentDate->startOfDay());
                });
            })
            ->when($search, function ($query, $search) {
                // Include past activities when searching
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('date_start', 'like', "%{$search}%")
                        ->orWhere('date_end', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('organisator', 'like', "%{$search}%");
                });
            })
            // When lessonId is provided, include activities with that lesson_id, regardless of the user
            ->when($lessonId, function ($query) use ($lessonId) {
                $query->where('lesson_id', $lessonId);
            })
            // Exclude activities with a lesson_id when lessonId is null
            ->when(!$lessonId && !$user->roles->contains('role', 'Administratie'), function ($query) use ($user) {
                $query->whereNull('lesson_id')
                    ->where('user_id', $user->id);
            })
            ->orderByRaw(
                'CASE
            WHEN date_end >= ? THEN date_end
            WHEN date_start >= ? THEN date_start
            ELSE NULL
        END ASC,
        date_start ASC',
                [$currentDate->startOfDay(), $currentDate->startOfDay()] // Ensure both parameters are passed
            )
            ->paginate(10);

        return view('agenda.edit', [
            'user' => $user,
            'roles' => $roles,
            'search' => $search,
            'activities' => $activities,
            'lesson' => $lesson
        ]);
    }


    public function editActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $all_roles = Role::all();

        $lessonId = $request->query('lessonId');
        $lesson = $lessonId ? Lesson::find($lessonId) : null;

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            ($lesson && $lesson->user_id === $user->id) ||
            ($lesson && $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists());

        $activity = Activity::with('formElements')->findOrFail($id);

        if (!$activity) {
            return redirect()->back()->with('error', 'Activiteit niet gevonden.');
        }

        // Check if the activity is attached to a lesson and no lessonId is provided in the URI
        if ((int)$activity->lesson_id !== (int)$lessonId) {
            return redirect()->route('agenda.edit')->with('error', 'Deze activiteit is gekoppeld aan een les, maar de les-ID ontbreekt.');
        }

        // Ownership or teacher check
        if ($activity->user_id !== Auth::id() && !$user->roles->contains('role', 'Administratie')) {
            if (!$lesson || !$isTeacher) {
                return redirect()->back()->with('error', 'Activiteit niet gevonden.');
            }
        }

        return view('agenda.edit-activity', [
            'user' => $user,
            'roles' => $roles,
            'activity' => $activity,
            'all_roles' => $all_roles,
            'lesson' => $lesson,
        ]);
    }


    public function editActivitySave(Request $request, $id)
    {
        // Validate the request inputs
        $validatedData = $request->validate([
            'title' => 'string|required',
            'content' => 'string|max:65535|nullable',
            'date_start' => 'date|required',
            'date_end' => 'date|required',
            'roles' => 'array|nullable',
            'users' => 'string|nullable',
            'public' => 'boolean|required',
            'presence' => 'boolean|required',
            'presence-date' => 'date|nullable',
            'price' => 'numeric|nullable',
            'location' => 'string|nullable',
            'organisator' => 'string|nullable',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000',
            'form_labels' => 'nullable|array',
            'form_types' => 'nullable|array',
            'form_options' => 'nullable|array',
            'is_required' => 'nullable|array',
            'lesson_id' => 'nullable'
        ]);

        try {
            // Find the activity to update
            $activity = Activity::findOrFail($id);

            // Process image upload
            $newPictureName = $activity->image; // Keep the existing image by default
            if ($request->hasFile('image')) {
                // If a new image is uploaded, generate a new file name
                $newPictureName = time() . '.' . $request->file('image')->extension();
                $destinationPath = 'files/agenda/agenda_images';
                $request->file('image')->move(public_path($destinationPath), $newPictureName);
            }

            // Handle roles and users input
            $roles = $request->input('roles') ? implode(', ', $request->input('roles')) : null;
            $users = $request->input('users') ? implode(', ', array_map('trim', array_filter(explode(',', $request->input('users'))))) : null;

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {

                $presence = $request->input('presence');

                if ($presence === "1" && $request->filled('presence-date')) {
                    $presence = $request->input('presence-date');
                }

                // Update the activity
                $activity->update([
                    'content' => $request->input('content'),
                    'price' => $request->input('price'),
                    'organisator' => $request->input('organisator'),
                    'location' => $request->input('location'),
                    'date_start' => $request->input('date_start'),
                    'date_end' => $request->input('date_end'),
                    'title' => $request->input('title'),
                    'user_id' => Auth::id(),
                    'roles' => $roles,
                    'users' => $users,
                    'image' => $newPictureName,
                    'public' => $request->input('public'),
                    'presence' => $presence,
                    'lesson_id' => $request->input('lesson_id'),
                ]);

                // Log the update of the activity
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Update activity', 'agenda', 'Activity id: ' . $activity->id, '');

                // Handle form elements (if provided)
                if (isset($validatedData['form_labels'])) {
                    // Clear existing form elements associated with the activity
                    ActivityFormElement::where('activity_id', $activity->id)->delete();

                    foreach ($validatedData['form_labels'] as $index => $label) {
                        $type = $validatedData['form_types'][$index];
                        $isRequired = isset($validatedData['is_required'][$index]);

                        $optionsString = null;
                        // If the field type is select, radio, or checkbox, save options
                        if (in_array($type, ['select', 'radio', 'checkbox']) && isset($validatedData['form_options'][$index])) {
                            $optionsString = implode(',', $validatedData['form_options'][$index]);
                        }

                        // Create new form element
                        ActivityFormElement::create([
                            'option_value' => $optionsString,
                            'activity_id' => $activity->id,
                            'label' => $label,
                            'type' => $type,
                            'is_required' => $isRequired,
                        ]);
                    }

                    // Log the creation of the form elements
                    $log->createLog(auth()->user()->id, 2, 'Update activity form', 'agenda', 'Activity id: ' . $activity->id, 'Er is een inschrijfformulier bijgewerkt.');
                }


                // Check if lesson_id is provided in the request
                $lessonId = $request->input('lesson_id');

                // If lesson_id is present, include it in the redirect, otherwise just use the activity ID
                if ($lessonId) {
                    return redirect()->route('agenda.edit', ['id' => $activity->id, 'lessonId' => $lessonId])
                        ->with('success', 'Je agendapunt is bijgewerkt!');
                } else {
                    return redirect()->route('agenda.edit', ['id' => $activity->id])
                        ->with('success', 'Je agendapunt is bijgewerkt!');
                }

            } else {
                throw ValidationException::withMessages(['content' => 'Je agendapunt kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je agendapunt. Probeer het opnieuw.')->withInput();
        }
    }


    public function agendaSubmissionsActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        // Get search term from query parameters, default to empty string
        $search = $request->query('search', '');

        // Retrieve the activity by its ID
        $activity = Activity::find($id);

        if (!$activity) {
            return redirect()->route('agenda.submissions')->with('error', 'Activiteit niet gevonden.');
        }

        // Retrieve all form submissions for this activity
        $formSubmissions = ActivityFormResponses::where('activity_id', $id)
            ->with('formElement') // Eager-load the related form elements
            ->get()
            ->groupBy('submitted_id');

        if ($formSubmissions->count() < 1) {
            return redirect()->route('agenda.submissions')->with('error', 'Activiteit niet gevonden.');
        }

        // Apply search filter to the grouped form submissions
        if (!empty($search)) {
            $formSubmissions = $formSubmissions->filter(function ($group) use ($search) {
                // Keep the group if any entry within the group matches the search term
                return $group->contains(function ($entry) use ($search) {
                    return stripos($entry->response, $search) !== false;
                });
            });
        }

        // Return view with activity and grouped form submission data
        return view('agenda.submissions-activity', [
            'activity' => $activity,
            'formSubmissions' => $formSubmissions, // This is now grouped by submitted_id
            'user' => $user,
            'roles' => $roles,
            'search' => $search,
        ]);
    }


    public function agendaPresence(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $search = $request->query('search', '');

        $lessonId = $request->query('lessonId');
        $lesson = $lessonId ? Lesson::find($lessonId) : null;

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            ($lesson && $lesson->user_id === $user->id) ||
            ($lesson && $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists());

        if (isset($lesson) && $isTeacher === false) {
            return redirect()->route('agenda.presence')->with('error', 'Deze activiteit bestaat niet.');
        }

        $currentDate = now();

        $activities = Activity::query()
            ->where('presence', '!=', 0)
            ->when(empty($search), function ($query) use ($currentDate) {
                // Include activities that are either upcoming or ongoing
                $query->where(function ($query) use ($currentDate) {
                    $query->where('date_start', '>=', $currentDate->startOfDay())
                        ->orWhere('date_end', '>=', $currentDate->startOfDay());
                });
            })
            ->when($search, function ($query, $search) {
                // Include past activities when searching
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('date_start', 'like', "%{$search}%")
                        ->orWhere('date_end', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('organisator', 'like', "%{$search}%");
                });
            })
            ->when($lessonId, function ($query) use ($lessonId) {
                // Show all activities connected to the lesson when viewing from a lesson
                $query->where('lesson_id', $lessonId);
            })
            ->when(!$lessonId, function ($query) use ($user) {
                // Only show activities created by the user when not viewing from a lesson
                $query->whereNull('lesson_id');
            })
            ->orderByRaw(
                'CASE
            WHEN date_end >= ? THEN date_end
            WHEN date_start >= ? THEN date_start
            ELSE NULL
        END ASC,
        date_start ASC',
                [$currentDate->startOfDay(), $currentDate->startOfDay()] // Ensure both parameters are passed
            )
            ->paginate(10);

        return view('agenda.presence', [
            'user' => $user,
            'roles' => $roles,
            'search' => $search,
            'activities' => $activities,
            'lesson' => $lesson
        ]);
    }


    public function agendaPresenceActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $selected_role = $request->query('role', 'none');
        $search = $request->query('search', '');
        $activity = Activity::find($id);

        if (!$activity || !$activity->presence) {
            return redirect()->route('agenda.presence')->with('error', 'Activiteit niet gevonden.');
        }

        $lessonId = $request->query('lessonId');
        $lesson = $lessonId ? Lesson::find($lessonId) : null;

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            ($lesson && $lesson->user_id === $user->id) ||
            ($lesson && $lesson->users()->where('user_id', $user->id)->wherePivot('teacher', true)->exists());

        if ($lesson && !$isTeacher) {
            return redirect()->route('agenda.presence')->with('error', 'Geen toegang tot deze activiteit.');
        }

        $activityRoleIds = $activity->roles ? array_map('trim', explode(',', $activity->roles)) : [];
        $activityUserIds = $activity->users ? array_map('trim', explode(',', $activity->users)) : [];

        $all_roles = Role::whereIn('id', $activityRoleIds)->get();

        if (!in_array($selected_role, $all_roles->pluck('role')->toArray())) {
            $selected_role = 'none';
        }

        // Fetch users by roles if roles exist
// Fetch users by roles if roles exist
        $usersWithRoles = User::whereHas('roles', function ($query) use ($activityRoleIds, $selected_role) {
            $query->whereIn('roles.id', $activityRoleIds);
            if ($selected_role !== 'none') {
                $query->where('role', $selected_role);
            }
        })->get();



        // Fetch users by specific user IDs if no roles exist
        $mentionedUsers = collect();
        if (!empty($activityUserIds)) {
            $mentionedUsers = User::whereIn('id', $activityUserIds)->get();
        }

        // Fetch users who set presence but are not explicitly listed
        $presenceUserIds = Presence::where('activity_id', $activity->id)->pluck('user_id')->toArray();
        $usersWithPresence = User::whereIn('id', $presenceUserIds)->get();

        if (isset($lesson)) {
            $users = $lesson->users()
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere('users.infix', 'like', '%' . $search . '%')
                        ->orWhere('users.last_name', 'like', '%' . $search . '%');
                })
                ->get();

            // Ensure the lesson owner is included
            $lessonOwner = User::find($lesson->user_id);
            if ($lessonOwner && !$users->contains('id', $lessonOwner->id)) {
                $users->push($lessonOwner);
            }
        } else {
            // Combine all users, ensuring uniqueness
            $users = $usersWithRoles->merge($mentionedUsers)->unique('id');
        }

// Include users with presence even if they weren't explicitly part of the activity
        $users = $users->merge($usersWithPresence)->unique('id');

// Fetch presence data for all users
        $userPresenceArray = Presence::where('activity_id', $activity->id)
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->mapWithKeys(function ($presenceRecord) {
                return [$presenceRecord->user_id => [
                    'status' => $presenceRecord->presence ? 'present' : 'absent',
                    'date' => $presenceRecord->updated_at->toDateTimeString()
                ]];
            });

// Assign presence status and determine if the user was invited
        $users->each(function ($user) use ($userPresenceArray, $usersWithRoles, $mentionedUsers, $lesson, $activity) {
            $user->presence = $userPresenceArray->get($user->id, ['status' => 'null', 'date' => null]);

            $user->not_invited = !$usersWithRoles->contains('id', $user->id) &&
                !$mentionedUsers->contains('id', $user->id) &&
                (!$lesson || !$lesson->users->contains('id', $user->id)) &&
                (!empty($activity->roles) || !empty($activity->users));
        });

// If filtering by role, remove users who are not invited
        if ($selected_role !== 'none') {
            $users = $users->filter(function ($user) {
                return !$user->not_invited;
            });
        }



// Sort users
        $sortedUsers = $users->sort(function ($a, $b) {
            $presenceOrder = ['present' => 1, 'absent' => 2, 'null' => 3];
            $aPresence = $presenceOrder[$a->presence['status']] ?? 3;
            $bPresence = $presenceOrder[$b->presence['status']] ?? 3;

            return $aPresence === $bPresence
                ? strcmp($a->last_name, $b->last_name)
                : $aPresence <=> $bPresence;
        })->values();

        return view('agenda.presence-activity', [
            'user' => $user,
            'roles' => $roles,
            'activity' => $activity,
            'users' => $sortedUsers,
            'all_roles' => $all_roles,
            'selected_role' => $selected_role,
            'search' => $search,
            'lesson' => $lesson,
        ]);

    }


    public function exportPresenceData(Request $request)
    {
        // Retrieve the filtered user data from the request
        $usersData = json_decode($request->input('users'), true);
        $activityName = $request->input('activity_name');

        // Ensure presence data is correctly mapped and sanitized
        $usersData = collect($usersData)->map(function ($user) {
            return [
                'ID' => $user['id'],
                'Name' => $user['name'],
                'Presence' => $user['presence'] ?? 'null',
                'Date' => isset($user['date']) && !empty($user['date'])
                    ? \Carbon\Carbon::parse($user['date'])->format('d-m-Y H:i')
                    : '-', // Use default '-' if no date present
            ];
        });

        // Check data structure by logging (for debugging)
        \Log::info('Users Data:', $usersData->toArray());

        // Export data to Excel
        $export = new AgendaExport($usersData->toArray(), $activityName);
        return $export->export();
    }


    public function agendaMonth(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();
        $rolesIDList = $roles->pluck('id')->toArray();
        $canViewAll = false;

        // Check if the user has one of the roles that allow them to view all activities
        if ($user->roles->contains('role', 'Dolfijnen Leiding') ||
            $user->roles->contains('role', 'Zeeverkenners Leiding') ||
            $user->roles->contains('role', 'Loodsen Stamoudste') ||
            $user->roles->contains('role', 'Afterloodsen Organisator') ||
            $user->roles->contains('role', 'Administratie') ||
            $user->roles->contains('role', 'Bestuur') ||
            $user->roles->contains('role', 'Praktijkbegeleider') ||
            $user->roles->contains('role', 'Loodsen Mentor') ||
            $user->roles->contains('role', 'Ouderraad') ||
            $user->roles->contains('role', 'Loods') ||
            $user->roles->contains('role', 'Afterloods')
        ) {
            $canViewAll = true;
        }

        $wantViewAll = filter_var($request->query('all', false), FILTER_VALIDATE_BOOLEAN);


        if ($wantViewAll === false) {
            $canViewAll = false;
        }

        $monthOffset = $request->query('month', 0);
        $dayOffset = $request->query('day', 0);

        Carbon::setLocale('nl');
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonthsNoOverflow($monthOffset);
        $calculatedDay = $calculatedDate->day;
        $calculatedMonth = $calculatedDate->month;
        $calculatedYear = $calculatedDate->year;

        $firstDayOfMonth = Carbon::create($calculatedYear, $calculatedMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $firstDayOfWeek = ($firstDayOfMonth->dayOfWeek + 6) % 7;

        $monthName = $calculatedDate->translatedFormat('F');


        // Fetch activities for the calculated month and year
        $lessonId = $request->query('lessonId');
        $lesson = Lesson::find($lessonId);

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];


        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = false;
        $isTeacher = false;
        if (isset($lesson)) {
            $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
                $lesson->user_id === $user->id ||
                $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists();
        }

        $activities = Activity::when($lessonId, function ($query) use ($lessonId, $isTeacher, $lesson) {
            // If lessonId is provided, filter by lesson_id
            return $query->where('lesson_id', $lessonId);
        })
            ->where(function ($query) use ($calculatedYear, $calculatedMonth) {
                $query->whereYear('date_start', $calculatedYear)
                    ->whereMonth('date_start', $calculatedMonth)
                    ->orWhere(function ($query) use ($calculatedYear, $calculatedMonth) {
                        $query->whereYear('date_end', $calculatedYear)
                            ->whereMonth('date_end', $calculatedMonth);
                    });
            })
            ->get()
            ->filter(function ($activity) use ($user, $rolesIDList, $canViewAll, $lesson, $isTeacher, $lessonId) {
                // Check if the activity is associated with a lesson
                if (isset($activity->lesson_id)) {
                    if (isset($activity->lesson->users)) {
                        // Fetch the lesson and its associated users
                        $lessonUsers = $activity->lesson->users->pluck('id')->toArray() ?? [];

                        if (!isset($lessonId) && !$isTeacher) {
                            // If the user is in the lesson's users, allow visibility; otherwise, hide it
                            if (in_array($user->id, $lessonUsers) || $canViewAll) {
                                return true; // User has access to the lesson
                            } else {
                                return false; // Hide the activity if the user has no access to the lesson
                            }
                        } else {
                            return true;
                        }
                    } else {
                        return false;
                    }
                } else {
                    // Check if activity has roles or users
                    $activityRoleIds = !empty($activity->roles)
                        ? array_map('trim', explode(',', $activity->roles))
                        : [];

                    $activityUserIds = !empty($activity->users)
                        ? array_map('trim', explode(',', $activity->users))
                        : [];

                    // Default behavior if both roles and users are null: available for everyone without highlighting
                    if (empty($activityRoleIds) && empty($activityUserIds)) {
                        $activity->should_highlight = false;
                        return true; // Keep activity available
                    }
                }

                // If no lesson is connected, proceed with roles and user access checks
                $hasRoleAccess = !empty(array_intersect($rolesIDList, $activityRoleIds));
                $isUserListed = in_array($user->id, $activityUserIds);

                // Check if any child has role access
                $isChildHasAccess = false;

                foreach ($user->children as $child) {
                    // Get the role IDs for the child
                    $childRoleIds = $child->roles->pluck('id')->toArray();

                    // Check if the child has access based on roles or user IDs
                    if (!empty(array_intersect($childRoleIds, $activityRoleIds)) || in_array($child->id, $activityUserIds)) {
                        $isChildHasAccess = true;
                        break; // Stop checking if any child has access
                    }
                }

                if ($canViewAll) {
                    // Highlight only if there are roles or users and the user doesn't have access
                    $activity->should_highlight = (!$hasRoleAccess && !$isUserListed);
                    return true; // Keep activity in the list
                } else {
                    // Hide activity if the user doesn't have access, but allow visibility if their child has access
                    return $hasRoleAccess || $isUserListed || $isChildHasAccess;
                }
            });


        $globalRowTracker = [];
        $activityPositions = [];

        foreach ($activities as $activity) {
            $startDate = Carbon::parse($activity->date_start)->startOfDay();
            $endDate = Carbon::parse($activity->date_end)->endOfDay();
            $position = 0; // Initialize position

            $conflictFound = true;

            // Find a non-conflicting position
            while ($conflictFound) {
                $conflictFound = !$this->trackEventPosition($activity->id, $startDate, $endDate, $position, $globalRowTracker);
                if ($conflictFound) {
                    $position++;
                }
            }

            $activityPositions[$activity->id] = $position;
        }

        return view('agenda.month', [
            'user' => $user,
            'roles' => $roles,
            'day' => $calculatedDay,
            'month' => $calculatedMonth,
            'year' => $calculatedYear,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentDay' => now()->day,
            'currentMonth' => now()->month,
            'currentYear' => now()->year,
            'monthOffset' => $monthOffset,
            'dayOffset' => $dayOffset,
            'monthName' => $monthName,
            'activities' => $activities,
            'wantViewAll' => $wantViewAll,
            'activityPositions' => $activityPositions,
            'lesson' => $lesson,
            'isTeacher' => $isTeacher
        ]);
    }


    public function agendaMonthPublic(Request $request)
    {
        $monthOffset = $request->query('month', 0);

        Carbon::setLocale('nl');
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonthsNoOverflow($monthOffset);
        $calculatedDay = $calculatedDate->day;
        $calculatedMonth = $calculatedDate->month;
        $calculatedYear = $calculatedDate->year;

        $firstDayOfMonth = Carbon::create($calculatedYear, $calculatedMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $firstDayOfWeek = ($firstDayOfMonth->dayOfWeek + 6) % 7;

        $monthName = $calculatedDate->translatedFormat('F');

        // Fetch activities for the calculated month and year
        $activities = Activity::where(function ($query) use ($calculatedYear, $calculatedMonth) {
            $query->whereYear('date_start', $calculatedYear)
                ->whereMonth('date_start', $calculatedMonth)
                ->orWhere(function ($query) use ($calculatedYear, $calculatedMonth) {
                    $query->whereYear('date_end', $calculatedYear)
                        ->whereMonth('date_end', $calculatedMonth);
                });
        })
            ->where('public', true)
            ->get();


        $globalRowTracker = [];
        $activityPositions = [];

        foreach ($activities as $activity) {
            $startDate = Carbon::parse($activity->date_start)->startOfDay();
            $endDate = Carbon::parse($activity->date_end)->endOfDay();
            $position = 0; // Initialize position

            $conflictFound = true;

            // Find a non-conflicting position
            while ($conflictFound) {
                $conflictFound = !$this->trackEventPosition($activity->id, $startDate, $endDate, $position, $globalRowTracker);
                if ($conflictFound) {
                    $position++;
                }
            }

            $activityPositions[$activity->id] = $position;
        }

        return view('agenda.public.month', [
            'day' => $calculatedDay,
            'month' => $calculatedMonth,
            'year' => $calculatedYear,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'currentDay' => now()->day,
            'currentMonth' => now()->month,
            'currentYear' => now()->year,
            'monthOffset' => $monthOffset,
            'monthName' => $monthName,
            'activities' => $activities,
            'activityPositions' => $activityPositions,
        ]);
    }

    // Function to track event positions and detect conflicts
    private function trackEventPosition($eventId, $startDate, $endDate, $position, &$globalRowTracker)
    {
        $conflictFound = false;

        for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
            $formattedDay = $day->format('Y-m-d');

            if (!isset($globalRowTracker[$formattedDay])) {
                $globalRowTracker[$formattedDay] = [];
            }

            if (isset($globalRowTracker[$formattedDay][$position])) {
                $conflictFound = true;
                break;
            }
        }

        if ($conflictFound) {
            return false;
        }

        for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
            $formattedDay = $day->format('Y-m-d');
            $globalRowTracker[$formattedDay][$position] = $eventId;
        }

        return true;
    }

    public function agendaPresent($activityId, $userId)
    {
        // Retrieve the specified user
        $user = User::findOrFail($userId);

        // Check if the user is either the authenticated user or a child of the authenticated user
        if ((int)$userId === Auth::id() || Auth::user()->children->contains('id', $userId)) {
            $presence = Presence::where('user_id', $userId)
                ->where('activity_id', $activityId)
                ->first();

            if ($presence) {
                $presence->update(['presence' => 1]);
            } else {
                Presence::create([
                    'user_id' => $userId,
                    'activity_id' => $activityId,
                    'presence' => 1,
                ]);
            }

            // Append query parameters to the redirect URL
            $queryParams = request()->query();
            $redirectUrl = route('agenda.activity', $activityId) . ($queryParams ? '?' . http_build_query($queryParams) : '');

            if ($userId === Auth::id()) {
                return redirect($redirectUrl)->with('success', 'Je bent aanwezig gemeld!');
            } else {
                return redirect($redirectUrl)->with('success', $user->name . ' ' . $user->infix . ' ' . $user->last_name . ' is aanwezig gemeld!');
            }
        } else {
            return redirect()->route('agenda.activity', $activityId)->with('error', 'Dit account kan niet aanwezig gemeld worden');
        }
    }

    public function agendaAbsent($activityId, $userId)
    {
        // Retrieve the specified user
        $user = User::findOrFail($userId);

        // Check if the user is either the authenticated user or a child of the authenticated user
        if ((int)$userId === Auth::id() || Auth::user()->children->contains('id', $userId)) {
            $presence = Presence::where('user_id', $userId)
                ->where('activity_id', $activityId)
                ->first();

            if ($presence) {
                $presence->update(['presence' => 0]);
            } else {
                Presence::create([
                    'user_id' => $userId,
                    'activity_id' => $activityId,
                    'presence' => 0,
                ]);
            }

            // Append query parameters to the redirect URL
            $queryParams = request()->query();
            $redirectUrl = route('agenda.activity', $activityId) . ($queryParams ? '?' . http_build_query($queryParams) : '');

            if ($userId === Auth::id()) {
                return redirect($redirectUrl)->with('success', 'Je bent afwezig gemeld, jammer dat je er niet bij bent!');
            } else {
                return redirect($redirectUrl)->with('success', $user->name . ' ' . $user->infix . ' ' . $user->last_name . ' is afwezig gemeld!');
            }
        } else {
            return redirect()->route('agenda.activity', $activityId)->with('error', 'Dit account kan niet afwezig gemeld worden');
        }
    }


    public function agendaSchedule(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();
        $rolesIDList = $roles->pluck('id')->toArray();
        $canViewAll = false;

        // Check if the user has one of the roles that allow them to view all activities
        if ($user->roles->contains('role', 'Dolfijnen Leiding') ||
            $user->roles->contains('role', 'Zeeverkenners Leiding') ||
            $user->roles->contains('role', 'Loodsen Stamoudste') ||
            $user->roles->contains('role', 'Afterloodsen Organisator') ||
            $user->roles->contains('role', 'Administratie') ||
            $user->roles->contains('role', 'Bestuur') ||
            $user->roles->contains('role', 'Praktijkbegeleider') ||
            $user->roles->contains('role', 'Loodsen Mentor') ||
            $user->roles->contains('role', 'Loods') ||
            $user->roles->contains('role', 'Afterloods') ||
            $user->roles->contains('role', 'Ouderraad')
        ) {
            $canViewAll = true;
        }

        $wantViewAll = filter_var($request->query('all', false), FILTER_VALIDATE_BOOLEAN);


        if ($wantViewAll === false) {
            $canViewAll = false;
        }

        // Retrieve query parameters for offsets, default to 0 if not set
        $monthOffset = $request->query('month', 0);
        $dayOffset = $request->query('day', 0);

        // Set locale to Dutch
        Carbon::setLocale('nl');

        // Calculate the date based on the offsets
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonths($monthOffset)->addDays($dayOffset);

        // Get the month name and year for display
        $monthName = $calculatedDate->translatedFormat('F');
        $calculatedYear = $calculatedDate->year;

        // Calculate the start and end date for the 3-month period
        $startDate = $calculatedDate->copy()->startOfMonth()->startOfDay();
        $endDate = $calculatedDate->copy()->addMonths(3)->endOfMonth()->endOfDay();

        // Fetch the IDs of the user's children
        $childrenIds = $user->children->pluck('id')->toArray();

        // Retrieve activities between the start of the calculated month and 3 months later
        $lessonId = $request->query('lessonId'); // Retrieve the lesson ID from the URI query parameters
        $lesson = Lesson::find($lessonId);

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = false;
        if (isset($lesson)) {
            $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
                $lesson->user_id === $user->id ||
                $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists();
        }


        $activities = Activity::when($lessonId, function ($query) use ($lessonId, $isTeacher) {
            // If lessonId is provided, filter by lesson_id
            return $query->where('lesson_id', $lessonId);
        })
            ->whereBetween('date_start', [$startDate, $endDate])
            ->orderBy('date_start')
            ->get()
            ->filter(function ($activity) use ($user, $rolesIDList, $canViewAll, $childrenIds, $isTeacher) {
                if (isset($activity->lesson_id)) {
                    if (isset($activity->lesson->users)) {
                        // Fetch the lesson and its associated users
                        $lessonUsers = $activity->lesson->users->pluck('id')->toArray() ?? [];

                        if (!isset($lessonId) && !$isTeacher) {
                            // If the user is in the lesson's users, allow visibility; otherwise, hide it
                            if (in_array($user->id, $lessonUsers) || $canViewAll) {
                                return true; // User has access to the lesson
                            } else {
                                return false; // Hide the activity if the user has no access to the lesson
                            }
                        } else {
                            return true;
                        }
                    } else {
                        return false;
                    }
                }
                // Check if activity has roles or users
                $activityRoleIds = !empty($activity->roles)
                    ? array_map('trim', explode(',', $activity->roles))
                    : [];

                $activityUserIds = !empty($activity->users)
                    ? array_map('trim', explode(',', $activity->users))
                    : [];

                // Default behavior if both roles and users are null: available for everyone without highlighting
                if (empty($activityRoleIds) && empty($activityUserIds)) {
                    $activity->should_highlight = false;
                    return true; // Keep activity available for everyone
                } else {
                    // If the user can view all, check if it should be highlighted
                    $hasRoleAccess = !empty(array_intersect($rolesIDList, $activityRoleIds));
                    $isUserListed = in_array($user->id, $activityUserIds);
                    $isChildListed = !empty(array_intersect($childrenIds, $activityUserIds)); // Check if any child is listed

                    // Check if any child has role access
                    $isChildHasAccess = false;
                    foreach ($user->children as $child) {
                        $childRoleIds = $child->roles->pluck('id')->toArray();
                        if (!empty(array_intersect($childRoleIds, $activityRoleIds)) || in_array($child->id, $activityUserIds)) {
                            $isChildHasAccess = true;
                            break; // Stop checking if any child has access
                        }
                    }

                    if ($canViewAll) {
                        // Highlight only if there are roles or users and the user doesn't have access
                        $activity->should_highlight = !$hasRoleAccess && !$isUserListed;
                        return true; // Keep activity in the list
                    } else {
                        // Hide activity if the user doesn't have access, but allow visibility if their child has access
                        return $hasRoleAccess || $isUserListed || $isChildListed || $isChildHasAccess;
                    }
                }

            });


        // Return view with activities data
        return view('agenda.schedule', [
            'activities' => $activities,
            'roles' => $roles,
            'user' => $user,
            'monthOffset' => $monthOffset,
            'monthName' => $monthName,
            'year' => $calculatedYear,
            'dayOffset' => $dayOffset,
            'wantViewAll' => $wantViewAll,
            'lesson' => $lesson,
            'isTeacher' => $isTeacher
        ]);
    }


    public function agendaSchedulePublic(Request $request)
    {
        // Retrieve query parameters for offsets, default to 0 if not set
        $monthOffset = $request->query('month', 0);

        // Retrieve the limit parameter, default to null
        $limit = $request->query('limit', null);

        // Set locale to Dutch
        Carbon::setLocale('nl');

        // Calculate the date based on the offsets
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonths($monthOffset);

        // Get the month name and year for display
        $monthName = $calculatedDate->translatedFormat('F');
        $calculatedYear = $calculatedDate->year;

        // Calculate the start and end date for the 3-month period
        $startDate = $calculatedDate->copy()->startOfMonth()->startOfDay();
        $endDate = $calculatedDate->copy()->addMonths(3)->endOfMonth()->endOfDay();

        // Retrieve activities between the start of the calculated month and 3 months later
        $query = Activity::whereBetween('date_start', [$startDate, $endDate])
            ->orderBy('date_start')
            ->where('public', true);

        // Apply limit only if it's not null
        if ($limit !== null) {
            $query->limit($limit);
        }

        $activities = $query->get();

        // Return view with activities data
        return view('agenda.public.schedule', [
            'activities' => $activities,
            'monthOffset' => $monthOffset,
            'monthName' => $monthName,
            'year' => $calculatedYear,
            'limit' => $limit
        ]);
    }


    public function agendaActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $canViewAll = true;

        // Check if the user has one of the roles that allow them to view all activities
        if ($user->roles->contains('role', 'Dolfijnen Leiding') ||
            $user->roles->contains('role', 'Zeeverkenners Leiding') ||
            $user->roles->contains('role', 'Loodsen Stamoudste') ||
            $user->roles->contains('role', 'Afterloodsen Organisator') ||
            $user->roles->contains('role', 'Administratie') ||
            $user->roles->contains('role', 'Bestuur') ||
            $user->roles->contains('role', 'Praktijkbegeleider') ||
            $user->roles->contains('role', 'Loodsen Mentor') ||
            $user->roles->contains('role', 'Ouderraad') ||
            $user->roles->contains('role', 'Loods') ||
            $user->roles->contains('role', 'Afterloods')
        ) {
            $canViewAll = true;
        } else {
            $canViewAll = false;
        }

        $wantViewAll = filter_var($request->query('all', false), FILTER_VALIDATE_BOOLEAN);


        $month = $request->query('month', '0');
        $view = $request->query('view', 'month');

        $rolesIDList = $roles->pluck('id')->toArray();

        // Fetch the activity using the provided id
        $activity = Activity::find($id);

        if (!$activity) {
            return redirect()->route('agenda.month')->with('error', 'Activiteit niet gevonden.');
        }

        // Fetch user's presence status for the activity
        $userPresence = Presence::where('user_id', $user->id)
            ->where('activity_id', $activity->id)
            ->first();
        $presenceStatus = $userPresence ? $userPresence->presence : null;

        // Retrieve activities between the start of the calculated month and 3 months later
        $lessonId = $request->query('lessonId'); // Retrieve the lesson ID from the URI query parameters
        $lesson = Lesson::find($lessonId);

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = false;
        if (isset($lesson)) {
            $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
                $lesson->user_id === $user->id ||
                $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists();
        }
        // Fetch roles and users for the activity
        $activityRoleIds = !empty($activity->roles)
            ? array_map('trim', explode(',', $activity->roles))
            : [];

        $activityUserIds = !empty($activity->users)
            ? array_map('trim', explode(',', $activity->users))
            : [];

        // Determine if the user has access to the activity
        $hasRoleAccess = !empty(array_intersect($rolesIDList, $activityRoleIds));
        $isUserListed = in_array($user->id, $activityUserIds);


        $allowedChildren = [];

        foreach ($user->children as $child) {
            // Get the role IDs for the child
            $childRoleIds = $child->roles->pluck('id')->toArray();

            // Check if the child has access based on roles or user IDs
            if (!empty(array_intersect($childRoleIds, $activityRoleIds)) || in_array($child->id, $activityUserIds)) {

                // Fetch child's presence status for the activity
                $childPresence = Presence::where('user_id', $child->id)
                    ->where('activity_id', $activity->id)
                    ->first();

                // Set a 'presence_status' attribute on the child to store their presence status
                $child->presence_status = $childPresence ? $childPresence->presence : null;

                // Add child with presence status to the allowed children array
                $allowedChildren[] = $child;
            }
        }

        // Check if the user is included directly in the activity's user_id
        $isDirectUserAccess = in_array($user->id, array_map('trim', explode(',', $activity->user_id)));

        // alloww everyone to view when there are no roles or users connected
        if (empty($activityRoleIds) && empty($activityUserIds)) {
            $isDirectUserAccess = true;
        }

        $canAlwaysView = $hasRoleAccess || $isUserListed || $isDirectUserAccess || $canViewAll;

        // Allow parents to access if they have role access, are listed, or if any child has access, or if directly added
        if (!$canAlwaysView) {
            if (count($allowedChildren) === 0) {
                // Log access attempt if denied
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'View activity', 'agenda', 'Activity item id: ' . $id, 'Gebruiker had geen toegang tot Activiteit');

                return redirect()->route('agenda.month')->with('error', 'Je hebt geen toegang tot deze activiteit.');
            }
        }

        // Return the activity view if access is granted
        return view('agenda.event', [
            'user' => $user,
            'roles' => $roles,
            'activity' => $activity,
            'presenceStatus' => $presenceStatus,
            'month' => $month,
            'wantViewAll' => $wantViewAll,
            'view' => $view,
            'allowedChildren' => $allowedChildren,
            'canAlwaysView' => $canAlwaysView,
            'lesson' => $lesson,
            'isTeacher' => $isTeacher
        ]);
    }


    public function agendaActivityPublic(Request $request, $id)
    {
        $month = $request->query('month', '0');
        $view = $request->query('view', 'month');

        // Fetch the activity with the provided id, but only if it's public
        $activity = Activity::where('id', $id)
            ->where('public', true)  // Check if the activity is public
            ->first();  // Get the first matching record or null

        // If the activity is not found or not public, set it to null
        if (!$activity) {
            $activity = null;
        }

        return view('agenda.public.event', [
            'activity' => $activity,
            'month' => $month,
            'view' => $view
        ]);
    }


    public function createAgenda(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $all_roles = Role::all();

        $lessonId = $request->query('lessonId'); // Retrieve the lesson ID from the URI query parameters
        $lesson = Lesson::find($lessonId);


        return view('agenda.add', ['user' => $user, 'roles' => $roles, 'all_roles' => $all_roles, 'lesson' => $lesson]);
    }

    public function createAgendaSave(Request $request)
    {
        // Validate the request inputs
        $validatedData = $request->validate([
            'title' => 'string|required',
            'content' => 'string|max:65535|nullable',
            'date_start' => ['date', 'required', 'before_or_equal:date_end'],
            'date_end' => ['date', 'required', 'after_or_equal:date_start'],
            'roles' => 'array|nullable',
            'users' => 'string|nullable',
            'public' => 'boolean|required',
            'presence' => 'boolean|required',
            'presence-date' => 'date|nullable',
            'price' => 'numeric|nullable',
            'location' => 'string|nullable',
            'organisator' => 'string|nullable',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000',
            'lesson_id' => 'integer|nullable',


            'form_labels' => 'nullable|array',
            'form_types' => 'nullable|array',
            'form_options' => 'nullable|array',
            'is_required' => 'nullable|array',
        ]);

        try {
            // Process image upload
            $newPictureName = null;
            if ($request->hasFile('image')) {
                $newPictureName = time() . '.' . $request->file('image')->extension();
                $destinationPath = 'files/agenda/agenda_images';
                $request->file('image')->move(public_path($destinationPath), $newPictureName);
            }

            // Handle roles and users input
            $roles = $request->input('roles') ? implode(', ', $request->input('roles')) : null;
            $users = $request->input('users') ? implode(', ', array_map('trim', array_filter(explode(',', $request->input('users'))))) : null;

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {

                $presence = $request->input('presence');

                if ($presence === "1" && $request->filled('presence-date')) {
                    $presence = $request->input('presence-date');
                }


                // Create the activity
                $activity = Activity::create([
                    'content' => $request->input('content'),
                    'price' => $request->input('price'),
                    'organisator' => $request->input('organisator'),
                    'location' => $request->input('location'),
                    'date_start' => $request->input('date_start'),
                    'date_end' => $request->input('date_end'),
                    'title' => $request->input('title'),
                    'user_id' => Auth::id(),
                    'roles' => $roles,
                    'users' => $users,
                    'image' => $newPictureName,
                    'public' => $request->input('public'),
                    'presence' => $presence,
                    'lesson_id' => $request->input('lesson_id')
                ]);

                // Log the creation of the activity
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create activity', 'agenda', 'Activity id: ' . $activity->id, '');

                // Handle form elements (if provided)
                if (isset($validatedData['form_labels'])) {
                    foreach ($validatedData['form_labels'] as $index => $label) {
                        $type = $validatedData['form_types'][$index];
                        $isRequired = isset($validatedData['is_required'][$index]);

                        $optionsString = null;
                        // If the field type is select, radio, or checkbox, save options
                        if (in_array($type, ['select', 'radio', 'checkbox']) && isset($validatedData['form_options'][$index])) {
                            $optionsString = implode(',', $validatedData['form_options'][$index]);
                        }

                        // Create form element
                        ActivityFormElement::create([
                            'option_value' => $optionsString,
                            'activity_id' => $activity->id,
                            'label' => $label,
                            'type' => $type,
                            'is_required' => $isRequired,
                        ]);
                    }

                    // Log the creation of the form elements
                    $log->createLog(auth()->user()->id, 2, 'Create activity form', 'agenda', 'Activity id: ' . $activity->id, 'Er is een inschrijfformulier aangemaakt.');
                }

                if ($request->input('lesson_id') === null) {
                    return redirect()->route('agenda.new')->with('success', 'Je agendapunt is opgeslagen!');
                } else {
                    return redirect()->route('agenda.month', ['lessonId' => $request->input('lesson_id')])->with('success', 'Je agendapunt is opgeslagen!');
                }
            } else {
                throw ValidationException::withMessages(['content' => 'Je agendapunt kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je agendapunt. Probeer het opnieuw.')->withInput();
        }
    }

    public function deleteActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lessonId = $request->query('lessonId');
        $lesson = $lessonId ? Lesson::find($lessonId) : null;

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            ($lesson && $lesson->user_id === $user->id) ||
            ($lesson && $lesson->users()
                    ->where('user_id', $user->id)
                    ->wherePivot('teacher', true)
                    ->exists());

        if (isset($lesson) && !$isTeacher) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete activity', 'activity', 'Actvity id: ' . $id, 'Activiteit bestaat niet');
            return redirect()->route('agenda.edit.activity', $id)->with('error', 'Je hebt hier geen toegang tot.');
        }

        try {
            $activity = Activity::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete activity', 'activity', 'Actvity id: ' . $id, 'Activiteit bestaat niet');
            return redirect()->route('agenda.edit.activity', $id)->with('error', 'Deze activiteit bestaat niet.');
        }
        if ($activity === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete activity', 'activity', 'Actvity id: ' . $id, 'Activiteit bestaat niet');
            return redirect()->route('agenda.edit.activity', $id)->with('error', 'Deze activiteit bestaat niet.');
        }

        Presence::where('activity_id', $activity->id)->delete();

        $activity->delete();

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Delete activity', 'activity', $activity->title, '');

        if (isset($lesson)) {
            return redirect()->route('agenda.edit', ['lessonId' => $lesson->id])->with('success', 'Je agendapunt is verwijderd');
        } else {
            return redirect()->route('agenda.edit')->with('success', 'Je activiteit is verwijderd');
        }
    }

}
