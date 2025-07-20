## Translation Management System

A lightweight, scalable API-based translation management service built using Laravel. It supports creating, retrieving,
updating, searching, and deleting translations with support for locales and tagging.

## Features

- RESTful API for managing translations

- Full CRUD support

- Use Repository Pattern for CRUD and other database queries, for clean, will organize and maintainable code structure

- Locale-based filtering

- Search by key, value, tag, or locale

- Sanctum-authenticated endpoints

- Fully Automated tests with Feature, Unit, and Performance Tests

## Setup

- git clone https://github.com/hidayat3676/tms_test.git
- cd `tms_test`
- run `composer install`
- cp `.env.example .env`
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

- for details about apis visit `api/documentation`
