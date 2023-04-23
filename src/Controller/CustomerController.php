<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un de vos clients.')]
    public function deleteCustomer(#[MapEntity(id: 'id')] User $user, #[MapEntity(id: 'customer_id')] Customer $customer, CustomerRepository $customerRepository): JsonResponse
    {
        // $customerToDelete = $customerRepository->findUserCustomerDetails($user, $customer_id);
        $customerRepository->remove($customer, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/customers', name: 'create_customer', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un client.')]
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
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour mettre à jour un de vos clients.')]
    public function updateCustomer(#[MapEntity(id: 'id')] User $user, #[MapEntity(id: 'customer_id')] Customer $currentCustomer, Request $request, CustomerRepository $customerRepository, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $updatedCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
       
        // We check the customer datas
        $errors = $validator->validate($updatedCustomer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $currentCustomer->update($updatedCustomer);
        $customerRepository->save($currentCustomer, true);

        $location = $urlGenerator->generate('customer_details', ['id' => $currentCustomer->getMarketplace()->getId(), 'customer_id' => $currentCustomer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, ["Location" => $location]);
    }
}
