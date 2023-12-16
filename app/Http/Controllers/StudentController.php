<?php

namespace App\Http\Controllers;

use App\Models\UserBookRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\book;
use App\Models\AcceptedRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;




class StudentController extends Controller
{

    public function calculateTotalFines($userId)
    {
        // Calculate the total fines for a user based on their user ID
        return AcceptedRequest::where('user_id', $userId)->sum('total_fines');
    }




    public function index(Request $request)
    {
        $query = User::where('is_admin', false);
        $fines = $request->session()->get('fines');

        if ($request->has('id_number_search')) {
            $idNumberSearch = $request->input('id_number_search');
            $query->where(function ($subquery) use ($idNumberSearch) {
                $subquery->where('id_number', 'LIKE', '%' . $idNumberSearch . '%')
                    ->orWhere('name', 'LIKE', '%' . $idNumberSearch . '%');
            });
        }

        $acceptedRequest = AcceptedRequest::where('user_id', Auth::id())->first();
        $date_pickup = $date_return = null;
        if ($acceptedRequest) {
            $date_pickup = $acceptedRequest->date_pickup;
            $date_return = $acceptedRequest->date_return;
        }

        // Join the 'chats' table to get users with existing chats
        $students = $query->leftJoin('chats', function ($join) {
            $join->on('users.id', '=', 'chats.sender_id')
                ->orWhere('users.id', '=', 'chats.receiver_id');
        })
        ->select(
            'users.id',
            'users.id_number',
            'users.name',
            'users.email',
            'users.contact',
            'users.gender',
            'users.grade_level',
            'users.is_suspended',
            'users.suspend_start_date',
            'users.suspend_end_date',
            'users.is_disabled',




            // Add other columns from the 'users' table here

            DB::raw('COUNT(chats.id) as chat_count'),
            DB::raw('MAX(chats.created_at) as latest_chat_date')
        )
        ->groupBy('users.id', 'users.id_number', 'users.name'
        , 'users.email'
        , 'users.contact'
        , 'users.gender'
        , 'users.grade_level' // Add other columns from the 'users' table here
        , 'users.is_suspended'
        , 'users.suspend_start_date'
        , 'users.suspend_end_date'
        , 'users.is_disabled')

        ->orderByDesc('latest_chat_date') // Order by the latest chat date in descending order
        ->get();

        // Calculate total fines for each student
        foreach ($students as $student) {
            // Calculate the total fines for this student using the function
            $student->totalFines = $this->calculateTotalFines($student->id);
        }

        $isAnyStudentDisabled = $students->contains('is_disabled', true);
        $isAnyStudentEnabled = $students->contains('is_disabled', false);

        return view('student', ['students' => $students, 'fines' => $fines, 'isAnyStudentDisabled' => $isAnyStudentDisabled,
        'isAnyStudentEnabled' => $isAnyStudentEnabled ])
            ->with('date_pickup', $date_pickup)
            ->with('date_return', $date_return)
            ->with('acceptedRequest', $acceptedRequest);
    }




    public function suspendAccount($id, Request $request)
    {
        $student = User::findOrFail($id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validate start and end dates as needed

        $student->suspend_start_date = $startDate;
        $student->suspend_end_date = $endDate;
        $student->is_suspended = true; // Set suspension status to true


        $student->save();

        return redirect('student')->with('success', 'Account suspended successfully.');
    }





    public function disableAccount($id)
    {
        $student = User::findOrFail($id);
        $student->is_disabled = true;
        $student->save();

        return redirect()->route('student')->with('success', 'Account disabled successfully.');
    }



    public function toggleAccountStatus($id)
    {
        $student = User::findOrFail($id);
        $student->is_disabled = !$student->is_disabled;
        $student->save();

        $message = $student->is_disabled ? 'Account disabled.' : 'Account enabled.';
        return redirect()->route('student')->with('success', $message);
    }


    public function disableAllAccounts()
    {
        // Assuming you have a User model
        User::where('is_disabled', false)
            ->whereNotIn('name', ['STAFF', 'ASSISTANT']) // Exclude specific user IDs
            ->update(['is_disabled' => true]);

        return redirect()->route('student')->with('success', 'All accounts disabled successfully.');
    }

    public function enableAllAccounts()
    {
        // Assuming you have a User model
        User::where('is_disabled', true)
            ->whereNotIn('name', ['STAFF', 'ASSISTANT']) // Exclude specific user IDs
            ->update(['is_disabled' => false]);

        return redirect()->route('student')->with('success', 'All accounts disabled successfully.');
    }





    public function requestIndex(Request $request)
    {
        // Retrieve all users with related requested books
        $usersQuery = User::with('requestedBooks');

        // Check if the "book_search" input field is provided in the request
        if ($request->has('book_search')) {
            $searchTerm = $request->input('book_search');
            // Add a where condition to filter users by the "ID Number" field
            $usersQuery->where('id_number', 'LIKE', "%$searchTerm%");
        }



        // Check if start_date and end_date are provided in the request
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->input('start_date') . ' 00:00:00';
            $endDate = $request->input('end_date') . ' 23:59:59';

            // Add a where condition to filter users by the created_at field within the date range
            $usersQuery->whereHas('requestedBooks', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('book_requests.created_at', [$startDate, $endDate]);
            });
        }

        // Get the filtered users
        $users = $usersQuery->get();

        // Calculate the total count of requests
        $totalRequests = $users->pluck('requestedBooks')->flatten()->count();

        return view('requests', compact('users', 'totalRequests'));
    }






    public function requestBook(Request $request, $id)
    {
        // Ensure the user is logged in
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if not logged in
        }

        // Get the logged-in user
        $user = Auth::user();

        // Get the book ID from the form input
        $bookId = $request->input('book_id');

        // Find the book by ID
        $book = book::findOrFail($bookId);

        // Find or create the user's book request record
        $userBookRequest = UserBookRequest::firstOrNew(['user_id' => $user->id]);
        $userBookRequest->request_count++;
        $userBookRequest->save();

        // Store the request information in the database
        $user->requestedBooks()->attach($book);

         // Mark the book as borrowed
        $book->update(['is_borrowed' => true]);

        return redirect()->route('viewBook', ['id' => $bookId])->with('success', 'Requested successfully.');
    }



}
