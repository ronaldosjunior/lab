<?php

declare(strict_types=1);

namespace Genpass;

/**
 * Gerador de senhas seguras com CSPRNG
 */
class PasswordGenerator
{
    /**
     * Gera uma senha segura com CSPRNG
     *
     * @param int $length Tamanho da senha (8-64)
     * @param array $options Opções de geração
     * @return string Senha gerada
     * @throws \InvalidArgumentException Se a entrada for inválida
     */
    public function generate(int $length, array $options = []): string
    {
        // Implementação será feita na Fase 3
        return '';
    }
}