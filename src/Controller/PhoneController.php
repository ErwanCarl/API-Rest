<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class PhoneController extends AbstractController
{
    #[Route('/phones', name: 'phones_list', methods: ['GET'])]
    public function getPhonesList(PhoneRepository $phoneRepository, SerializerInterface $serializer): JsonResponse
    {
        $phonesList = $phoneRepository->findAll();
        $jsonPhonesList = $serializer->serialize($phonesList, 'json') ;
        return new JsonResponse($jsonPhonesList, Response::HTTP_OK, [], true);
    }

    #[Route('/phones/{id}', name: 'phone_details', methods: ['GET'])]
    public function getPhoneDetails(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $jsonPhoneDetail = $serializer->serialize($phone, 'json') ;
        return new JsonResponse($jsonPhoneDetail, Response::HTTP_OK, [], true);
    }
}
