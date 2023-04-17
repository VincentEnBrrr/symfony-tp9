<?php

namespace App\Controller;
use App\Entity\Etudiant;
use App\Form\SaisieEtudiantType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EtudiantRepository;
use Symfony\Component\HttpFoundation\Request;

class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'ajouter_etudiant')]
    public function createetudiant(ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();
        $etudiant = $form->getData();

      //  $etudiant = new Etudiant();
       // $etudiant->setNom('JOHN');
        //$etudiant->setPrenom('Lee');
      //  $etudiant->setNaissance(new DateTime("2004-09-13"));
      //  $etudiant->setNiveau(1);

        // tell Doctrine you want to (eventually) save the etudiant (no queries yet)
        $entityManager->persist($etudiant);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Nouvel étudiant sauvegardé, ID '.$etudiant->getId());
    }

    #[Route('/etudiant/{id}', name: 'voir_etudiant')]
    public function show(ManagerRegistry $doctrine, int $id): Response

    {
        $etudiant = $doctrine->getRepository(Etudiant::class)->find($id);

        if (!$etudiant) {
            throw $this->createNotFoundException(
                'Pas d\'étudiant trouvé avec cet '.$id
            );
        }

        return new Response('Le nom de l\'étudiant est : '.$etudiant->getNom());

        // or render a template
        // in the template, print things with 
        // return $this->render('etudiant/show.html.twig', ['etudiant' => $etudiant]);
    }
    
    #[Route('/etudiant/{id}', name: 'voir_etudiant2')]
    public function show2(int $id, EtudiantRepository $etudiantRepository): Response
    {
            $etudiant = $etudiantRepository
                ->find($id);
            // ...
        return new Response('Le nom de l\'étudiant est : '.$etudiant->getNom());
    }

    #[Route('/changement', name: 'changement_niveau')]
    public function changementNiveau(
        EntityManagerInterface $entityManager,
        EtudiantRepository $etudiantRepository)
    {
    // récupération de tous les etudiants
    $etudiants = $etudiantRepository->findAll(); 

    //équivalent à SELECT * FROM etudiant
    foreach($etudiants as $etudiant)
    {
        $etudiant->setNiveau(2); // on set les différents champs
    }

    $entityManager->flush(); // on effectue les différentes modifications sur la base

    return new Response('Modification niveau OK ');

    }

    #[Route('/recherche-nom/{nom}', name: 'recherche_nom')]
    public function rechercheNom(
        String $nom,
        EntityManagerInterface $entityManager,
        EtudiantRepository $etudiantRepository)
        {

        $etudiants = $etudiantRepository->findStudentByNameSQL($nom);

        // $etudiants = $etudiantRepository->findStudentByNameSQL($nom);

        return $this->render('recherche_nom.html.twig', [
            'nom' => $nom,
            'etudiants' => $etudiants
        ]);
    }

    #[Route('/modification/{id}', name:'modification_etudiant')]
    public function modificationEtudiant(int $id,
        EntityManagerInterface $entityManager,
        EtudiantRepository $etudiantRepository)
    {

        // récupération du post avec id passé en paramètre
        $etudiant = $etudiantRepository->find($id); 
        // equivalent à SELECT * FROM etudiant WHERE id=X

        $etudiant->setNom('ALI'); // on set les différents champs
        $etudiant->setPrenom('Muhammad');
        $entityManager->flush(); // on effectue les différentes modifications sur la base de données 
        return new Response('Modification OK sur : ' . $etudiant->getId() );
    }

//Méthode avancée au niveau de la création des étudiant, ici nous avons la saisie

    #[Route('/saisie/etudiant', name:'saisie_etudiant')]
    public function saisieEtudiant(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(SaisieEtudiantType::class);
        $form->handleRequest($request);

            if ($form->isSubmitted()) {

            $etudiant = new Etudiant();
            $etudiant = $form->getData();
            $entityManager->persist($etudiant);
            $entityManager->flush();
               
            $response = new Response('Nouvel étudiant sauvegardé, ID '.$etudiant->getId());
        }   
        
            else {
    
                $response = $this->render('saisie.etudiant.html.twig', ['form' => $form->createView(),]);

            }

            return $response;
    }  

}
?>