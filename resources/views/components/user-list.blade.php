<div>
    @if($paginatedUsers->count())
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Picture</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paginatedUsers as $user)
            <tr class="user-item" data-user="{{ json_encode($user) }}">
                    <td><img src="{{ $user['picture']['thumbnail'] }}" alt="User Picture"></td>
                    <td>{{ $user['name']['first'] }} {{ $user['name']['last'] }}</td>
                    <td>{{ ucfirst($user['gender']) }}</td>
                    <td>{{ $user['email'] }}</td>
                    <td>{{ $user['location']['city'] }}, {{ $user['location']['country'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No users found.</p>
    @endif
</div>