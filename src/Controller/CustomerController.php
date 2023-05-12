<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

#[Route('/api/users')]
class CustomerController extends AbstractController
{
    /**
     * To get all the customers linked to a Marketplace
     * 
     * @OA\Response(
     *     response=200,
     *     description="Get back all the customers linked to a Marketplace",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomerDetails"}))
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
     * @OA\Tag(name="Customers")
     *
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
    #[Route('/customers', name: 'customers_list', methods: ['GET'])]
    public function getCustomersList(CustomerRepository $customerRepository, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $idCache = "getAllCustomers-user-".$user->getId();
        echo($idCache); 
        $jsonCustomersList = $cachePool->get($idCache, function (ItemInterface $item) use ($customerRepository, $user, $serializer) {
            // ligne pour tester only, à enlever après
            echo("Recherche pas encore en cache");
            $item->tag("customersCache");

            $customersList = $customerRepository->findUserCustomers($user);
            $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
            return $serializer->serialize($customersList, 'json', $context);
        });

        return new JsonResponse($jsonCustomersList, Response::HTTP_OK, [], true);
    }

    /**
     * To get a specific customer details linked to a Marketplace
     * 
     * @OA\Response(
     *     response=200,
     *     description="Get back a specific customer details linked to a Marketplace",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomerDetails"}))
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
     *     response=403,
     *     description="Access denied, the identified customer is not linked to your Marketplace",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=403),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Not found : wrong url, resource not found or does not exist",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=404),
     *        @OA\Property(property="message", type="string"),
     *        type="object"
     *     )
     * )
     * @OA\Parameter(
     *     name="customer_id",
     *     in="path",
     *     description="The customer identifier",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Customers")
     *
     * @param Customer $customer
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
    #[Route('/customers/{customer_id}', name: 'customer_details', methods: ['GET'])] 
    public function getCustomerDetails(#[MapEntity(id: 'customer_id')] Customer $customer, SerializerInterface $serializer, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $this->denyAccessUnlessGranted('view', $customer);

        $idCache = "getCustomerDetails-".$customer->getId();
        echo($idCache); 

        $jsonCustomerDetails = $cachePool->get($idCache, function (ItemInterface $item) use ($customer, $serializer) {
            // ligne pour tester only, à enlever après
            echo("Recherche pas encore en cache");
            $item->tag("customersCache-".$customer->getId());
            $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
            return $serializer->serialize($customer, 'json', $context);
        });

        return new JsonResponse($jsonCustomerDetails, Response::HTTP_OK, [], true);
    }

    /**
     * To delete a specific customer
     * 
     * @OA\Response(
     *     response=204,
     *     description="Delete a specific customer"
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
     *     response=403,
     *     description="Access denied, either the identified customer is not linked to your Marketplace or you don't have the admin role",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=403),
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
     *     name="customer_id",
     *     in="path",
     *     description="The customer identifier",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Customers")
     *
     * @param Customer $customer
     * @param CustomerRepository $customerRepository
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
    #[Route('/customers/{customer_id}', name: 'delete_customer', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un de vos clients.')]
    public function deleteCustomer(#[MapEntity(id: 'customer_id')] Customer $customer, CustomerRepository $customerRepository, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $this->denyAccessUnlessGranted('delete', $customer);

        $cachePool->invalidateTags(["customersCache","customersCache-".$customer->getId()]);
        $customerRepository->remove($customer, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * To create a customer
     * 
     * @OA\Response(
     *     response=201,
     *     description="Create a customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomerDetails"}))
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request, one or more property fields of your sent json body contains errors or are missing. Please refer to the Customer object schemas in the API documentation",
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
     *     response=403,
     *     description="Access denied, you don't have the admin role",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=403),
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
     * @OA\Tag(name="Customers")
     *
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @param ValidatorInterface $validator
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/customers', name: 'create_customer', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un client.')]
    public function createCustomer(Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $cachePool->invalidateTags(["customersCache"]);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setMarketPlace($user);

        // We check the customer datas
        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $customerRepository->save($customer, true);
        
        $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        $location = $urlGenerator->generate('customer_details', ['id' => $customer->getMarketplace()->getId(), 'customer_id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * To update a customer
     * 
     * @OA\Response(
     *     response=204,
     *     description="Update a customer"
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request, either there are errors in parameters and url wildcards or one or more property fields of your sent json body contains errors or are missing. In this case, please refer to the Customer object schemas in the API documentation",
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
     *     response=403,
     *     description="Access denied, either the identified customer is not linked to your Marketplace or you don't have the admin role",
     *     @OA\JsonContent(
     *        @OA\Property(property="code", type="integer", example=403),
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
     *  @OA\Parameter(
     *     name="customer_id",
     *     in="path",
     *     description="The customer identifier",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="Customers")
     *
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @param ValidatorInterface $validator
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/customers/{customer_id}', name: 'update_customer', methods: ['PUT'])] 
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour mettre à jour un de vos clients.')]
    public function updateCustomer(#[MapEntity(id: 'customer_id')] Customer $currentCustomer, Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $this->denyAccessUnlessGranted('edit', $currentCustomer);

        $cachePool->invalidateTags(["customersCache","customersCache-".$currentCustomer->getId()]);

        $updatedCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $currentCustomer->update($updatedCustomer);
       
        // We check the customer datas
        $errors = $validator->validate($currentCustomer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $customerRepository->save($currentCustomer, true);

        $location = $urlGenerator->generate('customer_details', ['id' => $currentCustomer->getMarketplace()->getId(), 'customer_id' => $currentCustomer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, ["Location" => $location]);
    }
}
