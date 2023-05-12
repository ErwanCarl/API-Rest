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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api')]
class PhoneController extends AbstractController
{
    /**
     * To get all the phones list.
     *
     * @OA\Response(
     *     response=200,
     *     description="Get all the phones list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized, you have to authenticate with the token",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=401),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found : wrong url",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=404),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     * @OA\Tag(name="Phones")
     * 
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
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

    /**
     * To get a specific phone details
     * 
     * @OA\Response(
     *     response=200,
     *     description="Get back a specific phone details",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request, check parameters and url wildcards",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=400),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized, you have to authenticate with the token",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=401),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found : wrong url, resource not found or does not exist",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=404),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="The phone identifier",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Phones")
     *
     * @param Phone $phone
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
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
