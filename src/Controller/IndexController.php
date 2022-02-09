<?php

namespace App\Controller;

use Casisdead2772\PostcodeBundle\Service\PublicApi\UK\postcodesApiService\PostcodesApiService;
use Casisdead2772\PostcodeBundle\PostcodeServiceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PostcodesApiService $apiPostcodeGetter, PostcodeServiceManager $factory): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/getinfo', name: 'postcodeinfo')]
    public function showPostcodeInfo(Request $request, PostcodesApiService $apiPostcodeGetter, PostcodeServiceManager $factory)
    {
        $service = $factory->findByType('postcodes.io');
        $postcode = $request->query->get('postcode');

        if ($postcode && $service) {
            try {
                $address = $service->getAddress($postcode);

                return $this->render('postcode/postcode.html.twig', [
                    'address' => $address
                ]);
            } catch (\Throwable $exception) {
                $errors = $exception->getMessage();
                $this->addFlash('danger', $errors);

                return $this->redirectToRoute('index');
            }
        }
        $this->addFlash('danger', 'Something went wrong');

        return $this->redirectToRoute('index');
    }
}
