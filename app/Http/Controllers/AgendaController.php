<?php

namespace App\Http\Controllers;

use App\Exports\AgendaExport;
use App\Models\Activity;
use App\Models\ActivityFormElement;
use App\Models\ActivityFormResponses;
use App\Models\Log;
use App\Models\News;
use App\Models\Presence;
use App\Models\Role;
use App\Models\User;
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
                // Exclude activities with date_start in the past when not searching
                $query->where('date_start', '>=', $currentDate);
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
            WHEN date_start >= ? THEN date_start
            ELSE NULL
        END ASC,
        date_start ASC',
                [$currentDate]
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

        $search = $request->query('search', '');
        $currentDate = now();

        $activities = Activity::query()
            ->when(empty($search), function ($query) use ($currentDate) {
                // Exclude activities with date_start in the past when not searching
                $query->where('date_start', '>=', $currentDate);
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
            ->where('user_id', Auth::id())
            ->orderByRaw(
                'CASE
            WHEN date_start >= ? THEN date_start
            ELSE NULL
        END ASC,
        date_start ASC',
                [$currentDate]
            )
            ->paginate(10);

        return view('agenda.edit', [
            'user' => $user,
            'roles' => $roles,
            'search' => $search,
            'activities' => $activities
        ]);
    }

    public function editActivity($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $all_roles = Role::all();

        $activity = Activity::with('formElements')->findOrFail($id);

        if (!$activity) {
            return redirect()->route('agenda.edit')->with('error', 'Activiteit niet gevonden.');
        }

        if ($activity->user_id !== Auth::id()) {
            return redirect()->route('agenda.edit')->with('error', 'Activiteit niet gevonden.');
        }

        return view('agenda.edit-activity', [
            'user' => $user,
            'roles' => $roles,
            'activity' => $activity,
            'all_roles' => $all_roles
        ]);
    }

    public function editActivitySave(Request $request, $id)
    {
        // Validate the request inputs
        $validatedData = $request->validate([
            'title' => 'string|required',
            'content' => 'string|max:65535|required',
            'date_start' => 'date|required',
            'date_end' => 'date|required',
            'roles' => 'array|nullable',
            'users' => 'string|nullable',
            'public' => 'boolean|required',
            'presence' => 'boolean|required',
            'price' => 'numeric|nullable',
            'location' => 'string|nullable',
            'organisator' => 'string|nullable',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000',
            'form_labels' => 'nullable|array',
            'form_types' => 'nullable|array',
            'form_options' => 'nullable|array',
            'is_required' => 'nullable|array',
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
            $users = $request->input('users') ? implode(', ', array_map('trim', array_filter(explode(',', $request->input('users'))))) : Auth::id();

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {
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
                    'presence' => $request->input('presence'),
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

                return redirect()->route('agenda.edit', $activity->id)->with('success', 'Je agendapunt is bijgewerkt!');
            } else {
                throw ValidationException::withMessages(['content' => 'Je agendapunt kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            dd($e);
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

        $currentDate = now();

        $activities = Activity::query()
            ->where('presence', 1)  // Filter by presence
            ->when(empty($search), function ($query) use ($currentDate) {
                // Exclude activities with date_start in the past when not searching
                $query->where('date_start', '>=', $currentDate);
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
                WHEN date_start >= ? THEN date_start
                ELSE NULL
            END ASC,
            date_start ASC',
                [$currentDate]
            )
            ->paginate(10);

        return view('agenda.presence', ['user' => $user, 'roles' => $roles, 'search' => $search, 'activities' => $activities]);
    }

    public function agendaPresenceActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $selected_role = $request->query('role', 'none');
        $search = $request->query('search', '');

        $activity = Activity::find($id);

        if (!$activity) {
            return redirect()->route('agenda.presence')->with('error', 'Activiteit niet gevonden.');
        }

        if (!$activity->presence) {
            return redirect()->route('agenda.presence')->with('error', 'Activiteit niet gevonden.');
        }

        $activityRoleIds = !empty($activity->roles)
            ? array_map('trim', explode(',', $activity->roles))
            : [];

        $activityUserIds = !empty($activity->users)
            ? array_map('trim', explode(',', $activity->users))
            : [];

        $all_roles = Role::whereIn('id', $activityRoleIds)->get();

        if (isset($selected_role) && !in_array($selected_role, Role::whereIn('id', $activityRoleIds)->pluck('role')->toArray())) {
            $selected_role = 'none';
        }

        if (isset($selected_role) && $selected_role !== 'none') {
            $usersWithRoles = User::whereHas('roles', function ($query) use ($selected_role) {
                $query->where('role', $selected_role);
            })
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('sex', 'like', '%' . $search . '%')
                        ->orWhere('infix', 'like', '%' . $search . '%')
                        ->orWhere('birth_date', 'like', '%' . $search . '%')
                        ->orWhere('street', 'like', '%' . $search . '%')
                        ->orWhere('postal_code', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%')
                        ->orWhere('dolfijnen_name', 'like', '%' . $search . '%');
                })
                ->orderBy('last_name', 'asc')->get();
        } else {
            if (!empty($activityRoleIds)) {
                $usersWithRoles = User::whereHas('roles', function ($query) use ($activityRoleIds) {
                    $query->whereIn('role_id', $activityRoleIds);
                })
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('sex', 'like', '%' . $search . '%')
                            ->orWhere('infix', 'like', '%' . $search . '%')
                            ->orWhere('birth_date', 'like', '%' . $search . '%')
                            ->orWhere('street', 'like', '%' . $search . '%')
                            ->orWhere('postal_code', 'like', '%' . $search . '%')
                            ->orWhere('city', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%')
                            ->orWhere('id', 'like', '%' . $search . '%')
                            ->orWhere('dolfijnen_name', 'like', '%' . $search . '%');
                    })
                    ->orderBy('last_name', 'asc')->get();
            } else {
                $usersWithRoles = User::where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('sex', 'like', '%' . $search . '%')
                        ->orWhere('infix', 'like', '%' . $search . '%')
                        ->orWhere('birth_date', 'like', '%' . $search . '%')
                        ->orWhere('street', 'like', '%' . $search . '%')
                        ->orWhere('postal_code', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%')
                        ->orWhere('dolfijnen_name', 'like', '%' . $search . '%');
                })
                    ->orderBy('last_name', 'asc')->get();
            }
        }

        $mentionedUsers = User::whereIn('id', $activityUserIds)->get();

        $users = $usersWithRoles->merge($mentionedUsers)->unique('id');

        $userPresenceArray = $users->mapWithKeys(function ($user) use ($activity) {
            $presenceRecord = Presence::where('activity_id', $activity->id)
                ->where('user_id', $user->id)
                ->first();

            $status = $presenceRecord ? ($presenceRecord->presence ? 'present' : 'absent') : 'null';

            return [$user->id => $status];
        });

        $users->each(function ($user) use ($userPresenceArray) {
            $user->presence = $userPresenceArray->get($user->id, 'null');
        });

        // Sort users by presence status and then by last name
        $sortedUsers = $users->sort(function ($a, $b) {
            $presenceOrder = ['present' => 1, 'absent' => 2, 'null' => 3];
            $aPresence = $presenceOrder[$a->presence] ?? 3;
            $bPresence = $presenceOrder[$b->presence] ?? 3;

            if ($aPresence === $bPresence) {
                return strcmp($a->last_name, $b->last_name);
            }

            return $aPresence <=> $bPresence;
        });

        return view('agenda.presence-activity', [
            'user' => $user,
            'roles' => $roles,
            'activity' => $activity,
            'users' => $sortedUsers,
            'all_roles' => $all_roles,
            'selected_role' => $selected_role,
            'search' => $search
        ]);
    }


    public function exportPresenceData(Request $request)
    {
        // Retrieve the filtered user data from the request
        $usersData = json_decode($request->input('users'), true);

        $activityName = $request->input('activity_name');

        // Export data to Excel
        $export = new AgendaExport($usersData, $activityName);
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
            $user->roles->contains('role', 'Ouderraad')
        ) {
            $canViewAll = true;
        }

        $wantViewAll = $request->query('all', 'false');


        if ($wantViewAll === 'false') {
            $canViewAll = false;
        }

        $monthOffset = $request->query('month', 0);
        $dayOffset = $request->query('day', 0);

        Carbon::setLocale('nl');
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonths($monthOffset)->addDays($dayOffset);
        $calculatedDay = $calculatedDate->day;
        $calculatedMonth = $calculatedDate->month;
        $calculatedYear = $calculatedDate->year;

        $firstDayOfMonth = Carbon::create($calculatedYear, $calculatedMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $firstDayOfWeek = ($firstDayOfMonth->dayOfWeek + 6) % 7;

        $monthName = $calculatedDate->translatedFormat('F');

        // Fetch activities for the calculated month and year
        $activities = Activity::whereYear('date_start', $calculatedYear)
            ->whereMonth('date_start', $calculatedMonth)
            ->orWhere(function ($query) use ($calculatedYear, $calculatedMonth) {
                $query->whereYear('date_end', $calculatedYear)
                    ->whereMonth('date_end', $calculatedMonth);
            })
            ->get()
            ->filter(function ($activity) use ($user, $rolesIDList, $canViewAll) {
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
                } else {
                    // If the user can view all, check if it should be highlighted
                    $hasRoleAccess = !empty(array_intersect($rolesIDList, $activityRoleIds));
                    $isUserListed = in_array($user->id, $activityUserIds);

                    if ($canViewAll) {
                        // Highlight only if there are roles or users and the user doesn't have access
                        $activity->should_highlight = !$hasRoleAccess && !$isUserListed;
                        return true; // Keep activity in the list
                    } else {
                        // Hide activity if the user doesn't have access
                        return $hasRoleAccess || $isUserListed;
                    }
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
        ]);
    }

    public function agendaMonthPublic(Request $request)
    {
        $monthOffset = $request->query('month', 0);

        Carbon::setLocale('nl');
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonths($monthOffset);
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

    public function agendaPresent($id)
    {
        $user = Auth::id();

        $presence = Presence::where('user_id', $user)
            ->where('activity_id', $id)
            ->first();

        if ($presence) {
            $presence->update(['presence' => 1]);
        } else {
            Presence::create([
                'user_id' => $user,
                'activity_id' => $id,
                'presence' => 1,
            ]);
        }

        return redirect()->route('agenda.activity', $id)->with('success', 'Je bent aanwezig gemeld!');
    }

    public function agendaAbsent($id)
    {
        $user = Auth::id();

        $presence = Presence::where('user_id', $user)
            ->where('activity_id', $id)
            ->first();

        if ($presence) {
            $presence->update(['presence' => 0]);
        } else {
            Presence::create([
                'user_id' => $user,
                'activity_id' => $id,
                'presence' => 0,
            ]);
        }

        return redirect()->route('agenda.activity', $id)->with('success', 'Je bent afwezig gemeld, jammer dat je er niet bij bent!');
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
            $user->roles->contains('role', 'Ouderraad')
        ) {
            $canViewAll = true;
        }

        $wantViewAll = $request->query('all', 'false');


        if ($wantViewAll === 'false') {
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

        // Retrieve activities between the start of the calculated month and 3 months later
        $activities = Activity::whereBetween('date_start', [$startDate, $endDate])
            ->orderBy('date_start')
            ->get()
            ->filter(function ($activity) use ($user, $rolesIDList, $canViewAll) {
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

                    if ($canViewAll) {
                        // Highlight only if there are roles or users and the user doesn't have access
                        $activity->should_highlight = !$hasRoleAccess && !$isUserListed;
                        return true; // Keep activity in the list
                    } else {
                        // Hide activity if the user doesn't have access
                        return $hasRoleAccess || $isUserListed;
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
            'wantViewAll' => $wantViewAll
        ]);
    }

    public function agendaSchedulePublic(Request $request)
    {
        // Retrieve query parameters for offsets, default to 0 if not set
        $monthOffset = $request->query('month', 0);

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
        $activities = Activity::whereBetween('date_start', [$startDate, $endDate])
            ->orderBy('date_start')
            ->where('public', true)
            ->get();

        // Return view with activities data
        return view('agenda.public.schedule', [
            'activities' => $activities,
            'monthOffset' => $monthOffset,
            'monthName' => $monthName,
            'year' => $calculatedYear,
        ]);
    }

    public function agendaActivity(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $wantViewAll = $request->query('all', 'false');
        $month = $request->query('month', '0');
        $view = $request->query('view', 'month');

        $rolesIDList = $roles->pluck('id')->toArray();

        // Fetch the activity using the provided id
        $activity = Activity::find($id);

        if (!$activity) {
            return redirect()->route('agenda.month')->with('error', 'Activiteit niet gevonden.');
        }

        $userPresence = Presence::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->first();
        $presenceStatus = $userPresence ? $userPresence->presence : null;

        if (empty($activity->roles) && empty($activity->users)) {
            return view('agenda.event', [
                'user' => $user,
                'roles' => $roles,
                'activity' => $activity,
                'presenceStatus' => $presenceStatus,

                'month' => $month,
                'wantViewAll' => $wantViewAll,
                'view' => $view
            ]);
        } else {

            if (!$user || !$user->roles->contains('role', 'Dolfijnen Leiding') && !$user->roles->contains('role', 'Zeeverkenners Leiding') && !$user->roles->contains('role', 'Loodsen Stamoudste') && !$user->roles->contains('role', 'Afterloodsen Organisator') && !$user->roles->contains('role', 'Administratie') && !$user->roles->contains('role', 'Bestuur') && !$user->roles->contains('role', 'Praktijkbegeleider') && !$user->roles->contains('role', 'Loodsen Mentor') && !$user->roles->contains('role', 'Ouderraad')
            ) {
                $activityRoleIds = !empty($activity->roles)
                    ? array_map('trim', explode(',', $activity->roles))
                    : [];

                $activityUserIds = !empty($activity->users)
                    ? array_map('trim', explode(',', $activity->users))
                    : [];

                // Check if the users has the right roles or id
                $hasRoleAccess = !empty(array_intersect($rolesIDList, $activityRoleIds));
                $isUserListed = in_array($user->id, $activityUserIds);

                if (!$hasRoleAccess && !$isUserListed) {
                    $log = new Log();
                    $log->createLog(auth()->user()->id, 1, 'View activity', 'agenda', 'Activity item id: ' . $id, 'Gebruiker had geen toegang tot Activiteit');

                    return redirect()->route('agenda.month')->with('error', 'Je hebt geen toegang tot deze activiteit.');
                }

            }


            return view('agenda.event', [
                'user' => $user,
                'roles' => $roles,
                'activity' => $activity,
                'presenceStatus' => $presenceStatus,

                'month' => $month,
                'wantViewAll' => $wantViewAll,
                'view' => $view
            ]);
        }
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


    public function createAgenda()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $all_roles = Role::all();


        return view('agenda.add', ['user' => $user, 'roles' => $roles, 'all_roles' => $all_roles]);
    }

    public function createAgendaSave(Request $request)
    {
        // Validate the request inputs
        $validatedData = $request->validate([
            'title' => 'string|required',
            'content' => 'string|max:65535|required',
            'date_start' => 'date|required',
            'date_end' => 'date|required',
            'roles' => 'array|nullable',
            'users' => 'string|nullable',
            'public' => 'boolean|required',
            'presence' => 'boolean|required',
            'price' => 'numeric|nullable',
            'location' => 'string|nullable',
            'organisator' => 'string|nullable',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000',


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
            $users = $request->input('users') ? implode(', ', array_map('trim', array_filter(explode(',', $request->input('users'))))) : Auth::id();

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {
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
                    'presence' => $request->input('presence'),
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

                return redirect()->route('agenda.new')->with('success', 'Je agendapunt is opgeslagen!');
            } else {
                throw ValidationException::withMessages(['content' => 'Je agendapunt kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je agendapunt. Probeer het opnieuw.')->withInput();
        }
    }


}
