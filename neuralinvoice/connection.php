<?php
// session_start();
// Database configuration is inherited from the parent Laravel project.
function neuralinvoice_env(string $key, ?string $default = null): ?string
{
    static $env = null;

    if ($env === null) {
        $env = [];
        $envPath = dirname(__DIR__) . '/.env';

        if (is_file($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$name, $value] = explode('=', $line, 2);
                $env[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }
    }

    return $_ENV[$key] ?? getenv($key) ?: ($env[$key] ?? $default);
}

$host = neuralinvoice_env('DB_HOST', '127.0.0.1');
$username = neuralinvoice_env('DB_USERNAME', 'root');
$password = neuralinvoice_env('DB_PASSWORD', '');
$database = neuralinvoice_env('DB_DATABASE', 'neurab2l_emsDB');
// Establish a connection to the database
$con = new mysqli($host, $username, $password, $database);

// Check the connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

require_once __DIR__ . '/includes/schema-bootstrap.php';
ni_ensure_database_schema($con);
