<?php
namespace App\Controller\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_AGENT')]
abstract class AdminController extends AbstractController
{
}
