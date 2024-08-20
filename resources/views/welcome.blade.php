@extends('layouts.app')

@section('content')
    <h1>User Listing</h1>

    <!-- Filter by Gender -->
    <form method="GET" action="{{ url('/') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="gender">Filter by Gender:</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">All</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <!-- Search by Name or Email -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email">
                </div>
            </div>

            <div class="col-md-4 mt-4">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <!-- User List -->
    <x-user-list :paginatedUsers="$paginatedUsers" />

    <!-- Pagination Links -->
    {{ $paginatedUsers->links() }}

    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-background">
                <div class="modal-content-wrapper">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container mt-5">
                            <div class="row">
                                <div class="line-left"></div>
                                <div class="line-right"></div>
                                <div class="col-md-12 text-center">
                                    <img src="" id="userImage" class="profile-img" alt="Profile Picture">
                                    <h6 class="mt-3">Hi, My name is</h6>
                                    <h3 id="userName" style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;"></h3>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <div class="social-icons">
                                        <a href="#">
                                            <i class="fas fa-user-circle"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fas fa-phone-alt"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
