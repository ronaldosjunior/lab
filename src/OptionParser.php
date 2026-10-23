<?php

declare(strict_types=1);

namespace Genpass;

/**
 * Parser de opções da linha de comando
 */
class OptionParser
{
    /**
     * Parseia as flags da linha de comando
     *
     * @param array $argv Argumentos da linha de comando
     * @return array Opções parseadas
     * @throws \InvalidArgumentException Se houver erro na análise
     */
    public function parse(array $argv): array
    {
        $options = [];
        
        // Ignora o primeiro argumento (nome do script)
        array_shift($argv);
        
        foreach ($argv as $arg) {
            if (strpos($arg, '--') !== 0) {
                throw new \InvalidArgumentException("Flag desconhecida: {$arg}");
            }
            
            $flag = substr($arg, 2); // Remove o "--"
            
            // Trata flags com valor (ex: --length=16)
            if (strpos($flag, '=') !== false) {
                [$key, $value] = explode('=', $flag, 2);
                
                switch ($key) {
                    case 'length':
                        if (!is_numeric($value)) {
                            throw new \InvalidArgumentException("Valor inválido para --length: {$value}");
                        }
                        $length = (int)$value;
                        if ($length < 8 || $length > 64) {
                            throw new \InvalidArgumentException("--length deve estar entre 8 e 64, recebido: {$length}");
                        }
                        $options['length'] = $length;
                        break;
                    default:
                        throw new \InvalidArgumentException("Flag desconhecida: --{$key}");
                }
            } else {
                // Trata flags sem valor (ex: --help, --no-symbols)
                switch ($flag) {
                    case 'help':
                        $options['help'] = true;
                        break;
                    case 'no-symbols':
                        $options['no-symbols'] = true;
                        break;
                    case 'no-lower':
                        $options['no-lower'] = true;
                        break;
                    case 'no-upper':
                        $options['no-upper'] = true;
                        break;
                    case 'no-digits':
                        $options['no-digits'] = true;
                        break;
                    default:
                        throw new \InvalidArgumentException("Flag desconhecida: --{$flag}");
                }
            }
        }
        
        return $options;
    }
}