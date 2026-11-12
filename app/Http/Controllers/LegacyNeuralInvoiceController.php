<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LegacyNeuralInvoiceController extends Controller
{
    public function __invoke(Request $request, ?string $path = null)
    {
        $path = trim($path ?: 'index.php', '/\\');
        $basePath = realpath(base_path('neuralinvoice'));
        abort_unless($basePath, 404);

        $targetPath = realpath($basePath . DIRECTORY_SEPARATOR . $path);
        if ($targetPath && is_dir($targetPath)) {
            $targetPath = realpath($targetPath . DIRECTORY_SEPARATOR . 'index.php');
        }

        abort_unless($targetPath && Str::startsWith($targetPath, $basePath) && is_file($targetPath), 404);

        if (pathinfo($targetPath, PATHINFO_EXTENSION) !== 'php') {
            return response()->file($targetPath);
        }

        if (session_status() === PHP_SESSION_NONE) {
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath) && is_writable($sessionPath)) {
                ini_set('session.save_path', $sessionPath);
            }
        }

        $previousDirectory = getcwd();
        chdir(dirname($targetPath));

        $_SERVER['SCRIPT_FILENAME'] = $targetPath;
        $_SERVER['SCRIPT_NAME'] = '/neuralinvoice/' . str_replace('\\', '/', $path);
        $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

        ob_start();
        try {
            include $targetPath;
            $content = ob_get_clean();
        } finally {
            chdir($previousDirectory ?: base_path());
        }

        return response($content);
    }
}
