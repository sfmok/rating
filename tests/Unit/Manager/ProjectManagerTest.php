<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Entity\Client;
use App\Entity\Project;
use App\Manager\ProjectManager;
use App\Repository\ProjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectManagerTest extends TestCase
{
    private ProjectRepository&MockObject $projectRepository;
    private Security&MockObject $security;
    private ProjectManager $projectManager;

    protected function setUp(): void
    {
        $this->projectRepository = self::createMock(ProjectRepository::class);
        $this->security = self::createMock(Security::class);
        $this->projectManager = new ProjectManager($this->projectRepository, $this->security);
    }

    public function testGetProject(): void
    {
        $expectedProject = new Project();
        $client = new Client();
        $this->security->expects(self::once())->method('getUser')->willReturn($client);
        $this->projectRepository->expects(self::once())->method('findOneBy')->with(['id' => 1, 'creator' => $client])->willReturn($expectedProject);
        self::assertSame($expectedProject, $this->projectManager->getProject(1));
    }

    public function testGetProjectNotFound(): void
    {
        $client = new Client();
        $this->security->expects(self::once())->method('getUser')->willReturn($client);
        $this->projectRepository->expects(self::once())->method('findOneBy')->with(['id' => 1, 'creator' => $client])->willReturn(null);
        self::expectException(NotFoundHttpException::class);
        self::expectExceptionMessage('Project id \'1\' not found.');
        $this->projectManager->getProject(1);
    }
}
