<?php

/**
 * Lightweight .env loader with process env fallback.
 */
function ah_env(string $key, ?string $default = null): ?string
{
    static $loaded = false;
    static $vars = [];

    if (!$loaded) {
        $loaded = true;

        $paths = [
            __DIR__ . '/../.env',
            dirname(__DIR__) . '/.env',
            getcwd() . '/.env',
            (isset($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/.env' : ''),
            (isset($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/../.env' : ''),
        ];

        $checked = [];
        foreach ($paths as $envPath) {
            if (!is_string($envPath) || $envPath === '') {
                continue;
            }

            $normalizedPath = str_replace('\\', '/', $envPath);
            if (isset($checked[$normalizedPath])) {
                continue;
            }
            $checked[$normalizedPath] = true;

            if (is_readable($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || strpos($line, '#') === 0) {
                        continue;
                    }

                    $parts = explode('=', $line, 2);
                    if (count($parts) !== 2) {
                        continue;
                    }

                    $name = trim($parts[0]);
                    $value = trim($parts[1]);

                    if (stripos($name, 'export ') === 0) {
                        $name = trim(substr($name, 7));
                    }

                    if (!preg_match('/^[A-Z0-9_]+$/i', $name)) {
                        continue;
                    }

                    if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === '\'' && substr($value, -1) === '\''))) {
                        $value = substr($value, 1, -1);
                    }

                    if (strpos($value, '#') !== false) {
                        $parts = preg_split('/\s+#/', $value, 2);
                        $value = trim((string) ($parts[0] ?? $value));
                    }

                    if (isset($vars[$name]) && $vars[$name] !== '' && $value === '') {
                        continue;
                    }

                    $vars[$name] = $value;
                    $existing = getenv($name);
                    if ($existing === false || $existing === '') {
                        putenv($name . '=' . $value);
                    }
                }

                break;
            }
        }
    }

    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return (string)$value;
    }

    if (array_key_exists($key, $vars)) {
        return (string)$vars[$key];
    }

    return $default;
}

function ah_env_source_path(): string
{
    ah_env('__AH_ENV_BOOTSTRAP__', null);

    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $paths = [
        __DIR__ . '/../.env',
        dirname(__DIR__) . '/.env',
        getcwd() . '/.env',
        (isset($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/.env' : ''),
        (isset($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/../.env' : ''),
    ];

    foreach ($paths as $envPath) {
        if (is_string($envPath) && $envPath !== '' && is_readable($envPath)) {
            $cached = $envPath;
            return $cached;
        }
    }

    $cached = '';
    return $cached;
}
