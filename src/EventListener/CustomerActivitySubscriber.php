<?php

namespace App\EventListener;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use JetBrains\PhpStorm\NoReturn;
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomerActivitySubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postLoad(LifecycleEventArgs $args): void
    {
        $this->customerModification('decrypt', $args);
    }
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->customerModification('encrypt', $args);
    }
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->customerModification('decrypt', $args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->customerModification('encrypt', $args);
    }
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->customerModification('decrypt', $args);
    }

    private function customerModification(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
       if (!$entity instanceof Customer) {
            return;
        }
       if($action === 'encrypt'){
           $key = sodium_crypto_secretbox_keygen();
           $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
           $newName = sodium_crypto_secretbox($entity->getFirstName(),$nonce,$key);
           dd($newName);
           $entity->setFirstName(sodium_crypto_secretbox($entity->getFirstName().'toto',$nonce,$key));
       }

       if ($action === 'decrypt'){
           $entity->setFirstName(substr($entity->getFirstName(),0,strpos($entity->getFirstName(),'toto')));
       }




        // ... get the entity information and log it somehow
    }
}