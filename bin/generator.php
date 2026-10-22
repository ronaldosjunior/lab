<?php

declare(strict_types=1);

/**
 * bin/generator.php
 *
 * Gerador de senhas seguras via CLI.
 * Uso: php bin/generator.php [--length=N] [--no-upper] [--no-lower] [--no-digits] [--no-symbols] [--help]
 */

// Impede inclusão acidental em contexto web
if (PHP_SAPI !== 'cli') {
    http_response_code(503);
    exit('Forbidden: CLI only.' . PHP_EOL);
}

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Implementação será feita na Fase 3
echo "Gerador de senhas seguras\n";
exit(0);