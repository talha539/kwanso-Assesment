<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About This Project

This project is built using the [Laravel framework](https://laravel.com). Laravel is a powerful PHP web application framework that provides an elegant syntax and tools for building robust web applications.

## Features

1. **User Authentication and Authorization**
   - API endpoints for user login and sign-up.
   - Mechanism for sending and resending invitations.
   - Invites expire after a set period and can be resent to reset the expiration date.
   - Sign-ups are invite-only and can only be sent by admins.

2. **Task Management**
   - CRUD operations for tasks associated with users.
   - Admins can view and manage their own tasks as well as those of other users.
   - Listing of tasks with pagination and filters based on task status and user ID.
   - Pagination is implemented using both offset-based and cursor-based methods.

3. **Frontend Features**
   - Integration with the API to fetch random user data: [Random User API](https://randomuser.me/api/).
   - Listing component with pagination, filtering by gender, and search functionality based on name and email.

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/talha539/kwanso-Assesment.git
    ```

2. Navigate to the project directory:

    ```bash
    cd your-project
    ```

3. Install the dependencies:

    ```bash
    composer install
    ```

4. Copy the `.env.example` file to `.env` and set up your environment configuration:

    ```bash
    cp .env.example .env
    ```

5. Generate the application key:

    ```bash
    php artisan key:generate
    ```

6. Run the migrations:

    ```bash
    php artisan migrate
    ```

7. Seed the database with initial admin user:

    ```bash
    php artisan db:seed --class=AdminUserSeeder
    ```

## Usage

Start the local development server:

```bash
php artisan serve
