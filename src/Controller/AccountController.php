<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Family;
use App\Entity\Gift;
use App\Entity\GiftGroup;
use App\Entity\User;
use App\Form\ChildFormType;
use App\Form\FamilyFormType;
use App\Form\GiftFormType;
use App\Form\GiftGroupFormType;
use App\Form\UserFamilyFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class AccountController extends AbstractController
{

    /********************************************/
    /**************FAMILY***********************/
    /******************************************/

    #[Route('/mon-compte', name: 'account')]
    public function account(SessionInterface $session): Response
    {
        if($session->get("familyId")){
            return $this->redirectToRoute("joinFamilyLink", ['id' => $session->get("familyId")]);
        };
        return $this->redirectToRoute("listFamiliesMembers");
    }

    #[Route('/rejoindre-une-famille/{id}', name: 'joinFamilyLink')]
    public function joinFamilyLink(SessionInterface $session, $id): Response
    {
        if(is_null($this->getUser())){
            $session->set("familyId", $id);
            return $this->redirectToRoute("login");
        }

        $family =  $this->getDoctrine()
            ->getRepository(Family::class)
            ->findOneBy(["uuid" => $id]);

        if(!$family){
            $this->addFlash('error', 'Cette famille n\'existe pas');
        } else{
            $this->getUser()->addFamily($family);
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();
            $this->addFlash('success', 'Vous faites maintenant partie de la famille '.$family->getName());
        }
        $session->remove("familyId");
        return $this->redirectToRoute('listFamiliesMembers');
    }

    #[Route('/mon-compte/ajouter-une-famille', name: 'createFamily')]
    public function createFamily(Request $request): Response
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

            $this->addFlash('success', 'La famille a bien été créé. Voici son lien de partage : '.$baseurl.'/rejoindre-une-famille/'.$uuid);

        }


        return $this->render('account/create_family.html.twig',[
            'form' => $form->createView(),
        ]);
    }



    #[Route('/mon-compte/rejoindre-une-famille', name: 'joinFamily')]
    public function joinFamily(Request $request): Response
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

    #[Route('/mon-compte/mes-familles', name: 'listFamilies')]
    public function listFamilies(): Response
    {
        return $this->render('account/list_families.html.twig');
    }

    #[Route('/mon-compte/mes-familles/membres', name: 'listFamiliesMembers')]
    public function listFamiliesMembers(): Response
    {
        $currentUser = $this->getUser();

        $families = $this->getUser()->getFamilies();
        $members = [];
        foreach ($families as $family){
            foreach ($family->getUsers() as $user){
                if($user !== $currentUser){
                    $members[$user->getId()] =$user;
                }
            }
        }

        return $this->render('account/list_families_members.html.twig',[
            'members' => $members,
        ]);
    }


    /********************************************/
    /**************CHILDREN*********************/
    /******************************************/

    #[Route('/mon-compte/mes-enfants', name: 'listChildren')]
    public function listChildren(): Response
    {
        return $this->render('account/list_children.html.twig');
    }

    #[Route('/mon-compte/mes-enfants/ajouter', name: 'addChildren')]
    public function addChildren(Request $request): Response
    {

        $child = new Child();
        $form = $this->createForm(ChildFormType::class, $child);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->getUser()->addChild($child);
            $em->persist($child);
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('success', 'L\'enfant a été ajouté.');
            return  $this->redirectToRoute('listChildren');
        }

        return $this->render('account/add_children.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/mes-enfants/supprimer/{id}', name: 'deleteChild')]
    public function deleteChild(Child $child): Response
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($child->getGifts() as $gift){
            $em->remove($gift);
        }
        $em->remove($child);
        $em->flush();

        $this->addFlash('success', 'L\'enfant a été retiré de votre liste');

        return $this->redirectToRoute("listChildren");
    }

    /********************************************/
    /******************GIFT*********************/
    /******************************************/

    #[Route('/mon-compte/demander-un-cadeau', name: 'askGift')]
    public function askGift(Request $request): Response
    {
        $gift = new Gift();
        $form = $this->createForm(GiftFormType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

//            $this->getUser()->addGift($gift);
            $gift->setAlreadyBuy(false);
            $em->persist($gift);
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été ajouté à votre liste');
            return $this->redirectToRoute("viewAllList", ['id'=> $this->getUser()->getId()]);
        }


        return $this->render('account/ask_gift.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/modifier-un-cadeau/{id}', name: 'editGift')]
    public function editGift(Request $request, Gift $gift): Response
    {
        //TODO: VOTER TO CHECK IF IS MINE
        $form = $this->createForm(GiftFormType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gift);
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été modifié');
            return $this->redirectToRoute("viewAllList", ['id'=> $this->getUser()->getId()]);
        }


        return $this->render('account/ask_gift.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/mon-compte/voir-une-liste/{id}/acheter-un-cadeau/{gift}', name: 'buyGift')]
    public function buyGift(User $user, Gift $gift): Response
    {
        $gift->setAlreadyBuy(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($gift);
        $em->flush();
        return $this->redirectToRoute('viewOneList', ['id' => $user->getId()]);
    }


    /********************************************/
    /******************LISTS*********************/
    /******************************************/

    #[Route('/mon-compte/creer-une-liste', name: 'createList')]
    public function createList(Request $request){
        $giftGroup = new GiftGroup();
        $form = $this->createForm(GiftGroupFormType::class, $giftGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->getUser()->addGiftGroup($giftGroup);
            $em->persist($giftGroup);

            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('success', 'La liste a été crée');
            return  $this->redirectToRoute('viewAllList', ['id' => $this->getUser()->getId()]);
        }


        return $this->render('account/ask_list.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/voir-les-listes/{id}', name: 'viewAllList')]
    public function viewAllList(User $user): Response
    {
        $hasRight = false;
        $families  = $this->getUser()->getFamilies();
        foreach ($families as $family){
            foreach ($family->getUsers() as $member){
                if($member === $user){
                    $hasRight=true;
                }
            }
        }

        if(!$hasRight){
            throw new AccessDeniedException("Vous n'avez pas le droit d'accéder aux listes de cette personne");
        }

        return $this->render('account/list_giftsGroup.html.twig',[
            'user' => $user
        ]);
    }


    #[Route('/mon-compte/voir-les-listes/enfant/{id}', name: 'viewChildAllList')]
    public function viewChildAllList(Child $child): Response
    {
        $hasRight = false;
        $families  = $this->getUser()->getFamilies();

        $user = $child->getParent();
        foreach ($families as $family){
            foreach ($family->getUsers() as $member){
                if($member === $user){
                    $hasRight=true;
                }
            }
        }

        if(!$hasRight){
            throw new AccessDeniedException("Vous n'avez pas le droit d'accéder à la liste de cette personne");
        }

        return $this->render('account/list_giftsGroup.html.twig',[
            'user' => $child
        ]);
    }

    #[Route('/mon-compte/voir-une-liste/{id}', name: 'viewOneList')]
    public function viewOneList(GiftGroup $giftGroup): Response
    {
        $user = $giftGroup->getAskBy();

        $hasRight = false;
        $families  = $this->getUser()->getFamilies();
        foreach ($families as $family){
            foreach ($family->getUsers() as $member){
                if($member === $user){
                    $hasRight=true;
                }
            }
        }

        if(!$hasRight){
            throw new AccessDeniedException("Vous n'avez pas le droit d'accéder à la liste de cette personne");
        }

        return $this->render('account/list_gifts.html.twig',[
            'user' => $user,
            'gifts_group' =>  $giftGroup
        ]);
    }

    #[Route('/mon-compte/voir-une-liste/enfant/{id}', name: 'viewChildList')]
    public function viewChildList(GiftGroup $giftGroup): Response
    {

        $child = $giftGroup->getChild();
        $hasRight = false;
        $families  = $this->getUser()->getFamilies();

        $user = $child->getParent();
        foreach ($families as $family){
            foreach ($family->getUsers() as $member){
                if($member === $user){
                    $hasRight=true;
                }
            }
        }

        if(!$hasRight){
            throw new AccessDeniedException("Vous n'avez pas le droit d'accéder à la liste de cette personne");
        }

        return $this->render('account/list_gifts.html.twig',[
            'user' => $child,
            'gifts_group' =>  $giftGroup
        ]);
    }



}
