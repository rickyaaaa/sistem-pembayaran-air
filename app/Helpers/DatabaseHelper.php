<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    /**
     * Get the correct month SQL function based on connection driver.
     *
     * @param string $column
     * @return string
     */
    public static function getMonthFunction(string $column): string
    {
        return config('database.default') === 'sqlite'
            ? "CAST(strftime('%m', $column) AS INTEGER)"
            : "MONTH($column)";
    }

    /**
     * Get the correct year SQL function based on connection driver.
     *
     * @param string $column
     * @return string
     */
    public static function getYearFunction(string $column): string
    {
        return config('database.default') === 'sqlite'
            ? "strftime('%Y', $column)"
            : "YEAR($column)";
    }
}
