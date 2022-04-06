<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use App\Repository\NurseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class GalleryController extends AbstractController
{
    private SluggerInterface $slugger;
    private NurseRepository $nurseRepository;
    private ManagerRegistry $doctrine;
    private GalleryRepository $galleryRepository;

    public function __construct(SluggerInterface  $slugger,
                                NurseRepository   $nurseRepository,
                                ManagerRegistry   $doctrine,
                                GalleryRepository $galleryRepository)
    {
        $this->slugger = $slugger;
        $this->nurseRepository = $nurseRepository;
        $this->doctrine = $doctrine;
        $this->galleryRepository = $galleryRepository;
    }

    #[IsGranted('ROLE_NURSE', message: 'Vous ne pouvez pas faire ça')]
    #[Route('/gallery/{nurseId}', name: 'app_gallery', methods: 'POST')]
    public function index(Request $request, int $nurseId): JsonResponse
    {
        $files = $request->files;
        $nurse = $this->nurseRepository->findOneBy(['id' => $nurseId]);
        $entityManager = $this->doctrine->getManager();

        /* @var UploadedFile $file */
        foreach ($files as $file) {
            $safeFilename = $this->slugger->slug($file->getClientOriginalName());
            $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            $photo = (new Gallery())->setUrl($fileName)->setNurse($nurse);
            $entityManager->persist($photo);
            $entityManager->flush();

            try {
                $file->move($this->getParameter('gallery_directory').'/'.$nurseId, $fileName);
            } catch (FileException $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $this->json([], Response::HTTP_CREATED);
    }

    #[IsGranted('ROLE_NURSE', message: 'Vous ne pouvez pas faire ça')]
    #[Route('/gallery/nurse/{nurseId}', name: 'app_gallery_get', methods: 'GET')]
    public function gallery(int $nurseId): JsonResponse
    {
        $photos = $this->galleryRepository->findBy(['nurse' => $nurseId]);
        return $this->json($photos, Response::HTTP_CREATED, [], ['groups' => 'gallery']);
    }

    #[IsGranted('ROLE_NURSE', message: 'Vous ne pouvez pas faire ça')]
    #[Route('/gallery/{galleryId}', name: 'app_gallery_delete', methods: 'DELETE')]
    public function delete(int $galleryId): JsonResponse
    {
        $entityManager = $this->doctrine->getManager();
        $photo = $this->galleryRepository->findOneBy(['id' => $galleryId]);
        $entityManager->remove($photo);
        $entityManager->flush();
        return $this->json([], Response::HTTP_NO_CONTENT);
    }

}
