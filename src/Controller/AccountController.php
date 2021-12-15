<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Family;
use App\Entity\Gift;
use App\Entity\GiftGroup;
use App\Entity\Pot;
use App\Entity\User;
use App\Form\ChildFormType;
use App\Form\FamilyFormType;
use App\Form\GiftFormType;
use App\Form\GiftGroupFormType;
use App\Form\PotFormType;
use App\Form\UserFamilyFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
        return $this->redirectToRoute("viewCurrentList");
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
        $this->denyAccessUnlessGranted('EDIT', $child);

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
            $gift->setUser($this->getUser());
            $gift->setAlreadyBuy(false);
            $em->persist($gift);
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été ajouté à votre liste');
            return $this->redirectToRoute("viewMyGifts");
        }


        return $this->render('account/ask_gift.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/modifier-un-cadeau/{id}', name: 'editGift')]
    public function editGift(Request $request, Gift $gift, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $gift);

        $form = $this->createForm(GiftFormType::class, $gift);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($gift);
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été modifié');
            return $this->redirectToRoute("viewMyGifts");
        }


        return $this->render('account/ask_gift.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/mon-compte/voir-mes-cadeaux/supprimer/{id}', name: 'deleteGift')]
    public function deleteGift(Gift $gift): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $gift);

        if(!$gift->getGiftGroup()->isEmpty()){
            $this->addFlash('error', 'Vous n\'avez pas le droit de supprimer un cadeau présent dans une liste. Supprimez d\'abord la liste.');
        } else{
            $em = $this->getDoctrine()->getManager();

            $em->remove($gift);
            $em->flush();

            $this->addFlash('success', 'Le cadeau a été supprimé');
        }

        return $this->redirectToRoute("viewMyGifts");
    }

    #[Route('/mon-compte/voir-une-liste/{id}/acheter-un-cadeau/{gift}', name: 'buyGift')]
    public function buyGift(User $user, Gift $gift, Session $session): Response
    {
        $gift->setBuyBy($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($gift);
        $em->flush();
//TODO: add redirect custom, maybe session variable to put url
        return $this->redirectToRoute($session->get('refererBuyGift')['path'], ['id' => $session->get('refererBuyGift')['giftGroup']]);
    }

    #[Route('/mon-compte/voir-mes-cadeaux', name: 'viewMyGifts')]
    public function viewMyGifts(Request $request): Response
    {

        $pot = new Pot();
        $form = $this->createForm(PotFormType::class, $pot);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->changePot($form, $pot);
        }

        return $this->render('account/list_gifts.html.twig',[
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }


    /********************************************/
    /******************LISTS*********************/
    /******************************************/

    #[Route('/mon-compte/listes-en-cours', name: 'viewCurrentList')]
    public function viewCurrentList(): Response
    {
        $families = $this->getUser()->getFamilies();
        $giftGroups = [];
        foreach ($families as $family){
            $members = $family->getUsers();
            foreach ($members as $member){
                $lists = $member->getGiftGroups();
                foreach($lists as $list){
                    if($list->getExpireDate() > new DateTime()){
                        $giftGroups[$list->getId()] = $list;
                    }
                }
            }
        }


        return $this->render('account/list_giftsGroup.html.twig',[
            'giftGroups' => $giftGroups
        ]);
    }

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
            $this->addFlash('success', 'La liste a été créée');

            if($form->getData()->getChild() != null){
                return  $this->redirectToRoute('viewChildAllList', ['id' => $form->getData()->getChild()->getId()]);
            }
            return  $this->redirectToRoute('viewAllList', ['id' => $this->getUser()->getId()]);
        }


        return $this->render('account/ask_list.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/voir-les-listes/supprimer/{id}', name: 'deleteList')]
    public function deleteList(GiftGroup $giftGroup): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $giftGroup);

        if($giftGroup->getExpireDate() > new DateTime()){
            $this->addFlash('error', 'Vous ne pouvez pas supprimer une liste qui n\'est pas expirée.');
        } else{
            $em = $this->getDoctrine()->getManager();

            $em->remove($giftGroup);
            $em->flush();

            $this->addFlash('success', 'La liste a été supprimée');
        }

        return  $this->redirectToRoute('viewAllList', ['id' => $this->getUser()->getId()]);
    }

    #[Route('/mon-compte/voir-les-listes/{id}', name: 'viewAllList')]
    public function viewAllList(User $user): Response
    {
        $this->denyAccessUnlessGranted('LIST', $user);

        return $this->render('account/list_giftsGroup.html.twig',[
            'giftGroups' => $user->getGiftGroups(),
            'user' => $user
        ]);
    }


    #[Route('/mon-compte/voir-les-listes/enfant/{id}', name: 'viewChildAllList')]
    public function viewChildAllList(Child $child): Response
    {
        $user = $child->getParent();
        $this->denyAccessUnlessGranted('LIST', $user);

        return $this->render('account/list_giftsGroup.html.twig',[
            'giftGroups' => $child->getGiftGroups(),
            'user' => $child
        ]);
    }

    #[Route('/mon-compte/voir-une-liste/{id}', name: 'viewOneList')]
    public function viewOneList(GiftGroup $giftGroup, Request $request, Session $session): Response
    {
        $user = $giftGroup->getAskBy();
        $session->set('refererBuyGift', [
                'path' =>  'viewOneList',
                'giftGroup' => $giftGroup->getId()
            ]
        );

        return $this->viewList($giftGroup, $request, $user);
    }

    #[Route('/mon-compte/voir-une-liste/enfant/{id}', name: 'viewChildList')]
    public function viewChildList(GiftGroup $giftGroup, Request $request, Session $session): Response
    {
        $child = $giftGroup->getChild();
        $user = $child->getParent();

        $session->set('refererBuyGift', [
                'path' =>  'viewChildList',
                'giftGroup' => $giftGroup->getId()
            ]
        );

        return $this->viewList($giftGroup, $request, $user);
    }

    /**
     * @param FormInterface $form
     * @param Pot $pot
     */
    public function changePot(FormInterface $form, Pot $pot): void
    {
        $em = $this->getDoctrine()->getManager();

        $potDB = $this->getDoctrine()
            ->getRepository(Pot::class)
            ->findOneBy([
                "gift" => $form->get('id')->getData(),
                "user" => $this->getUser()->getId()
            ]);

        if ($potDB) {
            $potDB->setAmount($form->getData()->getAmount());
            if ($form->getData()->getAmount() > 0) {
                $em->persist($potDB);
            } else {
                $em->remove($potDB);
            }
        } else {
            $gift = $this->getDoctrine()
                ->getRepository(Gift::class)
                ->findOneBy(['id' => $form->get('id')->getData()]);
            $pot->setUser($this->getUser());
            $pot->setGift($gift);
            $em->persist($pot);
        }

        $em->flush();
        $this->addFlash('success', 'Votre participation a été prise en compte');
    }

    /**
     * @param GiftGroup $giftGroup
     * @param Request $request
     * @param User|null $user
     * @return Response
     */
    public function viewList(GiftGroup $giftGroup, Request $request, ?User $user): Response
    {
        $this->denyAccessUnlessGranted('LIST', $user);

        $pot = new Pot();
        $form = $this->createForm(PotFormType::class, $pot);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->changePot($form, $pot);
        }

        return $this->render('account/list_gifts.html.twig', [
            'user' => $user,
            'gifts_group' => $giftGroup,
            'form' => $form->createView()
        ]);
    }


}
