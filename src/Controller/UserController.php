<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    private UserPasswordHasherInterface $passwordHasher;
    private array $encoders;
    /**
     * @var ObjectNormalizer[]
     */
    private array $normalizers;
    private Serializer $serializer;
    private ValidatorInterface $validator;
    private ManagerRegistry $doctrine;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $this->passwordHasher = $passwordHasher;
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($this->normalizers, $this->encoders);
        $this->validator = $validator;
        $this->doctrine = $doctrine;
    }

    #[Route('/user', name: 'user_create')]
    public function userCreate(Request $request)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $user = $this->serializer->denormalize(json_decode($request->getContent()), User::class);
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                return new Response((string)$errors, 400);
            }
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);

            $entityManager->flush();

            return new JsonResponse([], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'error' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ]
            ], Response::HTTP_CREATED);
        }
    }
}
