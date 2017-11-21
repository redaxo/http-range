<?php
namespace Ramsey\Http\Range\Test\Unit;

use Ramsey\Http\Range\Exception\NotSatisfiableException;
use Ramsey\Http\Range\Exception\ParseException;
use Ramsey\Http\Range\Test\TestCase;
use Ramsey\Http\Range\Unit\AbstractUnitRange;

class AbstractUnitRangeTest extends TestCase
{
    public function testConstructorThrowsParseExceptionWhenRangeIsEmpty()
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('Unable to parse range: ');

        $range = \Mockery::mock(AbstractUnitRange::class, ['', '10000']);
    }

    public function testConstructorThrowsNotSatisfiableExceptionWhenSuffixByteRangeSpecLengthIsZero()
    {
        $this->expectException(NotSatisfiableException::class);
        $this->expectExceptionMessage('Unable to satisfy range: -0; length is zero');

        $range = \Mockery::mock(AbstractUnitRange::class, ['-0', '10000']);
    }

    public function testConstructorThrowsNotSatisfiableExceptionWhenStartIsGreaterThanSize()
    {
        $this->expectException(NotSatisfiableException::class);
        $this->expectExceptionMessage('Unable to satisfy range: 10001-; start (10001) is greater than size (10000)');

        $range = \Mockery::mock(AbstractUnitRange::class, ['10001-', '10000']);
    }

    public function testConstructorThrowsParseExceptionWhenEndIsLessThanStart()
    {
        $this->expectException(ParseException::class);
        $this->expectExceptionMessage('The end value cannot be less than the start value: 9999-500');

        $range = \Mockery::mock(AbstractUnitRange::class, ['9999-500', '10000']);
    }

    /**
     * @dataProvider validRangeValuesProvider
     */
    public function testValidRangeValues($range, $size, $expectedStart, $expectedEnd)
    {
        $unitRange = \Mockery::mock(AbstractUnitRange::class, [$range, $size])->makePartial();

        $this->assertEquals($range, $unitRange->getRange());
        $this->assertEquals($size, $unitRange->getSize());
        $this->assertEquals($expectedStart, $unitRange->getStart());
        $this->assertEquals($expectedEnd, $unitRange->getEnd());
    }

    public function validRangeValuesProvider()
    {
        return [
            ['0-499', 1000, 0, 499],
            ['0-499', 200, 0, 199],
            ['40-80', 1000, 40, 80],
            ['-400', 1000, 600, 999],
            ['400-', 1000, 400, 999],
            ['0-', 1000, 0, 999],
            ['0-0', 1000, 0, 0],
            ['-1', 1000, 999, 999],
        ];
    }
}