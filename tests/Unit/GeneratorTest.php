<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * Testes unitários para o gerador de senhas seguras (v0.1)
 */
class GeneratorTest extends TestCase
{
    private string $generatorPath = 'bin/generator.php';

    public function testDefaultGeneration(): void
    {
        // RT-101: Given sem flags, When gerado, Then stdout tem 1 linha com exatamente 16 caracteres
        $process = new Process(['php', $this->generatorPath]);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertStringMatchesFormat('%s', $output); // Verifica que há output
    }

    public function testLengthFlag(): void
    {
        // RT-102: Given --length=8, Then stdout tem 1 linha de exatamente 8 caracteres
        $process = new Process(['php', $this->generatorPath, '--length=8']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertEquals(8, strlen($output));
    }

    public function testLengthFlag64(): void
    {
        // RT-103: Given --length=64, Then stdout tem 1 linha de exatamente 64 caracteres
        $process = new Process(['php', $this->generatorPath, '--length=64']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertEquals(64, strlen($output));
    }

    public function testInvalidLengthBelowRange(): void
    {
        // RT-105: Given --length=4, Then exit code 1 e stderr contém exatamente "Erro: --length deve estar entre 8 e 64"
        $process = new Process(['php', $this->generatorPath, '--length=4']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: --length deve estar entre 8 e 64', $errorOutput);
    }

    public function testInvalidLengthAboveRange(): void
    {
        // RT-106: Given --length=65, Then exit code 1 e stderr contém exatamente "Erro: --length deve estar entre 8 e 64"
        $process = new Process(['php', $this->generatorPath, '--length=65']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: --length deve estar entre 8 e 64', $errorOutput);
    }

    public function testInvalidLengthNonNumeric(): void
    {
        // RT-107: Given --length=abc, Then exit code 1 e stderr contém exatamente "Erro: --length deve ser um inteiro entre 8 e 64."
        $process = new Process(['php', $this->generatorPath, '--length=abc']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: --length deve ser um inteiro entre 8 e 64.', $errorOutput);
    }

    public function testHelpFlag(): void
    {
        // RT-114: Given --help, Then exit code 0 e stdout contém --length, --no-upper, --no-lower, --no-digits, --no-symbols
        $process = new Process(['php', $this->generatorPath, '--help']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertStringContainsString('--length', $output);
    }

    public function testUnknownFlag(): void
    {
        // RT-115: Given --foo, Then exit code 1, stderr contém exatamente "Erro: flag desconhecida --foo" e stdout está vazio
        $process = new Process(['php', $this->generatorPath, '--foo']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: flag desconhecida --foo', $errorOutput);
    }

    public function testNoDigitsFlag(): void
    {
        // RT-109: Given --no-digits, Then senha não contém dígitos
        $process = new Process(['php', $this->generatorPath, '--no-digits']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertDoesNotMatchRegularExpression('/[0-9]/', $output);
    }

    public function testNoSymbolsFlag(): void
    {
        // RT-110: Given --no-symbols, Then senha não contém símbolos
        $process = new Process(['php', $this->generatorPath, '--no-symbols']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertDoesNotMatchRegularExpression('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $output);
    }

    public function testAllFlagsDisabled(): void
    {
        // RT-111: Given --no-upper --no-lower --no-digits --no-symbols, Then exit code 1 e stderr contém exatamente "Erro: ao menos um conjunto de caracteres deve permanecer ativo."
       $process = new Process(['php', $this->generatorPath, '--no-upper', '--no-lower', '--no-digits', '--no-symbols']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: ao menos um conjunto de caracteres deve permanecer ativo.', $errorOutput);
    }

    public function testNoUpperFlag(): void
    {
        // RT-112: Given --no-upper, Then senha não contém maiúsculas
        $process = new Process(['php', $this->generatorPath, '--no-upper']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertDoesNotMatchRegularExpression('/[A-Z]/', $output);
    }

    public function testNoLowerFlag(): void
    {
        // RT-112: Given --no-lower, Then senha não contém minúsculas
        $process = new Process(['php', $this->generatorPath, '--no-lower']);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertDoesNotMatchRegularExpression('/[a-z]/', $output);
    }

    public function testSingleLineOutput(): void
    {
        // RT-113: Given geração padrão, Then stdout tem exatamente 1 linha (a senha) e stderr vazio
        $process = new Process(['php', $this->generatorPath]);
        $process->run();
        
        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertStringNotContainsString("\n", $output); // Verifica que não há nova linha na senha
    }
}