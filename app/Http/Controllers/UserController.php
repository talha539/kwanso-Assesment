<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * The index function is responsible for handling the display of a paginated list of users.
     * It fetches user data from an external API, applies filtering based on gender and search criteria,
     * and paginates the results. The filtered and paginated users are then passed to the 'welcome' view for rendering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Retrieve query parameters for filtering and pagination
        $gender = $request->query('gender'); // Filter by gender (optional)
        $page = $request->query('page', 1); // Current page number, defaults to 1
        $results = 10; // Number of users to display per page
        $search = $request->query('search', ''); // Search query for user names or emails (optional)

        // Prepare the API URL with pagination and gender filter
        $url = 'https://randomuser.me/api/?seed=foobar&results=' . $results . '&page=' . $page;

        // Fetch users from the API
        $response = Http::get($url);
        $users = $response->json()['results'];

        // Filter users by gender if the gender filter is applied
        if ($gender) {
            $users = array_filter($users, function($user) use ($gender) {
                return $user['gender'] === $gender;
            });
        }

        // Filter users based on the search query (first name, last name, or email)
        if ($search) {
            $users = array_filter($users, function($user) use ($search) {
                return stripos($user['name']['first'], $search) !== false ||
                       stripos($user['name']['last'], $search) !== false ||
                       stripos($user['email'], $search) !== false;
            });
        }

        // Assume total is static for now, as the API doesn't provide a total count
        $total = 100; // Total number of users, used for pagination (this is a static number)

        // Create a paginator instance for paginating the filtered user data
        $paginatedUsers = new LengthAwarePaginator(
            $users, // The items being paginated (filtered users)
            $total, // Total number of items (assumed static)
            $results, // Number of items per page
            $page, // Current page
            [
                'path' => url('/'), // The base URL for pagination links
                'query' => $request->query(), // Preserve query parameters for pagination links
            ]
        );

        // Pass the paginated users and filters (gender, search) to the view for rendering
        return view('welcome', compact('paginatedUsers', 'gender', 'search'));
    }
}
