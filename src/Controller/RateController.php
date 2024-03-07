<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\RateInput;
use App\Entity\Rate;
use App\Manager\ProjectManager;
use App\Manager\RateManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/** @psalm-api */
final class RateController extends AbstractController
{
    public function __construct(private RateManager $rateManager, private ProjectManager $projectManager)
    {
    }

    #[Route('/rate', name: 'app_rate_add', methods: [Request::METHOD_POST])]
    public function add(#[MapRequestPayload] RateInput $rateInput): JsonResponse
    {
        $project = $this->projectManager->getProject($rateInput->projectId);

        if ($project->isRated()) {
            throw $this->createAccessDeniedException(sprintf('Project id \'%s\' already rated.', $rateInput->projectId));
        }

        $rate = $this->rateManager->createOrUpdateRate($project, $rateInput);

        return $this->json($rate, Response::HTTP_CREATED, [], ['groups' => ['rate:read']]);
    }

    #[Route('/rate/{id}', name: 'app_rate_edit', methods: [Request::METHOD_PUT])]
    public function edit(Rate $rate, #[MapRequestPayload] RateInput $rateInput): JsonResponse
    {
        $project = $this->projectManager->getProject($rateInput->projectId);

        if ($rate !== $project->getRate()) {
            $this->createNotFoundException(sprintf('Rate id %s is not found.', $rate->getId()));
        }

        $rate = $this->rateManager->createOrUpdateRate($project, $rateInput);

        return $this->json($rate, Response::HTTP_OK, [], ['groups' => ['rate:read']]);
    }
}
