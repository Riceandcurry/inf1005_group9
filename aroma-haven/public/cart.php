<?php
// Fallback endpoint for direct /cart.php visits.
// We redirect back to the previous in-site page and append #open-cart.

$fallbackTarget = 'index.php#open-cart';
$target = $fallbackTarget;
$referer = $_SERVER['HTTP_REFERER'] ?? '';

if ($referer !== '' && !preg_match('/[\r\n]/', $referer)) {
    $parts = parse_url($referer);
    if ($parts !== false) {
        $requestHost = $_SERVER['HTTP_HOST'] ?? '';
        $refererHost = $parts['host'] ?? '';
        $isSameHost = ($refererHost === '') || (strcasecmp($refererHost, $requestHost) === 0);

        if ($isSameHost) {
            $path = $parts['path'] ?? '';
            if ($path !== '') {
                $query = isset($parts['query']) ? ('?' . $parts['query']) : '';
                $target = $path . $query . '#open-cart';
            }
        }
    }
}

header('Location: ' . $target);
exit;
