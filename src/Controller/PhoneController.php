<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class PhoneController extends AbstractController
{
    #[Route('/phones', name: 'phones_list', methods: ['GET'])]
    public function getPhonesList(PhoneRepository $phoneRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $idCache = "getAllPhones";
        echo($idCache);
        $jsonPhonesList = $cachePool->get($idCache, function (ItemInterface $item) use ($phoneRepository, $serializer) {
            // ligne pour tester only, à enlever après
            echo("Recherche pas encore en cache");
            $item->tag("phonesCache");
            $phonesList = $phoneRepository->findAll();
            return $serializer->serialize($phonesList, 'json');
        });

        return new JsonResponse($jsonPhonesList, Response::HTTP_OK, [], true);
    }

    #[Route('/phones/{id}', name: 'phone_details', methods: ['GET'])]
    public function getPhoneDetails(Phone $phone, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $idCache = "getPhoneDetails-".$phone->getId();
        echo($idCache);
        $jsonPhoneDetail = $cachePool->get($idCache, function (ItemInterface $item) use ($phone, $serializer) {
            // ligne pour tester only, à enlever après
            echo("Recherche pas encore en cache");
            $item->tag("phonesCache-".$phone->getId());
            return $serializer->serialize($phone, 'json');
        });

        return new JsonResponse($jsonPhoneDetail, Response::HTTP_OK, [], true);
    }
}
