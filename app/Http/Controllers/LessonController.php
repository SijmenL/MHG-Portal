<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\LessonCompetence;
use App\Models\LessonFile;
use App\Models\LessonTest;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use App\Models\UserLessonTestResult;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LessonController extends Controller
{
    private function checkLessonAccess($lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        // Check if the user has one of the specified roles
        $hasAccessByRole = $roles->contains('role', 'Administratie') ||
            $roles->contains('role', 'Praktijkbegeleider') ||
            $roles->contains('role', 'Ouderraad') ||
            $roles->contains('role', 'Bestuur');


        // Check if the user is linked to the lesson (many-to-many relationship)
        $isLinkedToLesson = Lesson::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('id', $lessonId)->exists();

        // If any of these checks pass, allow access
        if ($hasAccessByRole || $isLinkedToLesson) {
            return true;  // User has access, proceed as normal
        } else {
            // If the user doesn't have access, redirect or show an error
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Lesson access', 'Les ' . $lessonId, $lessonId, 'gebruiker had geen toegang tot de lesomgeving');

            return false;
        }
    }

    public function lessons(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $search = $request->query('search', '');

        if ($user->roles->contains('role', 'Administratie') ||
            $user->roles->contains('role', 'Praktijkbegeleider') ||
            $user->roles->contains('role', 'Ouderraad') ||
            $user->roles->contains('role', 'Bestuur')) {

            $lessons = Lesson::when($search, function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            })
                ->orderBy('updated_at', 'desc')
                ->get();
        } else {
            $lessons = Lesson::whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->when($search, function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                })
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        return view('lessons.home', [
            'user' => $user,
            'roles' => $roles,
            'lessons' => $lessons,
            'search' => $search,
        ]);
    }


    public function lessonsNew()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        return view('lessons.new', ['user' => $user, 'roles' => $roles]);
    }

    public function lessonsNewCreate(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'title' => 'string|max:200|required',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000|nullable',
            'description' => 'string|max:65535',
            'date_start' => 'date|nullable',
            'date_end' => 'date|nullable',
        ]);

        try {
            $newPictureName = null;
            if ($request->hasFile('image')) {
                // Process image upload
                $newPictureName = time() . '.' . $request->image->extension();
                $destinationPath = 'files/lessons/lesson-images';
                $request->image->move(public_path($destinationPath), $newPictureName);
            }

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('description'))) {
                // Create the lesson
                $lesson = Lesson::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'date_start' => $request->input('date_start'),
                    'date_end' => $request->input('date_end'),
                    'user_id' => Auth::id(),
                    'image' => $newPictureName,
                ]);

                // Attach users to the lesson
                $users = $request->filled('users') ? explode(',', $request->input('users')) : [];
                $lesson->users()->sync($users);

                // Log the creation of the lesson
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create lesson', 'les', 'Lesson id: ' . $lesson->id, '');

                return redirect()->route('lessons')->with('success', 'Je lesomgeving is aangemaakt!');
            } else {
                throw ValidationException::withMessages(['content' => 'Je lesomgeving kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error('Error creating lesson: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je lesomgeving. Probeer het opnieuw.')->withInput();
        }
    }

    public function lesson($lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            $lesson->user_id === $user->id ||
            $lesson->users()
                ->where('user_id', $user->id)
                ->wherePivot('teacher', true)
                ->exists();

        $posts = Post::where('lesson_id', $lessonId)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        if ($this->checkLessonAccess($lessonId)) {
            return view('lessons.environments.lesson', ['user' => $user, 'roles' => $roles, 'lesson' => $lesson, 'posts' => $posts, 'isTeacher' => $isTeacher]);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function editLesson($lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);

        return view('lessons.environments.edit_lesson', ['user' => $user, 'roles' => $roles, 'lesson' => $lesson]);
    }

    public function editLessonStore(Request $request, $lessonId)
    {
        $request->validate([
            'title' => 'string|max:200|required',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000|nullable',
            'description' => 'string|max:65535',
            'date_start' => 'date|nullable',
            'date_end' => 'date|nullable',
        ]);

        try {
            $lesson = Lesson::findOrFail($lessonId);

            // Process image upload if a new image is provided
            $newPictureName = $lesson->image; // Default to existing image
            if ($request->hasFile('image')) {
                // If there's a new image, generate a new name and store it
                $newPictureName = time() . '.' . $request->image->extension();
                $destinationPath = 'files/lessons/lesson-images';
                $request->image->move(public_path($destinationPath), $newPictureName);

                // Optionally, delete the old image file if it exists
                if ($lesson->image && file_exists(public_path('files/lessons/lesson-images/' . $lesson->image))) {
                    unlink(public_path('files/lessons/lesson-images/' . $lesson->image)); // Delete old image
                }
            }

            // Update the lesson with new data, including the image name if changed
            $lesson->update([
                'title' => $request->title,
                'image' => $newPictureName,
                'description' => $request->description,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lesson->id, '');


            // Check if the user has access to the lesson
            if ($this->checkLessonAccess($lessonId)) {
                return redirect()->route('lessons.environment.lesson', $lessonId)->with('success', 'Les bijgewerkt.');
            } else {
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log unexpected errors
            \Log::error('Error updating lesson: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je lesomgeving. Probeer het opnieuw.')->withInput();
        }
    }


    public function deleteLesson($lessonId)
    {
        try {
            // Fetch the lesson
            $lesson = Lesson::findOrFail($lessonId);

            // Check if the user has access to the lesson before deleting
            if (!$this->checkLessonAccess($lessonId)) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Delete lesson', 'les', 'Lesson id: ' . $lesson->id, 'Gebruiker had geen toegang');
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');

            }

            // Delete all related test results
            $lesson->tests()->delete();

            // Delete all users associated with this lesson (if needed)
            $lesson->users()->detach();  // Assuming it’s a many-to-many relationship with 'users'


            // If lesson has a file associated with it, delete that file
            if ($lesson->file) {
                Storage::disk('public')->delete($lesson->file->file_path);
                // Delete the file record from the database
                $lesson->file->delete();
            }

            // Delete the lesson
            $lesson->delete();

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete lesson', 'les', 'Lesson id: ' . $lesson->id, '');

            return redirect()->route('lessons')->with('success', 'Les volledig verwijderd.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('lessons')->with('error', 'Les niet gevonden.');
        }
    }


    public function users($lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);

        // Fetch users associated with the lesson, including pivot data (teacher field)
        $users = $lesson->users()->withPivot('teacher')->get();

        // Add the creator of the lesson to the users list as a teacher if not already included
        $creator = User::find($lesson->user_id);
        if ($creator && !$users->contains($creator)) {
            $creator->pivot = (object)['teacher' => true]; // Treat the creator as a teacher
            $users->push($creator);
        }

        // Separate teachers and students
        $teachers = $users->filter(function ($user) {
            return $user->pivot->teacher; // Check the teacher flag in the pivot table
        });

        $students = $users->filter(function ($user) {
            return !$user->pivot->teacher; // Students are those without the teacher flag
        });

        // Sort teachers and students (optional: by name or role)
        $teachers = $teachers->sortBy('last_name'); // Sort teachers alphabetically by name
        $students = $students->sortBy('last_name'); // Sort students alphabetically by name

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            $lesson->user_id === $user->id ||
            $lesson->users()
                ->where('user_id', $user->id)
                ->wherePivot('teacher', true)
                ->exists();


        // Check if the user has access to the lesson
        if ($this->checkLessonAccess($lessonId)) {
            return view('lessons.environments.users', [
                'user' => $user,
                'roles' => $roles,
                'lesson' => $lesson,
                'teachers' => $teachers,
                'students' => $students,
                'isTeacher' => $isTeacher
            ]);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function updateTeachers(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        // Get the list of users to be added as teachers from the request
        $teachers = $request->filled('users') ? explode(',', $request->input('users')) : [];

        // Only keep users that are currently teachers
        $existingTeachers = $lesson->users()
            ->wherePivot('teacher', true)
            ->pluck('users.id')
            ->toArray();

        // Find the users to add and remove
        $teachersToAdd = array_diff($teachers, $existingTeachers); // Users in request but not in DB
        $teachersToRemove = array_diff($existingTeachers, $teachers); // Users in DB but not in request

        // Remove teachers not listed in the request
        $lesson->users()->detach($teachersToRemove);

        // Ensure no user is both a teacher and a student
        foreach ($teachersToAdd as $teacherId) {
            // Remove user as a student if they are being added as a teacher
            $lesson->users()->detach($teacherId);

            // Attach as a teacher
            $lesson->users()->attach($teacherId, ['teacher' => true]);
        }

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lesson->id, 'Praktijkbegeleiders aangeast');

        return redirect()->route('lessons.environment.lesson.users', $id)
            ->with('success', 'Praktijkbegeleiders succesvol bijgewerkt.');
    }

    public function updateStudents(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        // Get the list of users to be added as students from the request
        $students = $request->filled('users') ? explode(',', $request->input('users')) : [];

        // Only keep users that are currently students (not teachers)
        $existingStudents = $lesson->users()
            ->wherePivot('teacher', false)
            ->pluck('users.id')
            ->toArray();

        // Find the users to add and remove
        $studentsToAdd = array_diff($students, $existingStudents); // Users in request but not in DB
        $studentsToRemove = array_diff($existingStudents, $students); // Users in DB but not in request

        // Remove students not listed in the request
        $lesson->users()->detach($studentsToRemove);

        // Ensure no user is both a student and a teacher
        foreach ($studentsToAdd as $studentId) {
            // Remove user as a teacher if they are being added as a student
            $lesson->users()->detach($studentId);

            // Attach as a student
            $lesson->users()->attach($studentId, ['teacher' => false]);
        }

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lesson->id, 'Deelnemers aangeast');


        return redirect()->route('lessons.environment.lesson.users', $id)
            ->with('success', 'Deelnemers succesvol bijgewerkt.');
    }


    public function files(Request $request, $lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);

        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            $lesson->user_id === $user->id ||
            $lesson->users()
                ->where('user_id', $user->id)
                ->wherePivot('teacher', true)
                ->exists();

        $folderId = $request->query('folder', null);

        if ($folderId !== null) {
            $currentFolder = LessonFile::find($folderId);

            if (!isset($currentFolder) || $currentFolder->type !== 2 || $currentFolder->lesson_id !== (int)$lessonId) {
                return redirect()->route('lessons.environment.lesson.files', $lessonId)->with('error', 'Deze map bestaat niet.');
            }

            if (isset($folderId)) {
                if ($currentFolder->access === "teachers" && !$isTeacher) {
                    return redirect()->route('lessons.environment.lesson.files', $lessonId)->with('error', 'Je hebt geen toegang tot deze map.');
                }
            }
        }

        // Check if the user has access to the lesson
        if (!$this->checkLessonAccess($lessonId)) {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }

        // Retrieve all files and folders related to the lesson
        $files = $lesson->files;

        // If a folder ID is provided, filter the files for that folder
        $files = $files->where('folder_id', $folderId);


        // Breadcrumb generation
        $breadcrumbs = $this->generateBreadcrumbs($lesson, $folderId);

        // Sort files by access level and name, but folders come first
        $files = $files->sort(function ($a, $b) use ($user) {
            // If both are folders or both are files, sort alphabetically
            if ($a->type === 2 && $b->type !== 2) {
                return -1; // Folder should come first
            }
            if ($a->type !== 2 && $b->type === 2) {
                return 1; // File should come after folder
            }

            // If both are the same type (both folders or both files), sort alphabetically by file name
            return strcmp($a->file_name, $b->file_name);
        });


        return view('lessons.environments.files', [
            'user' => $user,
            'roles' => $roles,
            'lesson' => $lesson,
            'files' => $files,
            'isTeacher' => $isTeacher,
            'folderId' => $folderId,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Generate breadcrumbs based on folder hierarchy.
     */
    private function generateBreadcrumbs($lesson, $folderId)
    {
        $breadcrumbs = [];


        // Continue with the folder structure
        while ($folderId) {
            // Get the current folder by its ID
            $currentFolder = $lesson->files->where('id', $folderId)->first();

            if (!$currentFolder) {
                break; // Stop if folder is not found
            }

            // Add the current folder's name and ID to the breadcrumbs
            $breadcrumbs[] = [
                'name' => $currentFolder->file_name,
                'url' => route('lessons.environment.lesson.files', ['lessonId' => $lesson->id, 'folder' => $currentFolder->id])
            ];

            // Move to the parent folder
            $folderId = $currentFolder->folder_id;
        }

        // Reverse the order to make it from root to current folder
        $breadcrumbs = array_reverse($breadcrumbs);

        return $breadcrumbs;
    }


    // Handle the file upload
    public function filesStore(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        $request->validate([
            'type' => 'required',
            'access' => 'required|string',
        ]);

        $folderId = null;
        if (isset($request->folder_id)) {
            $folderId = $request->folder_id;
        }

        if ($request->type === "0") {
            $request->validate([
                'file' => 'required|array', // Expect an array of files
                'file.*' => 'file|mimes:pdf,jpeg,jpg,webp,png,zip,pptx,docx,doc,ppt,mp4,mov,mp3,wav,xlsx', // Validate each file
            ]);

            $files = $request->file('file'); // Get the uploaded files

            foreach ($files as $file) {
                // Get the original file name
                $originalFileName = $file->getClientOriginalName();

                // Generate a unique file name by appending a timestamp or random string
                $uniqueFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Store the file with the unique name in the desired directory
                $path = $file->storeAs('files/lessons', $uniqueFileName, 'public');

                // Create a record in the database with the file information
                $fileRecord = new LessonFile([
                    'lesson_id' => $lesson->id,
                    'user_id' => Auth::id(),
                    'file_name' => $originalFileName,
                    'file_path' => $path,
                    'access' => $request->access,
                    'folder_id' => $folderId,
                    'type' => 0
                ]);

                $fileRecord->save();
            }

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lesson->id, 'Bestand(en) geüpload');
        }
        if ($request->type === "1") {
            $request->validate([
                'file' => 'required|string', // Validate as a string (URL)
                'title' => 'required|string', // Title is required
                'access' => 'required|string', // Access is required

            ]);

            // Save the file information to the database
            $fileRecord = new LessonFile([
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'file_name' => $request->title, // Use the provided title
                'file_path' => $request->file, // Save the URL as the file path
                'access' => $request->access,
                'folder_id' => $folderId,
                'type' => 1, // URL type
            ]);

            $fileRecord->save();

            // Log the action
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lesson->id, 'Url geüpload');
        }
        if ($request->type === "2") {
            $request->validate([
                'title' => 'required|string', // Title is required
                'access' => 'required|string', // Access is required
            ]);


            // Save the file information to the database
            $fileRecord = new LessonFile([
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'file_name' => $request->title, // Use the provided title
                'file_path' => "",
                'access' => $request->access,
                'folder_id' => $folderId,
                'type' => 2,
            ]);

            $fileRecord->save();

            // Log the action
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lesson->id, 'Map aangemaakt');
        }


        return redirect()->route('lessons.environment.lesson.files', ['lessonId' => $lesson->id, 'folder' => $request->folder_id])->with('success', 'Bestanden succesvol geüpload.');
    }


    // Delete a file
    public function filesDestroy($lessonId, $fileId)
    {
        $file = LessonFile::findOrFail($fileId);

        // Ensure the user is the teacher who uploaded the file or is a teacher for the lesson
        if ($file->user_id !== Auth::id() && !$file->lesson->users()->wherePivot('teacher', true)->where('user_id', Auth::id())->exists()) {
            return redirect()->route('lessons.environment.lesson.files', $file->lesson_id)->with('error', 'Je hebt geen toestemming om dit bestand te verwijderen.');
        }

        // Recursive function to delete files in the folder
        $this->deleteFolderContents($file);

        // Delete the file from storage
        Storage::disk('public')->delete($file->file_path);

        // Delete the file record from the database
        $file->delete();

        // Log the deletion
        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Bestand verwijderd');

        return redirect()->back()->with('success', 'Bestand succesvol verwijderd.');
    }

    private function deleteFolderContents($folder)
    {
        // If the file is a folder (type 2), recursively delete its contents
        if ($folder->type == 2) {
            // Get all files inside this folder (those whose `folder_id` is this folder's ID)
            $filesInFolder = LessonFile::where('folder_id', $folder->id)->get();

            foreach ($filesInFolder as $file) {
                // If the file is a folder itself, recursively delete its contents
                $this->deleteFolderContents($file);

                // Delete the file from storage
                Storage::disk('public')->delete($file->file_path);

                // Delete the file record from the database
                $file->delete();
            }
        }
    }


    public function toggleFileAccess($lessonId, $fileId)
    {
        // Fetch the file and ensure it belongs to the specified lesson
        $file = LessonFile::where('lesson_id', $lessonId)->findOrFail($fileId);

        // Ensure the authenticated user is either the one who uploaded the file
        // or a teacher for the lesson
        $isTeacher = $file->user_id === Auth::id() ||
            $file->lesson->users()
                ->wherePivot('teacher', true)
                ->where('user_id', Auth::id())
                ->exists();

        if (!$isTeacher) {
            return redirect()->route('lessons.environment.lesson.files', $lessonId)
                ->with('error', 'Je hebt geen toestemming om dit bestand aan te passen.');
        }

        // Toggle the file's access
        $newAccess = $file->access === 'teachers' ? 'all' : 'teachers';
        $file->update(['access' => $newAccess]);

        // Log the action
        $log = new Log();
        $log->createLog(
            auth()->user()->id,
            2,
            'Update lesson',
            'les',
            'Lesson id: ' . $lessonId,
            'Bestandstoegang aangepast naar: ' . $newAccess
        );

        return redirect()->back()
            ->with('success', 'Toegang succesvol aangepast.');
    }


    public function results($lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);

        $tests = $lesson->tests()->with('testResults')->get();

        // Define the roles that classify the user as a teacher
        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];
        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            $lesson->user_id === $user->id ||
            $lesson->users()
                ->where('user_id', $user->id)
                ->wherePivot('teacher', true)
                ->exists();

        if ($this->checkLessonAccess($lessonId)) {
            return view('lessons.environments.results', ['user' => $user, 'roles' => $roles, 'lesson' => $lesson, 'tests' => $tests, 'isTeacher' => $isTeacher]);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function storeTest(Request $request, $lessonId)
    {
        try {
            $this->validate($request, [
                'title' => 'required|string|max:255',
                'date' => 'nullable|date',
                'max_points' => 'nullable|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('lessons.environment.lesson.results', [$lessonId, 'error=true']);
        }

        $lesson = Lesson::findOrFail($lessonId);
        $lesson->tests()->create($request->all());

        if ($this->checkLessonAccess($lessonId)) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Examen toegevoegd');

            return redirect()->route('lessons.environment.lesson.results', $lessonId)->with('success', 'Examen toegevoegd.');
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }


    public function storeGrades(Request $request, $testId)
    {
        $test = LessonTest::findOrFail($testId);

        $validationRules = [
            'grades' => 'required|array',
            'grades.*.user_id' => 'required|exists:users,id',
            'grades.*.score' => 'nullable|integer|min:0',
            'grades.*.feedback' => 'nullable|string|max:1000',
            'grades.*.passed' => 'nullable|boolean',
        ];

// Conditionally add the max rule if max_points is not null
        if ($test->max_points !== null) {
            $validationRules['grades.*.score'] .= '|max:' . $test->max_points;
        }

        $request->validate($validationRules);


        foreach ($request->grades as $gradeData) {
            UserLessonTestResult::updateOrCreate(
                [
                    'user_id' => $gradeData['user_id'], // Ensure this key matches the database column
                    'test_id' => $test->id, // Ensure this is the correct foreign key
                ],
                [
                    'score' => $gradeData['score'],
                    'feedback' => $gradeData['feedback'],
                    'passed' => $gradeData['passed'],
                ]
            );
        }

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $test->lesson_id, 'Resultaten toegevoegd');

        return redirect()->route('lessons.environment.lesson.results', [$test->lesson_id, 'editedtest=' . $test->id])
            ->with('success', 'Examen resultaten zijn opgeslagen.');
    }

    public function editExam($lessonId, $testId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);
        $test = LessonTest::findOrFail($testId);

        return view('lessons.environments.edit_exam', ['user' => $user, 'roles' => $roles, 'lesson' => $lesson, 'test' => $test]);
    }

    public function editExamStore(Request $request, $lessonId, $testId)
    {
        try {
            $this->validate($request, [
                'title' => 'required|string|max:255',
                'date' => 'nullable|date',
                'max_points' => 'nullable|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('lessons.environment.lesson.results.edit.exam', $lessonId);
        }

        $lesson = Lesson::findOrFail($lessonId);
        $test = $lesson->tests()->findOrFail($testId);

        // Update the test with the new data
        $test->update($request->all());

        if ($this->checkLessonAccess($lessonId)) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Examen bewerkt');

            return redirect()->route('lessons.environment.lesson.results', [$test->lesson_id, 'editedtest=' . $test->id])
                ->with('success', 'Het examen is bijgewerkt en opgeslagen.');
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function deleteExam($lessonId, $testId)
    {
        try {
            $lesson = Lesson::findOrFail($lessonId);
            $test = $lesson->tests()->findOrFail($testId);

            // Check if the user has access to the lesson before deleting
            if (!$this->checkLessonAccess($lessonId)) {
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
            }

            // Delete all related test results
            $test->testResults()->delete();

            // Delete the test
            $test->delete();

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Examen verwijderd');


            return redirect()->route('lessons.environment.lesson.results', $lessonId)->with('success', 'Examen en resultaten verwijderd.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('lessons.environment.lesson.results', [$lessonId, 'error=true'])->with('error', 'Examen niet gevonden.');
        }
    }


    public function planningOptions($lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);

        return view('lessons.environments.planning', ['user' => $user, 'roles' => $roles, 'lesson' => $lesson]);
    }

    public function competences(Request $request, $lessonId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();
        $lesson = Lesson::findOrFail($lessonId);
        $competences = $lesson->competences()->with('competenceResults')->get();

        // Define the roles that classify the user as a teacher
        $teacherRoles = ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'];

        // Check if the user is a teacher based on roles or lesson-specific permissions
        $isTeacher = $roles->whereIn('role', $teacherRoles)->isNotEmpty() ||
            $lesson->user_id === $user->id ||
            $lesson->users()
                ->where('user_id', $user->id)
                ->wherePivot('teacher', true)
                ->exists();

        // Determine which user's competences to load
        $selectedUserId = $request->query('user');

        if ($selectedUserId) {
            // If a user ID is provided, only allow teachers to view other users' competences
            if (!$isTeacher && $selectedUserId != $user->id) {
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze gegevens.');
            }

            // Find the user from the lesson's users
            $selectedUser = $lesson->users()->where('user_id', $selectedUserId)->first();
        } else {
            // If no user ID is provided, set it to the first user in the lesson's users list if the viewer is a teacher
            if ($isTeacher) {
                $selectedUser = $lesson->users()->first();
            } else {
                $selectedUser = $user; // Default to the current user
            }
        }

        // If the selected user is null (e.g., invalid ID was provided), default to the current user
        if (!$selectedUser) {
            $selectedUser = $user;
        }

        if ($this->checkLessonAccess($lessonId)) {
            return view('lessons.environments.competence', [
                'user' => $user,
                'roles' => $roles,
                'lesson' => $lesson,
                'competences' => $competences,
                'isTeacher' => $isTeacher,
                'selectedUser' => $selectedUser
            ]);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }


    public function storeCompetence(Request $request, $lessonId)
    {
        if (ForumController::validatePostData($request->input('description'))) {
            try {
                $this->validate($request, [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:65535',
                ]);
            } catch (ValidationException $e) {
                return redirect()->route('lessons.environment.lesson.competences', [$lessonId, 'error=true']);
            }

            $lesson = Lesson::findOrFail($lessonId);
            $lesson->competences()->create($request->all());

            if ($this->checkLessonAccess($lessonId)) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Competentie toegevoegd');

                return redirect()->route('lessons.environment.lesson.competences', $lessonId)->with('success', 'Competentie toegevoegd.');
            } else {
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
            }

        } else {
            throw ValidationException::withMessages(['content' => 'Je competentie kan niet opgeslagen worden.']);
        }
    }

    public function updateCompetenceResult(Request $request)
    {
        $user = Auth::user();

        // Only allow teachers to update competence results
        if (!$user->roles()->whereIn('role', ['Administratie', 'Bestuur', 'Ouderraad', 'Praktijkbegeleider'])->exists()) {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze gegevens.');
        }

        $competenceId = $request->input('competence_id');
        $userId = $request->input('user_id');
        $passed = $request->input('passed');

        $competence = LessonCompetence::findOrFail($competenceId);

        if ($passed) {
            // Create or update the competence result
            $competence->competenceResults()->updateOrCreate(
                ['user_id' => $userId],
                ['passed' => true]
            );
        } else {
            // Remove the competence result
            $competence->competenceResults()->where('user_id', $userId)->delete();
        }

        return response()->json(['message' => 'Competentie succesvol bijgewerkt']);
    }


    public function editCompetence($lessonId, $competenceId)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $lesson = Lesson::findOrFail($lessonId);
        $competence = LessonCompetence::findOrFail($competenceId);

        return view('lessons.environments.edit_competence', ['user' => $user, 'roles' => $roles, 'lesson' => $lesson, 'competence' => $competence]);
    }

    public function editCompetenceStore(Request $request, $lessonId, $competenceId)
    {
        if (ForumController::validatePostData($request->input('description'))) {
            try {
                $this->validate($request, [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:65535',
                ]);
            } catch (ValidationException $e) {
                return redirect()->route('lessons.environment.lesson.competences.edit', $lessonId);
            }

            $lesson = Lesson::findOrFail($lessonId);
            $competence = $lesson->competences()->findOrFail($competenceId);

            // Update the test with the new data
            $competence->update($request->all());

            if ($this->checkLessonAccess($lessonId)) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Competentie bewerkt');

                return redirect()->route('lessons.environment.lesson.competences', [$lessonId])
                    ->with('success', 'De competentie is bijgewerkt en opgeslagen.');
            } else {
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
            }
        } else {
            throw ValidationException::withMessages(['content' => 'Je competentie kan niet opgeslagen worden.']);
        }
    }

    public function deleteCompetence($lessonId, $competenceId)
    {
        try {
            $lesson = Lesson::findOrFail($lessonId);
            $competence = $lesson->competences()->findOrFail($competenceId);

            // Check if the user has access to the lesson before deleting
            if (!$this->checkLessonAccess($lessonId)) {
                return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
            }

            // Delete all related test results
            $competence->competenceResults()->delete();

            // Delete the test
            $competence->delete();

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Update lesson', 'les', 'Lesson id: ' . $lessonId, 'Competentie verwijderd');


            return redirect()->route('lessons.environment.lesson.competences', $lessonId)->with('success', 'Competentie en resultaten verwijderd.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('lessons.environment.lesson.competences', [$lessonId])->with('error', 'Competentie niet gevonden.');
        }
    }


    /*
     * Forum section, including posts, comments and like handling.
     */

    public function postMessage(Request $request, $lessonId)
    {
        $user = Auth::user();
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if ($this->checkLessonAccess($lessonId)) {

            if (ForumController::validatePostData($request->input('content'))) {
                $post = Post::create([
                    'content' => $request->input('content'),
                    'user_id' => Auth::id(),
                    'location' => 6,
                    'lesson_id' => $lessonId,
                ]);

                $lesson = Lesson::findOrFail($lessonId);
                $users = $lesson->users->pluck('id')->toArray(); // Extract user IDs as an array

                $notification = new Notification();
                $notification->sendNotification($user->id, $users, 'Heeft een post geplaatst in de lesomgeving!', '/lessen/omgeving/' . $lessonId . '/post/' . $post->id, 'les', 'new_post', $post->id);

                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create post', 'Les ' . $lessonId, 'Post id: ' . $post->id, '');

                return redirect()->route('lessons.environment.lesson', [$lessonId, '#post-' . $post->id]);
            } else {
                throw ValidationException::withMessages(['content' => 'Je post kan niet geplaatst worden.']);
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function viewPost($lessonId, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();
        if ($this->checkLessonAccess($lessonId)) {

            $lesson = Lesson::findOrFail($lessonId);

            try {
                $post = Post::with(['comments' => function ($query) {
                    $query->withCount('likes')
                        ->orderByDesc('likes_count')
                        ->with(['comments' => function ($query) {
                            $query->orderBy('created_at', 'asc');
                        }]);
                }])->findOrFail($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'View post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Post bestaat niet');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            if ((int)$post->lesson_id !== (int)$lessonId) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'View post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'Je mag deze post niet bekijken.');
            }

            return view('lessons.environments.post', ['user' => $user, 'post' => $post, 'roles' => $roles, 'lesson' => $lesson]);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function postComment(Request $request, $lessonId, $id)
    {
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if ($this->checkLessonAccess($lessonId)) {
            if (ForumController::validatePostData($request->input('content'))) {

                try {
                    $post = Post::findOrFail($id);
                } catch (ModelNotFoundException $exception) {
                    $log = new Log();
                    $log->createLog(auth()->user()->id, 1, 'Post comment', 'Les ' . $lessonId, 'Comment id: ' . $id, 'Post bestaat niet');

                    return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
                }

                $comment = Comment::create([
                    'content' => $request->input('content'),
                    'user_id' => Auth::id(),
                    'post_id' => $id,
                ]);

                $displayText = trim(mb_substr(strip_tags(html_entity_decode($request->input('content'))), 0, 100));

                $notification = new Notification();
                $notification->sendNotification(Auth::id(), [$post->user_id], 'Heeft een reactie geplaatst: ' . $displayText, '/lessen/omgeving/' . $lessonId . '/post/' . $post->id . '#' . $comment->id, 'les', 'new_comment', $comment->id);


                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create comment', 'Les ' . $lessonId, 'Comment id: ' . $comment->id, '');

                return redirect()->route('lessons.environment.lesson.post', [$lessonId, $id, '#comments']);
            } else {
                throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function postReaction(Request $request, $lessonId, $id, $commentId)
    {
        if ($this->checkLessonAccess($lessonId)) {
            try {
                $post = Post::findOrFail($id);
                $originalComment = Comment::findOrFail($commentId);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Post comment', 'Les ' . $lessonId, 'Comment id: ' . $id, 'Post bestaat niet');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben deze post of reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }
            $validator = Validator::make($request->all(), [
                'content' => 'required|max:65535',
            ]);

            if (ForumController::validatePostData($request->input('content'))) {
                $comment = Comment::create([
                    'content' => $request->input('content'),
                    'user_id' => Auth::id(),
                    'post_id' => $id,
                    'comment_id' => $commentId,
                ]);


                $displayText = trim(mb_substr(strip_tags(html_entity_decode($request->input('content'))), 0, 100));

                $notification = new Notification();
                $notification->sendNotification(Auth::id(), [$originalComment->user_id], 'Heeft op je gereageerd: ' . $displayText, '/lessen/omgeving/' . $lessonId . '/post/' . $post->id . '#comment-' . $comment->id, 'les', 'new_reaction_comment', $comment->id);


                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create comment', 'Les ' . $lessonId, 'Comment id: ' . $comment->id, '');

                return redirect()->route('lessons.environment.lesson.post', [$lessonId, $id, '#comment-' . $comment->id]);
            } else {
                throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function editPost($lessonId, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        if ($this->checkLessonAccess($lessonId)) {

            $lesson = Lesson::findOrFail($lessonId);

            try {
                $post = Post::findOrFail($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Edit post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Post bestaat niet');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            if ((int)$post->lesson_id !== (int)$lessonId) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Edit post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'Je mag deze post niet bekijken.');
            }

            if ($post->user_id === Auth::id()) {
                return view('lessons.environments.post_edit', ['user' => $user, 'post' => $post, 'roles' => $roles, 'lesson' => $lesson]);
            } else {
                return redirect()->route('lessons.environment.lesson', $lesson->id)->with('error', 'Je mag deze post niet bewerken.');
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function storePost(Request $request, $lessonId, $id)
    {
        if ($this->checkLessonAccess($lessonId)) {

            try {
                $post = Post::findOrFail($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Edit post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Post bestaat niet');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            if ($post->user_id === Auth::id()) {
                $validatedData = $request->validate([
                    'content' => 'string|max:65535',
                ]);

                if (ForumController::validatePostData($request->input('content'))) {
                    $log = new Log();
                    $log->createLog(auth()->user()->id, 2, 'Edit post', 'Les ' . $lessonId, 'Post id: ' . $id, '');
                    $post->update($validatedData);
                } else {
                    $log = new Log();
                    $log->createLog(auth()->user()->id, 0, 'Edit post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Post kon niet bewerkt worden');
                    throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
                }

                return redirect()->route('lessons.environment.lesson.post', [$lessonId, $id]);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Edit post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'Je mag deze post niet bewerken.');
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function deletePost($lessonId, $id)
    {
        if ($this->checkLessonAccess($lessonId)) {

            try {
                $post = Post::findOrFail($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Delete post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Post bestaat niet');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Praktijkbegeleider') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

                foreach ($post->comments as $comment) {
                    $comment->delete();
                }

                foreach ($post->likes as $like) {
                    $like->delete();
                }

                $post->delete();
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Delete post', 'Les ' . $lessonId, 'Post id: ' . $id, '');

                return redirect()->route('lessons.environment.lesson', [$lessonId, '#posts']);

            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Delete post', 'Les ' . $lessonId, 'Post id: ' . $id, 'Gebruiker mag post niet verwijderen.');
                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'Je mag deze post niet verwijderen.');
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    public function deleteComment($lessonId, $id, $postId)
    {
        if ($this->checkLessonAccess($lessonId)) {
            try {
                $comment = Comment::findOrFail($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Delete comment', 'Les ' . $lessonId, 'Comment id: ' . $id, 'Reactie bestaat niet');

                return redirect()->route('lessons.environment.lesson', $lessonId)->with('error', 'We hebben deze reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Praktijkbegeleider') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

                $comment->delete();
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Delete comment', 'Les ' . $lessonId, 'Comment id: ' . $id, '');

                return redirect()->route('lessons.environment.lesson.post', [$lessonId, $postId, '#comments']);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Delete comment', 'Les ' . $lessonId, 'Comment id: ' . $id, 'Gebruiker mag reactie niet verwijderen.');
                return redirect()->route('lessons.environment.lesson.post', $lessonId)->with('error', 'Je mag deze post niet verwijderen.');
            }
        } else {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze les.');
        }
    }

    /*
     * End of forum section
     */

}
