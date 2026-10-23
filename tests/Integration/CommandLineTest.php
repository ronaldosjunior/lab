<?php

declare(strict_types=1);

namespace Genpass\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * Testes de integração para a linha de comando do gerador de senhas
 */
class CommandLineTest extends TestCase
{
    private string $binaryPath;

    protected function setUp(): void
    {
        $this->binaryPath = __DIR__ . '/../../bin/generator.php';
        $this->assertFileExists($this->binaryPath);
    }

    /**
     * RT-07: --help deve exibir ajuda e retornar exit code 0
     */
    public function testHelpFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--help']);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $this->assertStringContainsString('Uso:', $process->getOutput());
        $this->assertStringContainsString('--length=', $process->getOutput());
        $this->assertStringContainsString('--no-upper', $process->getOutput());
        $this->assertStringContainsString('--no-lower', $process->getOutput());
        $this->assertStringContainsString('--no-digits', $process->getOutput());
        $this->assertStringContainsString('--no-symbols', $process->getOutput());
    }

    /**
     * RT-07: flags válidas, --length=abc → erro, flag desconhecida → erro
     */
    public function testInvalidLengthFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=abc']);
        $process->run();

        $this->assertEquals(1, $process->getExitCode());
        $this->assertStringContainsString('Erro:', $process->getErrorOutput());
    }

    public function testUnknownFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--foo']);
        $process->run();

        $this->assertEquals(1, $process->getExitCode());
        $this->assertStringContainsString('Erro:', $process->getErrorOutput());
    }

    /**
     * RT-07: --length com valor fora do intervalo 8-64 → erro
     */
    public function testLengthBelowRange(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=4']);
        $process->run();

        $this->assertEquals(1, $process->getExitCode());
        $this->assertStringContainsString('Erro:', $process->getErrorOutput());
    }

    public function testLengthAboveRange(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=65']);
        $process->run();

        $this->assertEquals(1, $process->getExitCode());
        $this->assertStringContainsString('Erro:', $process->getErrorOutput());
    }

    /**
     * Teste de execução normal com diferentes tamanhos
     */
    public function testNormalExecution(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=16']);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        $this->assertStringNotContainsString('Erro:', $output);
        $this->assertEquals(16, strlen($output));
    }

    /**
     * Teste com flag --no-* para verificar que os caracteres são excluídos
     */
    public function testNoUpperFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=32', '--no-upper']);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        
        // Verificar que não há caracteres maiúsculos
        foreach (str_split($output) as $char) {
            $this->assertNotContains($char, str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
        }
    }

    public function testNoLowerFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=32', '--no-lower']);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        
        // Verificar que não há caracteres minúsculos
        foreach (str_split($output) as $char) {
            $this->assertNotContains($char, str_split('abcdefghijklmnopqrstuvwxyz'));
        }
    }

    public function testNoDigitsFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=32', '--no-digits']);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        
        // Verificar que não há dígitos
        foreach (str_split($output) as $char) {
            $this->assertNotContains($char, str_split('0123456789'));
        }
    }

    public function testNoSymbolsFlag(): void
    {
        $process = new Process(['php', $this->binaryPath, '--length=32', '--no-symbols']);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $output = trim($process->getOutput());
        
        // Verificar que não há símbolos
        foreach (str_split($output) as $char) {
            $this->assertNotContains($char, str_split('!@#$%^&*()_+-=[]{}|;:,.<>?'));
        }
    }
}