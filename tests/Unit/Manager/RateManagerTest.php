<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Dto\RateInput;
use App\Entity\Project;
use App\Entity\Rate;
use App\Manager\RateManager;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RateManagerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private RateRepository&MockObject $rateRepository;
    private RateManager $rateManager;

    protected function setUp(): void
    {
        $this->entityManager = self::createMock(EntityManagerInterface::class);
        $this->rateRepository = self::createMock(RateRepository::class);
        $this->rateManager = new RateManager($this->entityManager, $this->rateRepository);
    }

    public function testGetRate(): void
    {
        $expectedRate = new Rate();
        $this->rateRepository->expects(self::once())->method('find')->with(1)->willReturn($expectedRate);
        self::assertSame($expectedRate, $this->rateManager->getRate(1));
    }

    public function testGetRateNotFound(): void
    {
        $this->rateRepository->method('find')->with(1)->willReturn(null);
        self::assertNull($this->rateManager->getRate(1));
    }

    public function testCreateRate(): void
    {
        $project = new Project();
        $rateInput = new RateInput(1, 'Good', 2, 3, 4, 5);

        $this->entityManager->expects(self::once())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');

        $rate = $this->rateManager->createOrUpdateRate($project, $rateInput);

        self::assertSame($rateInput->satisfaction, $rate->getSatisfaction());
        self::assertSame($rateInput->feedback, $rate->getFeedback());
        self::assertSame($rateInput->communication, $rate->getCommunication());
        self::assertSame($rateInput->qualityOfWork, $rate->getQualityOfWork());
        self::assertSame($rateInput->valueForMoney, $rate->getValueForMoney());
        self::assertSame($project, $rate->getProject());
    }

    public function testUpdateRate(): void
    {
        $rate = new Rate();
        $project = (new Project())->setRate($rate);
        $rateInput = new RateInput(4, 'Good Updated', 5, 5, 4, 5);

        $this->entityManager->expects(self::never())->method('persist');
        $this->entityManager->expects(self::once())->method('flush');

        $rate = $this->rateManager->createOrUpdateRate($project, $rateInput);

        self::assertSame($rateInput->satisfaction, $rate->getSatisfaction());
        self::assertSame($rateInput->feedback, $rate->getFeedback());
        self::assertSame($rateInput->communication, $rate->getCommunication());
        self::assertSame($rateInput->qualityOfWork, $rate->getQualityOfWork());
        self::assertSame($rateInput->valueForMoney, $rate->getValueForMoney());
        self::assertSame($project, $rate->getProject());
    }
}
