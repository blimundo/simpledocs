<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\json;

pest()->extend(Tests\TestCase::class)
    ->in('Unit', 'Feature');

/**
 * Helper function to test form request validations.
 *
 * @param  string  $method  The HTTP method (GET, POST, PUT, PATCH, DELETE).
 * @param  string  $route  The route name.
 * @param  array<string, string>  $fieldsWithDatasets  An associative array where keys are field names and values are dataset names.
 * @param  ?Closure  $routeParameters  A closure that returns an array of parameters for the route.
 */
function testFormRequestValidations(
    string $method,
    string $route,
    array $fieldsWithDatasets,
    ?Closure $routeParameters = null
): void {
    foreach ($fieldsWithDatasets as $field => $dataset) {
        it(
            "validates {$field} field",
            function (mixed $value, ?string $expectedError = null) use ($method, $route, $field, $routeParameters) {
                $user = test()->user;

                // In Pest all datasets are evaluated before running the test,
                // so we need to use a closure to delay the evaluation of route parameters.
                $routeParameters = $routeParameters ? $routeParameters() : [];

                // Prepare request parameters. If $value is an array with
                // multiple entries, use it as is. Useful for testing complex
                // validation scenarios where field dependencies exist.
                $parameters = is_array($value) && count($value) > 1 ? $value : [$field => $value];

                // In GET requests, parameters are sent as query parameters. In
                // other requests, they are sent as JSON body, but we still need
                // to send model binding parameters (like resource IDs) as part of the route.
                $response = $method === 'GET'
                    ? actingAs($user)->json('GET', route($route, [...$routeParameters, ...$parameters]))
                    : actingAs($user)->json($method, route($route, $routeParameters), $parameters);

                $expectedError
                    ? expect($response->json("errors.{$field}.0"))->toBe($expectedError)
                    : expect($response->json("errors.{$field}"))->toBeNull();
            }
        )->with($dataset);
    }
}

/**
 * Helper function to test pagination parameters.
 *
 * @param  string  $route  The route name.
 */
function testPaginationParameters(string $route): void
{
    testFormRequestValidations('GET', $route, [
        'page' => 'pagination page validation data',
        'perPage' => 'pagination per page validation data',
        'sortOrder' => 'pagination sort order validation data',
    ]);
}

/**
 * Helper function to test authentication and authorization.
 *
 * @param  string  $method  The HTTP method (GET, POST, PUT, PATCH, DELETE).
 * @param  string  $route  The route name.
 * @param  ?Closure  $routeParameters  A closure that returns an array of routeParameters for the request.
 * @param  bool  $withAuthorization  Whether to test authorization for authenticated users.
 */
function testAuthenticationAndAuthorization(string $method, string $route, ?Closure $routeParameters = null, bool $withAuthorization = true): void
{
    it('prevents unauthenticated users from accessing', function () use ($method, $route, $routeParameters) {
        $routeParameters = $routeParameters ? $routeParameters() : [];
        $response = $method === 'GET'
            ? json('GET', route($route, $routeParameters))
            : json($method, route($route, $routeParameters));

        expect($response->status())->toBe(Response::HTTP_UNAUTHORIZED);
    });

    if ($withAuthorization) {
        it('prevents unauthorized users from accessing', function () use ($method, $route, $routeParameters) {
            $routeParameters = $routeParameters ? $routeParameters() : [];
            $userWithoutPermission = User::factory()->create();

            $response = $method === 'GET'
                ? actingAs($userWithoutPermission)->json('GET', route($route, $routeParameters))
                : actingAs($userWithoutPermission)->json($method, route($route, $routeParameters));

            expect($response->status())->toBe(Response::HTTP_FORBIDDEN);
        });
    }
}
