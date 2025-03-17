<?php
function debug(mixed $arr)
{
    echo "<pre>" . print_r($arr, 1) . "</pre>";
}

function debugIncludedFile()
{
    $backtrace = debug_backtrace();
    foreach ($backtrace as $trace) {
        if (isset($trace['file'])) {
            echo "<div style='color: green; font-size: 28px'>Included in {$trace['file']} on line {$trace['line']}</div><br/>";
        }
    }
}

function redirect(?string $path = null, string $reason_msg = '', ?string $redirect_after = null): void
{
    if ($path) {
        $redirect = DOMAIN . $path . $redirect_after;
    } else {
        $redirect = $_SERVER['HTTP_REFERER'] ?? DOMAIN;
    }

    if (isAjax()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['redirect' => $redirect, 'message' => $reason_msg]);
        exit;
    }
    header("Location: $redirect");
    exit;
}

function isAjax(): bool
{
    return (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest');
}

