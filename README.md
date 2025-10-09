## Translation Management System

A lightweight, scalable API-based translation management service built using Laravel. It supports creating, retrieving,
updating, searching, and deleting translations with support for locales and tagging.

##Test Environment

-Require `php 8.2 or higher`
- based on laravel `12`
- wan\'t work with sqlite is it does not support full text index which are used.
## Features

- RESTful API for managing translations

- Full CRUD support

- Use SOLID design principle, for clean, will organize and maintainable code structure

- Locale-based filtering

- Search by key, value, tag, or locale

- Sanctum-authenticated endpoints

- Fully Automated tests with Feature, Unit, and Performance Tests
- Test login Cred:
  - Email: test@example.com
  - password: test12345

## Setup

- git clone https://github.com/hidayat3676/tms_test.git
- cd `tms_test`
- run `composer install`
- cp `.env.example .env`
- Add Database credentials to .env
- run `php artisan key:generate` if key is not generated
- run `php artisan migrate --seed`
- Use credentials just seeded email: `test@example.com` password: `test12345`

## Test case

- To Run test cases for unit tests run:  `php artisan test --testsuite=Unit`
- For features and performance tests run: `php artisan test --testsuite=Feature`
- To run all tests at once run: `php artisan test`

## Note:

<p>When you run test case it refresh database so all persistent data will be loss.</p>

## Api's
- run `php artisan l5-swagger:generate` to generate api docs
- then visit `api/documentation` for details about apis 
