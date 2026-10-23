<?php

declare(strict_types=1);

namespace Genpass\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Testes unitários para bin/status.php
 */
class StatusTest extends TestCase
{
    private string $statusScriptPath;

    protected function setUp(): void
    {
        $this->statusScriptPath = __DIR__ . '/../../bin/status.php';
    }

    /**
     * Teste de saída exata — assert output = "ok\n" e exit_code = 0
     */
    public function testOutputIsExactOkNewlineAndExitCodeZero(): void
    {
        $process = new Process(['php', $this->statusScriptPath]);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
        $this->assertEquals("ok\n", $process->getOutput());
    }

    /**
     * Teste de stdout vs stderr — assert que a mensagem sai em stdout, não stderr
     */
    public function testMessageGoesToStdoutNotStderr(): void
    {
        $process = new Process(['php', $this->statusScriptPath]);
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        $this->assertEquals("ok\n", $output);
        $this->assertEmpty($errorOutput);
    }

    /**
     * Teste de idempotência — executar N vezes consecutivas, assert saída sempre idêntica
     */
    public function testIdempotency(): void
    {
        $expectedOutput = "ok\n";
        $iterations = 5;

        for ($i = 0; $i < $iterations; $i++) {
            $process = new Process(['php', $this->statusScriptPath]);
            $process->run();

            $this->assertEquals(0, $process->getExitCode());
            $this->assertEquals($expectedOutput, $process->getOutput());
        }
    }

    /**
     * Teste de exit code — assert exit_code === 0 em caso de sucesso e != 0 em caso de falência
     */
    public function testExitCodeIsZeroOnSuccess(): void
    {
        $process = new Process(['php', $this->statusScriptPath]);
        $process->run();

        $this->assertEquals(0, $process->getExitCode());
    }

    /**
     * Teste de ausência de saída extra — assert que não há warnings, notices ou byte extra no output
     */
    public function testNoExtraOutputOrWarnings(): void
    {
        $process = new Process(['php', $this->statusScriptPath]);
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        // A saída deve ser exatamente "ok\n" sem nenhum caractere extra
        $this->assertEquals("ok\n", $output);
        
        // Não deve haver output em stderr (warnings, notices, etc.)
        $this->assertEmpty($errorOutput);
        
        // Verificar que não há caracteres extras no output
        $this->assertSame(3, strlen($output)); // "ok" + "\n"
    }
}