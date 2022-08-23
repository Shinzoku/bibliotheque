<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Livre;
use DateTimeImmutable;
use App\Entity\Emprunteur;
use App\Repository\UserRepository;
use App\Repository\LivreRepository;
use App\Repository\EmpruntRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\EmprunteurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DbTestController extends AbstractController
{
    #[Route('/db/test/users', name: 'app_db_test_users')]
    public function users(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $users = $repository->findAll();
        dump($users);

        $user = $repository->find(1);
        dump($user);

        $user = $repository->findByEmail('foo.foo@example.com');
        dump($user);

        $role = 'ROLE_EMRUNTEUR';
        $users = $repository->findByRole($role);
        dump($users);

        exit();
    }

    #[Route('/db/test/livres', name: 'app_db_test_livres')]
    public function livres(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Livre::class);
        $livres = $repository->findAll();
        dump($livres);

        $livre = $repository->find(1);
        dump($livre);

        $livres = $repository->findByKeyword('lorem');
        dump($livres);
        
        $livres = $repository->findByKeywordGenre('roman');

        foreach ($livres as $livre) {
            dump($livre);

            $genres = $livre->getGenre();

            foreach ($genres as $genre) {
                dump($genre);
            }
        }

        exit();
    }

    #[Route('/db/test/emprunteurs', name: 'app_db_test_emprunteurs')]
    public function emprunteurs(
        EmprunteurRepository $emprunteurRepository,
        UserRepository $userRepository
        ): Response
    {
        $emprunteurs = $emprunteurRepository->findAll();
        dump($emprunteurs);

        $emprunteur = $emprunteurRepository->find(3);
        dump($emprunteur);

        $user = $userRepository->find(3);
        $user->getEmail();
        dump($user);
        
        $emprunteur = $emprunteurRepository->findByUser($user);
        dump($emprunteur);

        $nomOuPrenom = "foo";
        $emprunteurs = $emprunteurRepository->findByKeywordNomPrenom($nomOuPrenom);
        dump($emprunteurs);

        $nbrTel = "1234";
        $emprunteurs = $emprunteurRepository->findByKeywordTel($nbrTel);
        dump($emprunteurs);

        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-03-01 00:00:00');
        $emprunteurs = $emprunteurRepository->findByDateAnterieurCreatedAt($date);
        dump($emprunteurs);

        $emprunteurs = $emprunteurRepository->findAll();
        foreach ($emprunteurs as $emprunteur) {
            if (!$emprunteur->isActif()){
                dump($emprunteur);
            }
        }

        exit();
    }

    #[Route('/db/test/emprunts', name: 'app_db_test_emprunts')]
    public function emprunts(
        EmpruntRepository $empruntRepository,
        EmprunteurRepository $emprunteurRepository,
        LivreRepository $livreRepository
        ): Response
    {
        $emprunts = $empruntRepository->findNLast(10);
        dump($emprunts);

        $emprunteur = $emprunteurRepository->find(2);
        dump($emprunteur);

        $emprunts = $empruntRepository->findByEmprunteur($emprunteur);
        dump($emprunts);

        $livre = $livreRepository->find(3);
        dump($livre);

        $emprunts = $empruntRepository->findByLivre($livre);
        dump($emprunts);

        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-01-01 00:00:00');
        $emprunts = $empruntRepository->findByDateRetourAvantLe($date);
        dump($emprunts);

        $emprunts = $empruntRepository->findByDateRetourNull();
        dump($emprunts);

        $livre = $livreRepository->find(3);
        dump($livre);

        $emprunts = $empruntRepository->findRetourNull($livre);
        dump($emprunts);

        exit();
    }

    #[Route('/db/test/new', name: 'app_db_test_livre_new', methods: ['GET', 'POST'])]
    public function new(
        ObjectManager $manager, Livre $livre,
        AuteurRepository $auteurRepository,
        GenreRepository $genreRepositiry
        ): Response
    {
        $auteur = $auteurRepository->find(2);
        $genre = $genreRepositiry->find(6);

        $livre = new Livre();
        $livre->setTitre('Totum autem id externum');
        $livre->setAnneeEdition(2020);
        $livre->setNombrePages(300);
        $livre->setCodeIsbn('9790412882714');
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);

        $manager->persist($livre);
        $manager->flush();
    }

    #[Route('/db/test/edit', name: 'app_db_test_livre_edit', methods: ['GET', 'POST'])]
    public function edit(LivreRepository $livreRepository, GenreRepository $genreRepositiry): Response
    {
        $genreSelected = $genreRepository->find(2);
        $genreNew =$genreRepository->find(5);
        $livre->$livreRepository->find(2);
        $livre->setTitre('Aperiendum est igitur');
        $livre->removeGenre($genreSelected);
        $livre->addGenre($genreNew);

        $manager->persist($livre);
        $manager->flush();
    }

    #[Route('/db/test/delete', name: 'app_db_test_livre_delete', methods: ['POST'])]
    public function delete(LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find(123);
        $livreRepository->remove($livre, true);
    }
}
