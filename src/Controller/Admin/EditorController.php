<?php
namespace App\Controller\Admin;
use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin/editor')]
class EditorController extends AdminController
{
    #[Route('/', name: 'admin_editor_index', methods: ['GET'])]
    public function index(EditorRepository $editorRepository): Response
    {
        return $this->render('admin/editor/index.html.twig', [
            'editors' => $editorRepository->findAllOrdered(),
        ]);
    }
    #[Route('/new', name: 'admin_editor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $editor = new Editor();
        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($editor);
            $entityManager->flush();
            $this->addFlash('success', 'Editor created successfully!');
            return $this->redirectToRoute('admin_editor_index');
        }
        return $this->render('admin/editor/new.html.twig', [
            'editor' => $editor,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'admin_editor_show', methods: ['GET'])]
    public function show(Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }
    #[Route('/{id}/edit', name: 'admin_editor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Editor $editor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Editor updated successfully!');
            return $this->redirectToRoute('admin_editor_index');
        }
        return $this->render('admin/editor/edit.html.twig', [
            'editor' => $editor,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'admin_editor_delete', methods: ['POST'])]
    public function delete(Request $request, Editor $editor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$editor->getId(), $request->request->get('_token'))) {
            $entityManager->remove($editor);
            $entityManager->flush();
            $this->addFlash('success', 'Editor deleted successfully!');
        }
        return $this->redirectToRoute('admin_editor_index');
    }
}
