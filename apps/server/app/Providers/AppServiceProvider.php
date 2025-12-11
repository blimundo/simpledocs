<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());

        Model::unguard();
        Model::shouldBeStrict(app()->isLocal());

        Date::use(CarbonImmutable::class);

        $this->registerQueryMacros();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    public function registerQueryMacros(): void
    {
        $macro = function (string $column, string $value): QueryBuilder|EloquentBuilder {
            if (DB::getDriverName() === 'pgsql') {
                /**
                 * @var EloquentBuilder<Model>
                 *
                 * @phpstan-ignore method.notFound
                 */
                return $this->where($column, 'ILIKE', "%{$value}%");    // @codeCoverageIgnore
            }

            /**
             * @var QueryBuilder
             *
             * @phpstan-ignore method.notFound
             */
            return $this->whereRaw("LOWER($column) LIKE LOWER(?)", ["%{$value}%"]);
        };

        QueryBuilder::macro('whereLikeInsensitive', $macro);
        EloquentBuilder::macro('whereLikeInsensitive', $macro);
    }
}
