<?php

namespace App\Http\Controllers;

use App\Models\CalendarItem;
use App\Models\Log;
use App\Models\News;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use MailerSend\Common\Roles;

class AgendaController extends Controller
{
    public function agendaMonth(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        // Retrieve query parameters for offsets, default to 0 if not set
        $monthOffset = $request->query('month', 0);
        $dayOffset = $request->query('day', 0);

        // Set locale to Dutch
        Carbon::setLocale('nl');

        // Calculate the date based on the offsets
        $baseDate = Carbon::now();
        $calculatedDate = $baseDate->copy()->addMonths($monthOffset)->addDays($dayOffset);

        // Extract day, month, and year from the calculated date
        $calculatedDay = $calculatedDate->day;
        $calculatedMonth = $calculatedDate->month;
        $calculatedYear = $calculatedDate->year;

        // Create a Carbon instance for the first day of the calculated month and year
        $firstDayOfMonth = Carbon::create($calculatedYear, $calculatedMonth, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $firstDayOfWeek = $firstDayOfMonth->dayOfWeek;

        // Adjust for weeks starting on Monday (0 = Monday, 6 = Sunday)
        $firstDayOfWeek = ($firstDayOfWeek + 6) % 7;

        // Get the month name in Dutch
        $monthName = $calculatedDate->translatedFormat('F');

        $events = CalendarItem::whereYear('date_start', $calculatedYear)
            ->whereMonth('date_start', $calculatedMonth)
            ->orWhere(function ($query) use ($calculatedYear, $calculatedMonth) {
                $query->whereYear('date_end', $calculatedYear)
                    ->whereMonth('date_end', $calculatedMonth);
            })
            ->get();

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
            'events' => $events,
        ]);
    }

    public function agendaSchedule(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

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

        // Retrieve events between the start of the calculated month and 3 months later
        $events = CalendarItem::whereBetween('date_start', [$startDate, $endDate])
            ->orderBy('date_start')
            ->get();

        // Return view with events data
        return view('agenda.schedule', [
            'events' => $events,
            'roles' => $roles,
            'user' => $user,
            'monthOffset' => $monthOffset,
            'monthName' => $monthName,
            'year' => $calculatedYear,
            'dayOffset' => $dayOffset
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
        $request->validate([
            'title' => 'string|required',
            'content' => 'string|max:65535|required',
            'date_start' => 'date|required',
            'date_end' => 'date|required',
            'roles' => 'array',
            'users' => 'string|nullable',
            'public' => 'boolean|required',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000',
        ]);

        try {
            if (null !== $request->input('content') && $request->hasFile('image')) {
                // Process image upload
                $newPictureName = time() . '.' . $request->file('image')->extension();
                $destinationPath = 'files/agenda/agenda_images';
                $request->file('image')->move(public_path($destinationPath), $newPictureName);
            } else {
                $newPictureName = null;
            }


            if ($request->input('roles') !== null) {
                $role = $request->input('roles');
                $roles = implode(', ', $role);
            } else {
                $roles = null;
            }

            if ($request->input('users') !== null) {
                $userInput = $request->input('users');

                // Check if userInput is a string and convert it to an array
                if (is_string($userInput)) {
                    $user = explode(',', $userInput);
                } else {
                    $user = $userInput;
                }

                // Trim whitespace and remove empty values
                $user = array_map('trim', $user);
                $user = array_filter($user);

                $users = implode(', ', $user);
            } else {
                $users = Auth::id();
            }

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {

                // Create the news item
                $agenda = CalendarItem::create([
                    'content' => $request->input('content'),
                    'date_start' => $request->input('date_start'),
                    'date_end' => $request->input('date_end'),
                    'title' => $request->input('title'),
                    'user_id' => Auth::id(),
                    'roles' => $roles,
                    'users' => $users,
                    'image' => $newPictureName,
                    'public' => $request->input('public'),
                ]);

                // Log the creation of the news item
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create agenda', 'agenda', 'Agenda item id: ' . $agenda->id, '');

                return redirect()->route('agenda.new')->with('success', 'Je agendapunt is opgeslagen!.');
            } else {
                throw ValidationException::withMessages(['content' => 'Je agendapunt kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // General exception handling for unexpected errors
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je agendapunt. Probeer het opnieuw.')->withInput();
        }
    }


}
