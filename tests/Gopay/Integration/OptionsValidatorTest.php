<?php
namespace GopayTest\Integration;

use DateTime;
use InvalidArgumentException;
use Gopay\Enums\ChargeStatus;
use Gopay\Utility\OptionsValidator;
use PHPUnit\Framework\TestCase;

class OptionsValidatorTest extends TestCase
{
    use IntegrationSuite;
    use Requests;
    use OptionsValidator;

    public function testOptionValidator()
    {
        $rules = [
            'date' => 'ValidationHelper::getAtomDate',
            'enum' => 'ValidationHelper::getEnumValue',
            'array' => 'ValidationHelper::isArray'
        ];

        $date = date_create();
        $opts = [
            'date' => $date,
            'enum' => ChargeStatus::PENDING(),
            'array' => ['foo', 'bar'],
            'notValidated' => 'foobar'
        ];

        $validated = $this->validate($opts, $rules);
        $this->assertEquals($date->format(DateTime::ATOM), $validated['date']);
        $this->assertEquals(ChargeStatus::PENDING()->getValue(), $validated['enum']);
        $this->assertEquals(['foo', 'bar'], $validated['array']);
        $this->assertEquals('foobar', $validated['notValidated']);
    }

    public function testOptionValidationError()
    {
        $rules = [
            'date' => 'ValidationHelper::getAtomDate',
            'enum' => 'ValidationHelper::getEnumValue',
            'array' => 'ValidationHelper::isArray'
        ];

        $date = date_create();
        $opts = [
            'enum' => 'pending',
            'notValidated' => 'foobar'
        ];

        $this->expectException(InvalidArgumentException::class);
        $validated = $this->validate($opts, $rules);
    }
}
