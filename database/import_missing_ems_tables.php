<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$dumpPath = __DIR__ . '/../neurab2l_emsDB.sql';
$sql = file_get_contents($dumpPath);

if ($sql === false) {
    fwrite(STDERR, "Unable to read {$dumpPath}\n");
    exit(1);
}

$sql = preg_replace('/^\s*--.*(?:\r?\n|$)/m', '', $sql);

$existingTables = collect(DB::select('select table_name from information_schema.tables where table_schema = database()'))
    ->map(fn ($row) => $row->TABLE_NAME ?? $row->table_name)
    ->flip();

preg_match_all('/CREATE TABLE `([^`]+)`/i', $sql, $matches);
$dumpTables = array_values(array_unique($matches[1]));
$missingTables = array_values(array_filter($dumpTables, fn ($table) => !isset($existingTables[$table])));

$statements = splitSqlStatements($sql);
$importedStatements = 0;
$dumpTableSet = array_flip($dumpTables);

DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::statement("SET SESSION SQL_MODE=''");

foreach ($statements as $statement) {
    [$phase, $table] = statementInfo($statement);

    if ($phase === 'CREATE TABLE' && in_array($table, $missingTables, true)) {
        DB::unprepared($statement);
        $importedStatements++;
    }
}

$existingTables = collect(DB::select('select table_name from information_schema.tables where table_schema = database()'))
    ->map(fn ($row) => $row->TABLE_NAME ?? $row->table_name)
    ->flip();

$insertTables = [];
foreach ($dumpTables as $table) {
    if (isset($existingTables[$table]) && tableCount($table) === 0) {
        $insertTables[] = $table;
    }
}

foreach ($statements as $statement) {
    [$phase, $table] = statementInfo($statement);

    if ($phase === 'INSERT INTO' && in_array($table, $insertTables, true)) {
        DB::unprepared($statement);
        $importedStatements++;
    }
}

$alterTables = [];
foreach ($dumpTables as $table) {
    if (isset($existingTables[$table]) && !hasPrimaryKey($table)) {
        $alterTables[] = $table;
    }
}

foreach ($statements as $statement) {
    [$phase, $table] = statementInfo($statement);

    if ($phase === 'ALTER TABLE' && in_array($table, $alterTables, true)) {
        try {
            DB::unprepared($statement);
            $importedStatements++;
        } catch (Throwable $exception) {
            echo "Skipped ALTER for {$table}: " . $exception->getMessage() . PHP_EOL;
        }
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo 'Imported missing EMS tables: ' . implode(', ', $missingTables) . PHP_EOL;
echo 'Filled empty EMS tables: ' . implode(', ', $insertTables) . PHP_EOL;
echo 'Altered EMS tables: ' . implode(', ', $alterTables) . PHP_EOL;
echo "Executed statements: {$importedStatements}" . PHP_EOL;

function statementInfo(string $statement): array
{
    $statement = trim(preg_replace('/^\/\*![\s\S]*?\*\/;?\s*/', '', ltrim($statement)) ?? $statement);

    if (preg_match('/^(CREATE TABLE|INSERT INTO|ALTER TABLE) `([^`]+)`/i', $statement, $matches)) {
        return [strtoupper($matches[1]), $matches[2]];
    }

    return [null, null];
}

function tableCount(string $table): int
{
    return (int) DB::table($table)->count();
}

function hasPrimaryKey(string $table): bool
{
    $rows = DB::select(
        'select count(*) as aggregate from information_schema.table_constraints where table_schema = database() and table_name = ? and constraint_type = ?',
        [$table, 'PRIMARY KEY']
    );

    return (int) ($rows[0]->aggregate ?? 0) > 0;
}

function splitSqlStatements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $length = strlen($sql);
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $escapeNext = false;

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $buffer .= $char;

        if ($escapeNext) {
            $escapeNext = false;
            continue;
        }

        if ($char === '\\' && ($inSingleQuote || $inDoubleQuote)) {
            $escapeNext = true;
            continue;
        }

        if ($char === "'" && !$inDoubleQuote) {
            $inSingleQuote = !$inSingleQuote;
            continue;
        }

        if ($char === '"' && !$inSingleQuote) {
            $inDoubleQuote = !$inDoubleQuote;
            continue;
        }

        if ($char === ';' && !$inSingleQuote && !$inDoubleQuote) {
            $statement = trim($buffer);
            $buffer = '';

            if ($statement !== '' && !str_starts_with($statement, '--')) {
                $statements[] = $statement;
            }
        }
    }

    $tail = trim($buffer);
    if ($tail !== '') {
        $statements[] = $tail;
    }

    return $statements;
}
