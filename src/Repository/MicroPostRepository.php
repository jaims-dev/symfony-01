<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    /**
     * @param Collection $users
     * @return mixed
     *
     * Doctrine somehows instantiates stuff on its own; User::following is not an ArrayCollection
     * (as we set in __construct) but a PersistenCollection (db). Both implement the
     * Collection interface, therefore the arg for findAllByUsers must be a Collection
     */
    public function findAllByUsers(Collection $users) {
        $qb = $this->createQueryBuilder('p');
        return $qb->select('p')
            ->where('p.user IN (:following)')
            ->setParameter('following', $users)
            ->orderBy('p.time', 'DESC')
            ->getQuery()
            ->execute();
    }
}
