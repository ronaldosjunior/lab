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

use Genpass\OptionParser;
use Genpass\PasswordGenerator;

// Criar instâncias dos objetos necessários
$optionParser = new OptionParser();
$passwordGenerator = new PasswordGenerator();

try {
    // Parsear as opções da linha de comando
    $options = $optionParser->parse($argv);
    
    // Tratar a flag --help
    if (isset($options['help'])) {
        $helpText = "Uso: php bin/generator.php [--length=N] [--no-upper] [--no-lower] [--no-digits] [--no-symbols] [--help]\n\n";
        $helpText .= "Opções:\n";
        $helpText .= "  --length=N      Tamanho da senha (8-64, padrão: 16)\n";
        $helpText .= "  --no-upper      Excluir letras maiúsculas\n";
        $helpText .= "  --no-lower      Excluir letras minúsculas\n";
        $helpText .= "  --no-digits     Excluir dígitos\n";
        $helpText .= "  --no-symbols    Excluir símbolos\n";
        $helpText .= "  --help          Mostrar esta ajuda\n";
        fwrite(STDOUT, $helpText);
        exit(0);
    }
    
    // Definir o comprimento padrão da senha
    $length = $options['length'] ?? 16;
    
    // Gerar a senha
    $password = $passwordGenerator->generate($length, $options);
    
    // Imprimir a senha
    fwrite(STDOUT, $password . PHP_EOL);
    exit(0);
} catch (\InvalidArgumentException $e) {
    // Tratar exceções de validação e mostrar erro na stderr
    fwrite(STDERR, "Erro: " . $e->getMessage() . PHP_EOL);
    exit(1);
} catch (\Exception $e) {
    // Tratar outros erros
    fwrite(STDERR, "Erro: " . $e->getMessage() . PHP_EOL);
    exit(1);
}