<?php


namespace tests\functional;

use PHPUnit\Framework\TestCase;
use kch42\ste\STECore;

abstract class BaseTest extends TestCase
{
    /** @var STECore */
    protected $ste;

    private static function normalize(string $string): string
    {
        $lines = explode("\n", $string);

        $lines = array_map(static function ($line) {
            return preg_replace('/\s{2,}/', " ", $line);
        }, $lines);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        return implode("\n", $lines);
    }

    public function setUp(): void
    {
        $this->ste = new STECore(new TestStorage($this->getDirectory()));
        $this->ste->mute_runtime_errors = false;

        $this->setUpSte($this->ste);
    }

    protected function setUpSte(STECore $ste): void
    {
    }

    public function testTemplate(): void
    {
        $have = $this->ste->exectemplate("test.tpl");
        $want = file_get_contents($this->getDirectory() . DIRECTORY_SEPARATOR . "want");

        $normalizedHave = self::normalize($have);
        $normalizedWant = self::normalize($want);

        self::assertSame($normalizedWant, $normalizedHave);
    }

    /**
     * Get the directory of the test.
     * @return string
     */
    abstract protected function getDirectory(): string;
}
