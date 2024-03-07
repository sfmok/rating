<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer;

use App\Dto\RateInput;
use App\Entity\Project;
use App\Entity\Rate;
use App\Manager\RateManager;
use App\Serializer\RateInputDenormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RateInputDenormalizerTest extends TestCase
{
    private DenormalizerInterface&MockObject $denormalizer;
    private RequestStack&MockObject $requestStack;
    private RateManager&MockObject $rateManager;
    private RateInputDenormalizer $rateInputDenormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = self::createMock(DenormalizerInterface::class);
        $this->requestStack = self::createMock(RequestStack::class);
        $this->rateManager = self::createMock(RateManager::class);
        $this->rateInputDenormalizer = new RateInputDenormalizer($this->denormalizer, $this->requestStack, $this->rateManager);
    }

    public function testSupportsDenormalization(): void
    {
        self::assertTrue($this->rateInputDenormalizer->supportsDenormalization([], RateInput::class));
        self::assertFalse($this->rateInputDenormalizer->supportsDenormalization([], \stdClass::class));
    }

    public function testDenormalizeWithExistingRate(): void
    {
        $rate = $this->getTestRate();
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn(new Request([], [], ['id' => 1]));
        $this->rateManager->expects(self::once())->method('getRate')->willReturn($rate);

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->with(
                [],
                RateInput::class,
                'json',
                self::callback(static fn (array $context) => isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])),
            )
            ->willReturn(new RateInput(1, 'Good', 1, 1, 1, 1))
        ;

        self::assertInstanceOf(RateInput::class, $this->rateInputDenormalizer->denormalize([], RateInput::class, 'json'));
    }

    public function testDenormalizeWithoutExistingRate(): void
    {
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn(new Request([], [], ['id' => 1]));
        $this->rateManager->expects(self::once())->method('getRate')->willReturn(null);

        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->willReturnCallback(static function (array $context) {
                self::assertArrayNotHasKey(AbstractNormalizer::OBJECT_TO_POPULATE, $context);

                return new RateInput(1, 'Good', 1, 1, 1, 1);
            })
        ;

        self::assertInstanceOf(RateInput::class, $this->rateInputDenormalizer->denormalize([], RateInput::class));
    }

    public function testDenormalizeWithoutRateId(): void
    {
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn(new Request());
        $this->rateManager->expects(self::never())->method('getRate');
        $this->denormalizer->expects(self::once())
            ->method('denormalize')
            ->willReturnCallback(static function (array $context) {
                self::assertArrayNotHasKey(AbstractNormalizer::OBJECT_TO_POPULATE, $context);

                return new RateInput(1, 'Good', 1, 1, 1, 1);
            })
        ;

        self::assertInstanceOf(RateInput::class, $this->rateInputDenormalizer->denormalize([], RateInput::class));
    }

    public function getTestRate(): Rate
    {
        return (new Rate())
            ->setSatisfaction(5)
            ->setFeedback('Good')
            ->setCommunication(5)
            ->setQualityOfWork(5)
            ->setValueForMoney(5)
            ->setProject(self::createMock(Project::class))
        ;
    }
}
