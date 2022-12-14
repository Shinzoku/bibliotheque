<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Livre;
use App\Entity\User;
use App\Repository\AuteurRepository;
use App\Repository\EmpruntRepository;
use App\Repository\EmprunteurRepository;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
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

        $emprunts = $empruntRepository->findByDateRetourIsNull();
        dump($emprunts);

        $livre = $livreRepository->find(3);
        dump($livre);

        $emprunts = $empruntRepository->findOneByDateRetourIsNull($livre);
        dump($emprunts);

        exit();
    }

    #[Route('/db/test/livres/new', name: 'app_db_test_livres_new')]
    public function newLivre(
        EntityManagerInterface $manager,
        AuteurRepository $auteurRepository,
        GenreRepository $genreRepositiry
        ): Response
    {
        $auteur = $auteurRepository->find(2);
        dump($auteur);

        $genre = $genreRepositiry->find(6);
        dump($genre);

        $livre = new Livre();
        $livre->setTitre('Totum autem id externum');
        $livre->setAnneeEdition(2020);
        $livre->setNombrePages(300);
        $livre->setCodeIsbn('9790412882714');
        $livre->setAuteur($auteur);
        $livre->addGenre($genre);

        $manager->persist($livre);
        $manager->flush();

        dump($livre);

        exit();
    }

    #[Route('/db/test/livres/edit', name: 'app_db_test_livres_edit')]
    public function editlivre(
        EntityManagerInterface $manager,
        LivreRepository $livreRepository,
        GenreRepository $genreRepository
        ): Response
    {
        $genreSelected = $genreRepository->find(2);
        dump($genreSelected);

        $genreNew =$genreRepository->find(5);
        dump($genreNew);

        $livre = $livreRepository->find(2);
        dump($livre);
        $genres = $livre->getGenre();
        foreach ($genres as $genre) {
            dump($genre);
        }

        $livre->setTitre('Aperiendum est igitur');
        $livre->removeGenre($genreSelected);
        $livre->addGenre($genreNew);

        $manager->persist($livre);
        $manager->flush();

        dump($livre);
        $genres = $livre->getGenre();
        foreach ($genres as $genre) {
            dump($genre);
        }

        exit();
    }

    #[Route('/db/test/livres/delete', name: 'app_db_test_livres_delete')]
    public function deleteLivre(EntityManagerInterface $manager, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find(123);
        dump($livre);
        
        if ($livre) {
            $livreRepository->remove($livre, true);
            $manager->flush();
        }
        
        dump($livre);

        exit();
    }

    #[Route('/db/test/emprunts/new', name: 'app_db_test_emprunts_new')]
    public function newEmprunt(
        EntityManagerInterface $manager,
        EmprunteurRepository $emprunteurRepository,
        LivreRepository $livreRepository,
        ): Response
    {
        $emprunteur = $emprunteurRepository->find(1);
        dump($emprunteur);
        $livre = $livreRepository->find(1);
        dump($livre);

        $emprunt = new Emprunt();
        $emprunt->setDateEmprunt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-12-01 16:00:00'));
        $emprunt->setDateRetour(null);
        $emprunt->setEmprunteur($emprunteur);
        $emprunt->setLivre($livre);

        $manager->persist($emprunt);
        $manager->flush();

        dump($emprunt);
        exit();
    }

    #[Route('/db/test/emprunts/edit', name: 'app_db_test_emprunts_edit')]
    public function edit(EntityManagerInterface $manager, EmpruntRepository $empruntRepository): Response
    {
        $emprunt = $empruntRepository->find(3);
        dump($emprunt);
        $emprunt->setDateRetour(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-05-01 10:00:00'));

        $manager->persist($emprunt);
        $manager->flush();

        dump($emprunt);

        exit();
    }

    #[Route('/db/test/emprunts/delete', name: 'app_db_test_emunts_delete')]
    public function delete(EntityManagerInterface $manager, EmpruntRepository $empruntRepository): Response
    {
        $emprunt = $empruntRepository->find(42);
        dump($emprunt);

        if ($emprunt) {
            $empruntRepository->remove($emprunt);
            $manager->flush();
        }

        dump($emprunt);
        exit();
    }
}
