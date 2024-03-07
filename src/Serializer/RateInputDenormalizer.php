<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Dto\RateInput;
use App\Manager\RateManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/** @psalm-api */
class RateInputDenormalizer implements DenormalizerInterface
{
    public function __construct(private DenormalizerInterface $denormalizer, private RequestStack $requestStack, private RateManager $rateManager)
    {
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return RateInput::class === $type;
    }

    public function denormalize($data, $type, $format = null, array $context = []): mixed
    {
        if (null !== $rateId = $this->requestStack->getCurrentRequest()?->attributes->get('id')) {
            if ($rate = $this->rateManager->getRate((int) $rateId)) {
                $context[AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE] = true;
                $context[AbstractNormalizer::OBJECT_TO_POPULATE] = RateInput::createFromEntity($rate);
            }
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [RateInput::class => false];
    }
}
