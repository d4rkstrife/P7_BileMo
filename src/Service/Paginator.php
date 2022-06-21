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

    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $request, private ParameterBagInterface $params)
    {
    }

    public function requestPage(string $type, array $criteria = [], mixed $repository): int
    {
        $requestPage = $this->request->getCurrentRequest()->get('page', 1);
        if ($requestPage < 1) {
            $requestPage = 1;
        } else if ($requestPage > $this->maxPage($type, $criteria, $repository)) {
            $requestPage = $this->maxPage($type, $criteria, $repository);
        }
        return $requestPage;
    }

    public function numberOfItems(string $type): int
    {
        return $this->params->get($type);
    }

    public function actualPageItems(string $type, array $criteria = [], mixed $repository): int
    {
        $actualPage = $this->requestPage($type, $criteria, $repository) - 1;
        return $actualPage * $this->params->get($type);
    }

    public function maxPage(string $type, array $criteria = [], mixed $repository): int
    {
        $numberOfItems = $repository->count($criteria);

        return ceil($numberOfItems / $this->params->get($type));
    }

    public function createPagination(string $repositoryName, array $criteria, array $orderBy, string $type): void
    {
        $repository = $this->entityManager->getRepository($repositoryName);
        $this->datas = $repository->findBy($criteria, $orderBy, $this->numberOfItems($type), $this->actualPageItems($type, $criteria, $repository));
    }

    public function getDatas(): mixed
    {
        return $this->datas;
    }
}
