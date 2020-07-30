<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEntryType;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /** @var EntityManagerInterface  */
    private $entityManager;
    /** @var Auth  */
    private $auth;

    /**
     * UserController constructor.
     * @param EntityManagerInterface $entityManager
     * @param Auth $auth
     */
    public function __construct(EntityManagerInterface $entityManager, Auth $auth)
    {
        $this->entityManager = $entityManager;
        $this->auth = $auth;
    }

    /**
     * @Route("/user", name="user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserEntryType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRecord = $this->auth->createUserWithEmailAndPassword($user->getEmail(), $user->getPassword());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }
}
