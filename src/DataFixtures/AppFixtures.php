<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Offre;
use App\Entity\Service;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private const TAGS = ['PHP', 'SYMFONY', 'LARAVEL', 'JS', 'REACT', 'VUE', 'ANGULAR', 'SQL', 'POSTGRESQL'];
    private const SERVICES = ['MARKETING', 'DESIGN', 'DEVELOPMENT', 'SALES', 'ACCOUNTING', 'HR'];

    public function load(ObjectManager $manager): void
    {
        /* On importe des données venant de faker */
        $faker = Factory::create('fr_FR');

        foreach (self::TAGS as $tagName) {
            $tagName = $this->createTag($tagName);
            $manager->persist($tagName);
        }

        foreach (self::SERVICES as $serviceName) {
            $manager->persist(
                $this->createService(
                $serviceName,
                $faker->phoneNumber(),
                $faker->email()
                )
            );
        }

        $manager->flush();

        /* On crée des offres */
        for ($i=0; $i < 25; $i++) {
            $offre = $this->createOffre(
                $this->randomService($manager),
                $faker->jobTitle(),
                $faker->paragraph(3),
                $faker->randomFloat(6, 0, 9999),
                $this->randomTags($manager)
            );
            $manager->persist($offre);
        }
        $manager->flush();
    }

    /* Méthode pour créer les services */
    private function createService(string $nom, string $telephone, string $email): Service
    {
        $service = new Service();
        $service
            ->setNom($nom)
            ->setTelephone($telephone)
            ->setEmail($email);

        return $service;
    }
    /* Méthode pour créer les tags */
    private function createTag(string $nom): Tag
    {
        $tag = new Tag();
        $tag->setNom($nom);

        return $tag;
    }

    /* Méthode pour créer les offres */
    private function createOffre(Service $service, string $nom, string $description, float $salaire, array $tags): Offre
    {
        $offre = new Offre();
        $offre
            ->setService($service)
            ->setNom($nom)
            ->setDescription($description)
            ->setSalaire($salaire);

        foreach ($tags as $tag) {
            $offre->addTag($tag);
        }

        return $offre;
    }

    /* Méthode pour allé chercher un service aléatoirement */
    private function randomService(ObjectManager $manager): Service
    {
        return $manager->getRepository(Service::class)->findByNom(
            self::SERVICES[array_rand(self::SERVICES)])[0];
    }
    
    /* Méthode pour allé checher un tag aléatoirement */
    private function randomTags(ObjectManager $manager): array
    {
        $tags = [];
        for ($i=0; $i < 3; $i++){        
            $tags[] = $manager->getRepository(Tag::class)->findByNom(
            self::TAGS[array_rand(self::TAGS)])[0];
        }
        return $tags;
    }

}
