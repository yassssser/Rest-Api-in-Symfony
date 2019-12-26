<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthorSubscriber implements EventSubscriberInterface
{
    private $myToken;
     
    public function __construct(TokenStorageInterface $myToken)
    {
        $this->myToken = $myToken;
    }

    public function getUserFromToken(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($entity instanceof Post || $entity instanceof Comment ) && $method == Request::METHOD_POST){

            $author = $this->myToken->getToken()->getUser();
            $entity->setAuthor($author);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['getUserFromToken' , EventPriorities::PRE_WRITE],
        ];
    }
}
