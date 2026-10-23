<?php

declare(strict_types=1);

/**
 * bin/status.php
 *
 * Health-check via CLI. Retorna "ok" e exit(0).
 * Uso: php bin/status.php
 */

// Impede inclusão acidental em contexto web
if (PHP_SAPI !== 'cli') {
    http_response_code(503);
    fwrite(STDERR, "Forbidden: CLI only." . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "ok\n");
exit(0);

