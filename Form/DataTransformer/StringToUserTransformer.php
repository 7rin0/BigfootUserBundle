<?php

namespace Bigfoot\Bundle\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class StringToUserTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if ($value != null) {
            return (string) $value;
        }

        return '';
    }

    public function reverseTransform($value)
    {
        $user = $this->entityManager->getRepository('BigfootUserBundle:User')->findOneByEmail($value);

        var_dump($user);die();
        // y.latti@c2is.fr

        if (!$user) {
            throw new TransformationFailedException("User not found");
        }

        return $user;
    }
}
