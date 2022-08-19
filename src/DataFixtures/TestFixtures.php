<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture
{
    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher)
    {
        $this->doctrine = $doctrine;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create('fr_FR');

        $this->loadAuteurs($manager, $faker);
        $this->loadLivres($manager, $faker);
        $this->loadUsers($manager, $faker);
        $this->loadEmprunts($manager, $faker);
    }

    public function loadAuteurs(ObjectManager $manager, FakerGenerator $faker): void
    {
        for ($i = 0; $i < 500; $i++) { 
            $auteur = new Auteur();
            $auteur->setNom($faker->lastName($gender = 'male'|'female'));
            $auteur->setPrenom($faker->firstName($gender = 'male'|'female'));
            $manager->persist($auteur);
        }

        $manager->flush();
    }

    public function loadLivres(ObjectManager $manager, FakerGenerator $faker): void
    {
        $repository = $this->doctrine->getRepository(Auteur::class);
        $auteurs = $repository->findAll();

        $repository = $this->doctrine->getRepository(Genre::class);
        $genres = $repository->findAll();

        for ($i = 0; $i < 1000; $i++) { 
            $livre = new Livre();
            $livre->setTitre($faker->sentence(3));
            $livre->setAnneeEdition($faker->numberBetween(1960, 2022));
            $livre->setNombrePages($faker->numberBetween(100, 300));
            $livre->setCodeIsbn($faker->isbn13());
            
            $auteur = $faker->randomElement($auteurs);
            $livre->setAuteur($auteur);

            $genres = $faker->randomElements($genres);

            foreach ($genres as $genre) {
                $livre->addGenre($genre);
            }
            
            $manager->persist($livre);
        }

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager, FakerGenerator $faker): void
    {
        for ($i = 0; $i < 100; $i++) { 
            $user = new User();
            $user->setEmail($faker->freeEmail());
            $user->setRoles(['ROLE_EMRUNTEUR']);
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setEnabled(true);

            $manager->persist($user);

            $emprunteur = new Emprunteur();
            $emprunteur->setUser($user);
            $emprunteur->setNom($faker->lastName($gender = 'male'|'female'));
            $emprunteur->setPrenom($faker->firstName($gender = 'male'|'female'));
            $emprunteur->setTel($faker->phoneNumber());
            $emprunteur->setActif(true);

            $manager->persist($emprunteur);
        }

        $manager->flush();
    }

    public function loadEmprunts(ObjectManager $manager, FakerGenerator $faker): void
    {
        $repository = $this->doctrine->getRepository(Livre::class);
        $livres = $repository->findAll();

        $repository = $this->doctrine->getRepository(Emprunteur::class);
        $emprunteurs = $repository->findAll();

        for ($i = 0; $i < 200; $i++) {
            $emprunt = new Emprunt();

            $date = $faker->dateTimeBetween('-6 month','-3 month');
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "2022-{$date->format('m-d H:i:s')}");
            $emprunt->setDateEmprunt($date);

            $date = $faker->optional($weight = 0.9)->dateTimeBetween('-3 month','-1 month');
            if ($date) {
                $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "2022-{$date->format('m-d H:i:s')}");
            }
            $emprunt->setDateRetour($date);

            $emprunteur = $faker->randomElement($emprunteurs);
            $emprunt->setEmprunteur($emprunteur);

            $livres = $faker->randomElements($livres, 3);
            foreach ($livres as $livre) {
                $emprunt->setLivre($livre);
            }

            $manager->persist($emprunt);
        }

        $manager->flush();
    }
}
