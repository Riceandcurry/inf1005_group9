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
        $envPath = __DIR__ . '/../.env';

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

                if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === '\'' && substr($value, -1) === '\''))) {
                    $value = substr($value, 1, -1);
                }

                $vars[$name] = $value;
                if (getenv($name) === false) {
                    putenv($name . '=' . $value);
                }
            }
        }
    }

    $value = getenv($key);
    if ($value !== false) {
        return (string)$value;
    }

    if (array_key_exists($key, $vars)) {
        return (string)$vars[$key];
    }

    return $default;
}
