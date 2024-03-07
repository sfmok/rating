<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Rate;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class RateInput
{
    public function __construct(
        #[Assert\GreaterThanOrEqual(1, message: 'great_or_equal {{ compared_value }}')]
        #[Assert\LessThanOrEqual(5, message: 'less_or_equal {{ compared_value }}')]
        public int $satisfaction,
        #[Assert\NotBlank(message: 'not_blank/not_null')]
        #[Assert\Length(min: 2, max: 500, minMessage: 'min_length {{ limit }}', maxMessage: 'max_length {{ limit }}')]
        public string $feedback,
        #[Assert\GreaterThanOrEqual(1, message: 'great_or_equal {{ compared_value }}')]
        #[Assert\LessThanOrEqual(5, message: 'less_or_equal {{ compared_value }}')]
        public int $communication,
        #[Assert\GreaterThanOrEqual(1, message: 'great_or_equal {{ compared_value }}')]
        #[Assert\LessThanOrEqual(5, message: 'less_or_equal {{ compared_value }}')]
        #[SerializedName('quality_of_work')]
        public int $qualityOfWork,
        #[Assert\GreaterThanOrEqual(1, message: 'great_or_equal {{ compared_value }}')]
        #[Assert\LessThanOrEqual(5, message: 'less_or_equal {{ compared_value }}')]
        #[SerializedName('value_for_money')]
        public int $valueForMoney,
        #[SerializedName('project_id')]
        public int $projectId,
    ) {
    }

    public static function createFromEntity(Rate $rate): self
    {
        return new self(
            satisfaction: $rate->getSatisfaction(),
            feedback: $rate->getFeedback(),
            communication: $rate->getCommunication(),
            qualityOfWork: $rate->getQualityOfWork(),
            valueForMoney: $rate->getValueForMoney(),
            projectId: $rate->getProject()->getId(),
        );
    }
}
