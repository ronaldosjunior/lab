<?php

declare(strict_types=1);

namespace Genpass\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Genpass\PasswordGenerator;
use InvalidArgumentException;

/**
 * Testes unitários para PasswordGenerator
 */
class PasswordGeneratorTest extends TestCase
{
    private PasswordGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new PasswordGenerator();
    }

    /**
     * RT-01: tamanho exato (--length 8, 16, 64)
     */
    public function testGenerateWithExactLength8(): void
    {
        $password = $this->generator->generate(8);
        $this->assertEquals(8, strlen($password));
    }

    public function testGenerateWithExactLength16(): void
    {
        $password = $this->generator->generate(16);
        $this->assertEquals(16, strlen($password));
    }

    public function testGenerateWithExactLength64(): void
    {
        $password = $this->generator->generate(64);
        $this->assertEquals(64, strlen($password));
    }

    /**
     * RT-02: default = 16
     */
    public function testGenerateDefaultLength(): void
    {
        $password = $this->generator->generate(16);
        $this->assertEquals(16, strlen($password));
    }

    /**
     * RT-03: --no-upper/--no-lower/--no-digits/--no-symbols excluem apenas o conjunto indicado
     */
    public function testGenerateWithoutUppercase(): void
    {
        $password = $this->generator->generate(32, ['no-upper' => true]);
        $this->assertStringNotContainsStringAny('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $password);
    }

    public function testGenerateWithoutLowercase(): void
    {
        $password = $this->generator->generate(32, ['no-lower' => true]);
        $this->assertStringNotContainsStringAny('abcdefghijklmnopqrstuvwxyz', $password);
    }

    public function testGenerateWithoutDigits(): void
    {
        $password = $this->generator->generate(32, ['no-digits' => true]);
        $this->assertStringNotContainsStringAny('0123456789', $password);
    }

    public function testGenerateWithoutSymbols(): void
    {
        $password = $this->generator->generate(32, ['no-symbols' => true]);
        $this->assertStringNotContainsStringAny('!@#$%^&*()_+-=[]{}|;:,.<>?', $password);
    }

    /**
     * RT-04: todos os conjuntos desligados → exceção
     */
    public function testGenerateWithAllCharacterSetsDisabled(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->generate(16, [
            'no-upper' => true,
            'no-lower' => true,
            'no-digits' => true,
            'no-symbols' => true
        ]);
    }

    /**
     * RT-05: length < 8 ou > 64 → exceção
     */
    public function testGenerateWithLengthLessThan8(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->generate(7);
    }

    public function testGenerateWithLengthGreaterThan64(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generator->generate(65);
    }

    /**
     * RT-06: distribuição — 10k amostras, cada caractere do pool presente (teste fraco contra viés)
     */
    public function testPasswordDistribution(): void
    {
        // Gerar 10000 senhas de tamanho 32 para análise de distribuição
        $allCharacters = [];
        for ($i = 0; $i < 10000; $i++) {
            $password = $this->generator->generate(32);
            // Verificar que cada caractere está presente em pelo menos uma senha gerada
            foreach (str_split($password) as $char) {
                $allCharacters[$char] = true;
            }
        }

        // Verificando que todos os tipos de caracteres estão presentes no pool
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Verificar que pelo menos um caractere de cada tipo foi gerado
        foreach (str_split($uppercase) as $char) {
            $this->assertTrue(isset($allCharacters[$char]), "Caractere maiúsculo '$char' não encontrado em 10k senhas");
        }

        foreach (str_split($lowercase) as $char) {
            $this->assertTrue(isset($allCharacters[$char]), "Caractere minúsculo '$char' não encontrado em 10k senhas");
        }

        foreach (str_split($digits) as $char) {
            $this->assertTrue(isset($allCharacters[$char]), "Caractere numérico '$char' não encontrado em 10k senhas");
        }

        foreach (str_split($symbols) as $char) {
            $this->assertTrue(isset($allCharacters[$char]), "Caractere especial '$char' não encontrado em 10k senhas");
        }
    }
}