<?php

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\CreatesApplication;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class)->in('Feature');

expect()->extend(
    'toBeOne',
    function () {
        return $this->toBe(1);
    }
);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/*function something(): void
{
    // ..
}*/

TestResponse::macro('assertApiResponseStructure', function () {
    return $this->assertJsonStructure([
        'status',
        'message',
        'data',
        'errors',
        'statusCode'
    ]);
});
