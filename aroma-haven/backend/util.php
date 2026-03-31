<?php
    require __DIR__ . '/../vendor/autoload.php';

    // Centralize application error logging for production (PHP-FPM).
    ini_set('log_errors', '1');
    ini_set('error_log', '/var/log/php8.1-fpm.log');

    function ah_log_error(string $context, ?Throwable $exception = null, array $meta = []): void
    {
        $payload = [
            'app' => 'aroma-haven',
            'context' => $context,
            'meta' => $meta,
        ];

        if ($exception !== null) {
            $payload['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) {
            error_log('[aroma-haven] logging failure: unable to encode payload');
            return;
        }

        error_log('[aroma-haven] ' . $encoded);
    }

    function connect_db(){
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $db   = $_ENV['DB_NAME'];
        $port = $_ENV['DB_PORT'];

        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
        try {
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            ah_log_error('db_connection_failed', $e, [
                'db_host' => $host,
                'db_name' => $db,
                'db_port' => $port,
            ]);
            http_response_code(500);
            exit('Service temporarily unavailable. Please try again later.');
        }
    }
    function sanitize_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
