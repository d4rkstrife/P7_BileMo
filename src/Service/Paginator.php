<?php

namespace App\Service;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Paginator
{
    private mixed $datas;
    private int $numberOfItems = 0;
    private int $maxPage = 0;

    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $request, private ParameterBagInterface $params)
    {
    }

    public function requestPage(): int
    {
        $requestPage = $this->request->getCurrentRequest()->get('page', 1);
        if ($requestPage < 1) {
            $requestPage = 1;
        } else if ($requestPage > $this->maxPage) {
            $requestPage = $this->maxPage;
        }
        return $requestPage - 1;
    }

    public function getNumberOfItems(): int
    {
        return $this->numberOfItems;
    }

    public function getMaxPage(): int
    {
        return $this->maxPage;
    }

    public function createPagination(string $repositoryName, array $criteria, array $orderBy, string $type): void
    {
        $this->numberOfItems = $this->params->get($type);
        $repository = $this->entityManager->getRepository($repositoryName);
        $maxOfItems = $repository->count($criteria);
        $this->maxPage = ceil($maxOfItems / $this->numberOfItems);
        $actualPage = $this->requestPage();
        $this->datas = $repository->findBy($criteria, $orderBy, $this->numberOfItems, $actualPage * $this->numberOfItems);
    }

    public function getDatas(): mixed
    {
        return $this->datas;
    }
}
