<?php

declare(strict_types=1);

namespace App\Manager;

use App\Dto\RateInput;
use App\Entity\Project;
use App\Entity\Rate;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;

class RateManager
{
    public function __construct(private EntityManagerInterface $entityManager, private RateRepository $rateRepository)
    {
    }

    public function getRate(int $rateId): ?Rate
    {
        return $this->rateRepository->find($rateId);
    }

    public function createOrUpdateRate(Project $project, RateInput $rateInput): Rate
    {
        [$rate, $persist] = $project->isRated() ? [$project->getRate(), false] : [new Rate(), true];
        \assert($rate instanceof Rate);
        $rate
            ->setSatisfaction($rateInput->satisfaction)
            ->setFeedback($rateInput->feedback)
            ->setCommunication($rateInput->communication)
            ->setQualityOfWork($rateInput->qualityOfWork)
            ->setValueForMoney($rateInput->valueForMoney)
            ->setProject($project)
        ;

        if ($persist) {
            $this->entityManager->persist($rate);
        }

        $this->entityManager->flush();

        return $rate;
    }
}
