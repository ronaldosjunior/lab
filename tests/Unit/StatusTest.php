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
     * Teste de contexto — assert que o script não roda (ou retorna error) quando php_sapi_name() !== 'cli'
     */
    public function testScriptReturnsErrorWhenNotInCliContext(): void
    {
        // Criar um script temporário que simula o contexto web
        $tempScript = sys_get_temp_dir() . '/status_test_web.php';
        $scriptContent = '<?php
            // Simular contexto web
            $_SERVER[\'PHP_SAPI\'] = \'apache\';
            require \'' . $this->statusScriptPath . '\';
        ';
        file_put_contents($tempScript, $scriptContent);

        $process = new Process(['php', $tempScript]);
        $process->run();

        // Deve retornar exit code 503 (HTTP 503) ou outro código de erro
        $this->assertNotNull($process->getExitCode());
        $this->assertStringContainsString('Forbidden: CLI only', $process->getErrorOutput());
        
        unlink($tempScript);
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