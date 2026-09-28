<?php

namespace Webkul\Support\Database\Dialects;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SqliteDialect implements DatabaseDialect
{
    public function jsonArrayAgg(string $column): string
    {
        return "json_group_array({$column})";
    }

    public function monthBucket(string $column): string
    {
        return "strftime('%Y-%m', {$column})";
    }

    public function alterColumnType(string $table, string $column, string $blueprintMethod, string $postgresType, string $postgresUsing): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($column, $blueprintMethod) {
            $blueprint->{$blueprintMethod}($column)->change();
        });
    }

    public function syncSequences(): void
    {
        //
    }

    public function caseInsensitiveEquals(string $column): string
    {
        return "{$column} = ?";
    }
}
