<?php

namespace App\Controller;

use App\Entity\Family;
use App\Entity\Gift;
use App\Entity\User;
use App\Form\FamilyFormType;
use App\Form\GiftFormType;
use App\Form\UserFamilyFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class AccountController extends AbstractController
{

    #[Route('/ajouter-une-famille', name: 'createFamily')]
    public function createFamily(Request $request, UrlHelper $urlHelper): Response
    {
        $family = new Family();

        $form = $this->createForm(FamilyFormType::class, $family);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $family = $form->getData();
            $uuid = Uuid::v4();
            $family->setUuid($uuid);
            $this->getUser()->addFamily($family);
            $em->persist($this->getUser());
            $em->persist($family);
            $em->flush();

            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

            $this->addFlash('success', 'La famille a bien été créé. Voici son lien de partage : '.$baseurl.'/'.$uuid);

        }


        return $this->render('account/create_family.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rejoindre-une-famille', name: 'joinFamily')]
    public function joinFamily(Request $request, UrlHelper $urlHelper): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserFamilyFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $family =  $this->getDoctrine()
                ->getRepository(Family::class)
                ->findOneBy(["uuid" => $form->getData()['uuid']]);

            if(!$family){
                $this->addFlash('error', 'Cette famille n\'existe pas');
            } else{
                $user->addFamily($family);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Vous faites maintenant partie de la famille '.$family->getName());
            }
        }


        return $this->render('account/join_family.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mes-familles', name: 'listFamiliesMembers')]
    public function listFamiliesMembers(Request $request, UrlHelper $urlHelper): Response
    {
        $currentUser = $this->getUser();

        $families = $this->getUser()->getFamilies();
        $members = [];
        foreach ($families as $family){
            foreach ($family->getUsers() as $user){
                if($user !== $currentUser)
                $members[$user->getId()] =$user;
            }
        }
        return $this->render('account/list_families_members.html.twig',[
            'members' => $members,
        ]);
    }

    #[Route('/demander-un-cadeau', name: 'askGift')]
    public function askGift(Request $request, UrlHelper $urlHelper): Response
    {
        $gift = new Gift();
        $form = $this->createForm(GiftFormType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->getUser()->addGift($gift);
            $gift->setAlreadyBuy(false);
            $em->persist($gift);
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été ajouté à votre liste');
        }


        return $this->render('account/ask_gift.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modifier-un-cadeau/{id}', name: 'editGift')]
    public function editGift(Request $request, Gift $gift): Response
    {

        $form = $this->createForm(GiftFormType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gift);
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été modifié');
            return $this->redirectToRoute("viewMyList");
        }


        return $this->render('account/ask_gift.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/voir-ma-liste', name: 'viewMyList')]
    public function viewMyList(Request $request): Response
    {
        return $this->render('account/list_gifts.html.twig',[
            'user' => $this->getUser()
        ]);
    }

    #[Route('/voir-une-liste/{id}', name: 'viewOneList')]
    public function viewOneList(Request $request, User $user): Response
    {
        //check if user is on family for list
        return $this->render('account/list_gifts.html.twig',[
            'user' => $user
        ]);
    }

    #[Route('/voir-une-liste/{id}/acheter-un-cadeau/{gift}', name: 'buyGift')]
    public function buyGift(Request $request, User $user, Gift $gift): Response
    {
        $gift->setAlreadyBuy(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($gift);
        $em->flush();
        return $this->redirectToRoute('viewOneList', ['id' => $user->getId()]);
    }
}
