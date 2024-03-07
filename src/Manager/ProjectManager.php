<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectManager
{
    public function __construct(private ProjectRepository $projectRepository, private Security $security)
    {
    }

    public function getProject(int $projectId): Project
    {
        $project = $this->projectRepository->findOneBy(['id' => $projectId, 'creator' => $this->security->getUser()]);

        return $project ?? throw new NotFoundHttpException(sprintf('Project id \'%s\' not found.', $projectId));
    }
}
