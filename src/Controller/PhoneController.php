<?php

namespace App\Controller;

use App\Entity\Phone;
use OA\ExternalDocumentation;
use OpenApi\Annotations as OA;
use App\Service\PaginationHandler;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     description="The page number to get back, page 1 is used by default if nothing is specified in the url query.",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     required=false,
     *     description="The object number to get back, limit 20 is used by default if nothing is specified in the url query.",
     *     @OA\Schema(type="integer")
     * )
     * @OA\ExternalDocumentation(
     *     url="https://example.com/api/customers?page=1&limit=10",
     *     description="Example request"
     * )
     * @OA\Tag(name="Phones")
     * 
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
    #[Route('/phones', name: 'phones_list', methods: ['GET'])]
    public function getPhonesList(PhoneRepository $phoneRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool, PaginatorInterface $paginator, Request $request, PaginationHandler $paginationHandler): JsonResponse
    {
        $page = $request->query->getInt('page',1);
        $limit = $request->query->getInt('limit', 20);
        $idCache = "getAllPhones-P".$page."-L".$limit;

        $knpPhonesList = $cachePool->get($idCache, function (ItemInterface $item) use ($phoneRepository, $paginator, $request) {
            $item->tag("phonesCache");
            $item->expiresAfter(3600);
            $phonesList = $paginator->paginate(
                $phoneRepository->findAll(),
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 20)
            );
            
            return $phonesList;
        });
        $jsonPhonesList = $serializer->serialize($knpPhonesList->getItems(), 'json');

        $phoneNumberGet = count($knpPhonesList->getItems());
        $paginationHandler->isPhonePageEmpty($phoneNumberGet, $phoneRepository->count([]), $page, $limit);

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
        $jsonPhoneDetail = $cachePool->get($idCache, function (ItemInterface $item) use ($phone, $serializer) {
            $item->tag("phonesCache-".$phone->getId());
            $item->expiresAfter(3600);
            return $serializer->serialize($phone, 'json');
        });

        return new JsonResponse($jsonPhoneDetail, Response::HTTP_OK, [], true);
    }
}
