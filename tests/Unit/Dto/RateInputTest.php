<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto;

use App\Dto\RateInput;
use PHPUnit\Framework\TestCase;

class RateInputTest extends TestCase
{
    public function testCreateRateInputInstance(): void
    {
        $rateInput = new RateInput(1, 'Good', 2, 3, 4, 5);

        self::assertSame(1, $rateInput->satisfaction);
        self::assertSame('Good', $rateInput->feedback);
        self::assertSame(2, $rateInput->communication);
        self::assertSame(3, $rateInput->qualityOfWork);
        self::assertSame(4, $rateInput->valueForMoney);
        self::assertSame(5, $rateInput->projectId);
    }
}
