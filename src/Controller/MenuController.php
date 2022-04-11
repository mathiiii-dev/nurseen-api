<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Nurse;
use App\Repository\MenuRepository;
use App\Repository\NurseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private NurseRepository $nurseRepository;
    private MenuRepository $menuRepository;

    public function __construct(ManagerRegistry $doctrine, NurseRepository $nurseRepository, MenuRepository $menuRepository)
    {
        $this->doctrine = $doctrine;
        $this->nurseRepository = $nurseRepository;
        $this->menuRepository = $menuRepository;
    }

    /**
     * @throws Exception
     */
    #[Route('/menu/add/{nurseId}', name: 'app_menu_add')]
    public function add(Request $request, int $nurseId): Response
    {
        $data = $request->toArray();
        $nurse = $this->nurseRepository->findOneBy(['nurse' => $nurseId]);
        $menu = (new Menu())->setDate(new \DateTime($data['date']))->setDessert($data['dessert'])->setEntry($data['entry'])->setMeal($data['meal'])->setNurse($nurse);

        $entityManager = $this->doctrine->getManager();

        $entityManager->persist($menu);
        $entityManager->flush();

        return $this->json([], Response::HTTP_CREATED);
    }

    #[Route('/menu/{nurseId}', name: 'app_menu_get')]
    public function get(int $nurseId): Response
    {
        $nurse = $this->nurseRepository->findOneBy(['nurse' => $nurseId]);
        $menu = $this->menuRepository->findOneBy([
            'date' => (new \DateTime())->modify('-1 day'),
            'nurse' => $nurse->getId()
        ]);

        return $this->json($menu, Response::HTTP_OK, [], ['groups' => 'menu']);
    }
}
