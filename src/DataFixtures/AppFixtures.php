<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Project;
use App\Entity\Rate;
use App\Entity\Vico;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @psalm-api */
class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Clients
        $rawPassword = 'admin';
        $client = (new Client())->setFirstName('John')->setLastName('Doe')->setUsername('johndoe@foobar.com');
        $client->setPassword($this->userPasswordHasher->hashPassword($client, $rawPassword));
        $manager->persist($client);

        // Vicos
        $vico = (new Vico())->setName('Circle Design');
        $manager->persist($vico);

        // Projects
        $project1 = (new Project())->setTitle('Build a website')->setCreator($client)->setVico($vico);
        $project2 = (new Project())->setTitle('Build a mobile application')->setCreator($client)->setVico($vico);
        $manager->persist($project1);
        $manager->persist($project2);

        // Rate
        $rate = (new Rate())
            ->setSatisfaction(5)
            ->setFeedback('Good Job')
            ->setCommunication(4)
            ->setQualityOfWork(4)
            ->setValueForMoney(4)
            ->setProject($project2)
        ;
        $manager->persist($rate);

        $manager->flush();
    }
}
