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
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users')]
class CustomerController extends AbstractController
{
    #[Route('/{id}/customers', name: 'customers_list', methods: ['GET'])]
    public function getCustomersList(User $user, CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customersList = $customerRepository->findUserCustomers($user);

        $context = (new SerializationContext())->setGroups(['getCustomers']);
        $jsonCustomersList = $serializer->serialize($customersList, 'json', $context);

        return new JsonResponse($jsonCustomersList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/customers/{customer_id}', name: 'customer_details', methods: ['GET'])] 
    public function getCustomerDetails(#[MapEntity(id: 'id')] User $user, #[MapEntity(id: 'customer_id')] Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        // $customerDetails = $customerRepository->findUserCustomerDetails($user, $customer_id);

        $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
        $jsonCustomerDetails = $serializer->serialize($customer, 'json', $context);

        return new JsonResponse($jsonCustomerDetails, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/customers/{customer_id}', name: 'delete_customer', methods: ['DELETE'])]
    public function deleteCustomer(#[MapEntity(id: 'id')] User $user, #[MapEntity(id: 'customer_id')] Customer $customer, CustomerRepository $customerRepository): JsonResponse
    {
        // $customerToDelete = $customerRepository->findUserCustomerDetails($user, $customer_id);
        
        $customerRepository->remove($customer, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/customers', name: 'create_customer', methods: ['POST'])]
    public function createCustomer(User $user, Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
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

    #[Route('/{id}/customers/{customer_id}', name: 'update_customer', methods: ['PUT'])] 
    public function updateCustomer(#[MapEntity(id: 'id')] User $user, #[MapEntity(id: 'customer_id')] Customer $currentCustomer, Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        // $currentCustomer = $customerRepository->findUserCustomerDetails($user, $customer_id);

        // $contextDes = (DeserializationContext::create())->setAttribute(AbstractNormalizer::OBJECT_TO_POPULATE, $currentCustomer);
        $updatedCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $updatedCustomer->setMarketplace($user);

        // We check the customer datas
        $errors = $validator->validate($updatedCustomer);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $customerRepository->save($updatedCustomer, true);
        
        $context = (new SerializationContext())->setGroups(['getCustomerDetails']);
        $jsonUpdatedCustomer = $serializer->serialize($updatedCustomer, 'json', $context);

        $location = $urlGenerator->generate('customer_details', ['id' => $updatedCustomer->getMarketplace()->getId(), 'customer_id' => $updatedCustomer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUpdatedCustomer, Response::HTTP_NO_CONTENT, ["Location" => $location], true);
    }
}
