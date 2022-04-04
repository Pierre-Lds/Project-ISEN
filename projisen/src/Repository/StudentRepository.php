<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Student $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Student $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Student) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return Student[]
     */
    public function findAllWithoutPair() {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id_pair IS NULL')
            ->orderBy('q.last_name','asc')
            ->getQuery()
            ->getResult();
    }

    public function findMainStudentsById($id) {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id = :id')
            ->orWhere('q.id_pair = :id')
            ->andWhere('q.is_main_student = true')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findStudentsById($id) {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findMainStudentsWithoutProject() {
        return $this->createQueryBuilder('q')
            ->andWhere('q.is_main_student = true')
            ->andWhere('q.id_project IS NULL')
            ->orderBy('q.last_name','asc')
            ->getQuery()
            ->getResult();
    }

    public function findMainStudentsWithProject() {
        return $this->createQueryBuilder('q')
            ->andWhere('q.is_main_student = true')
            ->andWhere('q.id_project IS NOT NULL')
            ->orderBy('q.last_name','asc')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Student[] Returns an array of Student objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
