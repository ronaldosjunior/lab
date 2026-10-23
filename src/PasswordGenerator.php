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
        // Validação do tamanho da senha
        if ($length < 8 || $length > 64) {
            throw new \InvalidArgumentException('Erro: O comprimento da senha deve ser entre 8 e 64 caracteres.');
        }

        // Conjuntos de caracteres disponíveis
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Aplicar opções
        if (isset($options['no-upper']) && $options['no-upper']) {
            $uppercase = '';
        }
        
        if (isset($options['no-lower']) && $options['no-lower']) {
            $lowercase = '';
        }
        
        if (isset($options['no-digits']) && $options['no-digits']) {
            $digits = '';
        }
        
        if (isset($options['no-symbols']) && $options['no-symbols']) {
            $symbols = '';
        }

        // Verificar se ao menos um conjunto de caracteres está ativo
        if (empty($uppercase) && empty($lowercase) && empty($digits) && empty($symbols)) {
            throw new \InvalidArgumentException('Erro: ao menos um conjunto de caracteres deve permanecer ativo.');
        }

        // Montar o pool de caracteres disponíveis
        $pool = $uppercase . $lowercase . $digits . $symbols;

        // Gerar senha usando CSPRNG
        $password = '';
        $poolLength = strlen($pool);
        
        for ($i = 0; $i < $length; $i++) {
            // Usar random_int para evitar viés de módulo
            $randomIndex = random_int(0, $poolLength - 1);
            $password .= $pool[$randomIndex];
        }

        return $password;
    }
}