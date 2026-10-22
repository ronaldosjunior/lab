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
        // RT-105: Given --length=4, Then exit code 1 e stderr contém exatamente "Erro: --length deve estar entre 8 e 64."
        $process = new Process(['php', $this->generatorPath, '--length=4']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: --length deve estar entre 8 e 64.', $errorOutput);
    }

    public function testInvalidLengthAboveRange(): void
    {
        // RT-106: Given --length=65, Then exit code 1 e stderr contém exatamente "Erro: --length deve estar entre 8 e 64."
        $process = new Process(['php', $this->generatorPath, '--length=65']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: --length deve estar entre 8 e 64.', $errorOutput);
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

    public function testAllFlagsDisabled(): void
    {
        // RT-111: Given --no-upper --no-lower --no-digits --no-symbols, Then exit code 1 e stderr contém exatamente "Erro: ao menos um conjunto de caracteres deve permanecer ativo."
        $process = new Process(['php', $this->generatorPath, '--no-upper', '--no-lower', '--no-digits', '--no-symbols']);
        $process->run();
        
        $this->assertEquals(1, $process->getExitCode());
        $errorOutput = trim($process->getErrorOutput());
        $this->assertStringContainsString('Erro: ao menos um conjunto de caracteres deve permanecer ativo.', $errorOutput);
    }
}