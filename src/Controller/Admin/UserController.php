<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserRolesType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AdminController
{
    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['email' => 'ASC']);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Prevent editing own account
        if ($user === $this->getUser()) {
            $this->addFlash('warning', 'You cannot edit your own roles. Ask another administrator.');
            return $this->redirectToRoute('admin_user_index');
        }

        $form = $this->createForm(UserRolesType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'User roles updated successfully!');
            return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/reset-password', name: 'admin_user_reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->isCsrfTokenValid('reset-password'.$user->getId(), $request->request->get('_token'))) {
            // Generate a random password
            $newPassword = bin2hex(random_bytes(8));

            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Password reset successfully! New temporary password: <strong>%s</strong><br>Please share this with the user and ask them to change it.',
                $newPassword
            ));
        }

        return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
    }
}

