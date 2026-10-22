<?php

declare(strict_types=1);

namespace Genpass\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Genpass\OptionParser;
use InvalidArgumentException;

/**
 * Testes unitários para OptionParser
 */
class OptionParserTest extends TestCase
{
    private OptionParser $parser;

    protected function setUp(): void
    {
        $this->parser = new OptionParser();
    }

    /**
     * RT-07: flags válidas, --length=abc → erro, flag desconhecida → erro, --help
     */
    public function testParseValidLengthFlag(): void
    {
        $argv = ['generator.php', '--length=16'];
        $options = $this->parser->parse($argv);
        
        $this->assertArrayHasKey('length', $options);
        $this->assertEquals(16, $options['length']);
    }

    public function testParseValidHelpFlag(): void
    {
        $argv = ['generator.php', '--help'];
        $options = $this->parser->parse($argv);
        
        $this->assertArrayHasKey('help', $options);
        $this->assertTrue($options['help']);
    }

    public function testParseInvalidLengthNonNumeric(): void
    {
        $argv = ['generator.php', '--length=abc'];
        
        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse($argv);
    }

    public function testParseUnknownFlag(): void
    {
        $argv = ['generator.php', '--foo'];
        
        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse($argv);
    }

    public function testParseLengthBelowRange(): void
    {
        $argv = ['generator.php', '--length=4'];
        
        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse($argv);
    }

    public function testParseLengthAboveRange(): void
    {
        $argv = ['generator.php', '--length=65'];
        
        $this->expectException(InvalidArgumentException::class);
        $this->parser->parse($argv);
    }

    public function testParseMultipleValidFlags(): void
    {
        $argv = ['generator.php', '--length=32', '--no-upper', '--no-lower'];
        $options = $this->parser->parse($argv);
        
        $this->assertArrayHasKey('length', $options);
        $this->assertEquals(32, $options['length']);
        $this->assertTrue($options['no-upper']);
        $this->assertTrue($options['no-lower']);
        $this->assertFalse(isset($options['no-digits']));
        $this->assertFalse(isset($options['no-symbols']));
    }

    public function testParseNoUpperFlag(): void
    {
        $argv = ['generator.php', '--no-upper'];
        $options = $this->parser->parse($argv);
        
        $this->assertTrue($options['no-upper']);
    }

    public function testParseNoLowerFlag(): void
    {
        $argv = ['generator.php', '--no-lower'];
        $options = $this->parser->parse($argv);
        
        $this->assertTrue($options['no-lower']);
    }

    public function testParseNoDigitsFlag(): void
    {
        $argv = ['generator.php', '--no-digits'];
        $options = $this->parser->parse($argv);
        
        $this->assertTrue($options['no-digits']);
    }

    public function testParseNoSymbolsFlag(): void
    {
        $argv = ['generator.php', '--no-symbols'];
        $options = $this->parser->parse($argv);
        
        $this->assertTrue($options['no-symbols']);
    }
}