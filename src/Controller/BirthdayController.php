<?php
namespace App\Controller;

use App\Entity\Birthday;
use App\Repository\BirthdayRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/birthday')]
final class BirthdayController extends AbstractController
{
    #[Route('', name: 'app_birthday_index', methods: ['GET'])]
    public function index(BirthdayRepository $birthdayRepository, SerializerInterface $serializer): Response
    {
        $birthdays = $birthdayRepository->findAll();
        $json = $serializer->serialize($birthdays, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('', name: 'app_birthday_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $birthday = $serializer->deserialize($request->getContent(), Birthday::class, 'json');

        $entityManager->persist($birthday);
        $entityManager->flush();

        $json = $serializer->serialize($birthday, 'json');
        return new JsonResponse($json, 201, [], true);
    }

    #[Route('/{id}', name: 'app_birthday_show', methods: ['GET'])]
    public function show(Birthday $birthday, SerializerInterface $serializer): Response
    {
        $json = $serializer->serialize($birthday, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'app_birthday_edit', methods: ['PATCH'])]
    public function edit(Request $request, Birthday $birthday, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $temp = $serializer->deserialize($request->getContent(), Birthday::class, 'json');

        if ($temp->getName()) {
            $birthday->setName($temp->getName());
        }

        if ($temp->getBirthday()) {
            $birthday->setBirthday($temp->getBirthday());
        }

        $entityManager->flush();

        $json = $serializer->serialize($birthday, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'app_birthday_delete', methods: ['DELETE'])]
    public function delete(Birthday $birthday, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($birthday);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Birthday deleted'], 200);
    }
}
