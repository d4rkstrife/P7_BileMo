<?php

namespace App\Service;

use App\Repository\PhoneRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator
{
    private RequestStack $request;
    private ParameterBagInterface $params;

    public function __construct(RequestStack $request, ParameterBagInterface $params, PhoneRepository $phoneRepo)
    {
        $this->phoneRepo = $phoneRepo;
        $this->params = $params;
        $this->request = $request;
    }

    public function requestPage(string $type): int
    {
        $requestPage = $this->request->getCurrentRequest()->get('page', 1);
        if ($requestPage < 1) {
            $requestPage = 1;
        } else if ($requestPage > $this->maxPage($type)) {
            $requestPage = $this->maxPage($type);
        }
        return $requestPage;
    }

    public function numberOfItems(string $type): int
    {
        return $this->params->get($type);
    }

    public function actualPageItems(string $type): int
    {
        $actualPage = $this->requestPage($type) - 1;
        return $actualPage * $this->params->get($type);
    }

    public function maxPage(string $type): int
    {
        $numberOfItems = $this->phoneRepo->count([]);
        return $numberOfItems / $this->params->get($type);
    }
}
