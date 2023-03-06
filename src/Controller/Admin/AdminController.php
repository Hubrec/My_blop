<?php

namespace App\Controller\Admin;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/{name}", methods={"GET"})
     */
    public function index(LoggerInterface $logger, string $name): Response
    {
        $logger->info('Admin/'. $name . ' page is being accessed');

        return $this -> render('admin/base.admin.html.twig', [
            'name' => $name,
        ]);
    }
}

?>