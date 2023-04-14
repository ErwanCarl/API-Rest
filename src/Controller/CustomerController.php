<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    #[Route('/api/users/{id}/customers', name: 'customers_list', methods: ['GET'])]
    public function getCustomersList(User $user, CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customersList = $customerRepository->findUserCustomers($user);

        $context = (new SerializationContext())->setGroups(['getCustomers']);
        $jsonCustomersList = $serializer->serialize($customersList, 'json', $context);

        return new JsonResponse($jsonCustomersList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}/customers/{customer_id}', name: 'customer_details', methods: ['GET'])] 
    // #[ParamConverter('user', options: ['mapping' => ['id' => 'id']])]
    public function getCustomerDetails(User $user, int $customer_id, CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customerDetails = $customerRepository->findUserCustomerDetails($user, $customer_id);
        
        $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
        $jsonCustomerDetails = $serializer->serialize($customerDetails, 'json', $context);

        return new JsonResponse($jsonCustomerDetails, Response::HTTP_OK, [], true);
    }

    #[Route('/api/users/{id}/customers/{customer_id}', name: 'delete_customer', methods: ['DELETE'])]
    public function deleteCustomer(User $user, int $customer_id, CustomerRepository $customerRepository): JsonResponse
    {
        $customerToDelete = $customerRepository->findUserCustomerDetails($user, $customer_id);
        $customerRepository->remove($customerToDelete, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/users/{id}/customers', name: 'create_customer', methods: ['POST'])]
    public function createCustomer(User $user, Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setMarketPlace($user);
        $customerRepository->save($customer, true);
        
        $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        $location = $urlGenerator->generate('customer_details', ['id' => $customer->getMarketplace()->getId(), 'customer_id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    // #[Route('/api/users/{id}/customers/{customer_id}', name: 'update_customer', methods: ['PUT'])] 
    // public function updateCustomer(User $user, int $customer_id, Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
    // {
    //     $currentCustomer = $customerRepository->findUserCustomerDetails($user, $customer_id);

    //     $context = (DeserializationContext::create())->setAttribute(AbstractNormalizer::OBJECT_TO_POPULATE, $currentCustomer);
    //     $updatedCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json', $context);

    //     $customerRepository->save($updatedCustomer, true);
        
    //     $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
    //     $jsonUpdatedCustomer = $serializer->serialize($updatedCustomer, 'json', $context);

    //     $location = $urlGenerator->generate('customer_details', ['id' => $updatedCustomer->getMarketplace()->getId(), 'customer_id' => $updatedCustomer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    //     return new JsonResponse($jsonUpdatedCustomer, Response::HTTP_NO_CONTENT, ["Location" => $location], true);
    // }
}
