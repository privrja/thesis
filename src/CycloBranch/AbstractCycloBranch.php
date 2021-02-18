<?php

namespace App\CycloBranch;

use App\Entity\Container;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractCycloBranch
 * Abstract class for import/export data from CycloBranch
 */
abstract class AbstractCycloBranch implements ICycloBranch {

    public const TABULATOR = "\t";

    /** @var ServiceEntityRepository */
    protected $repository;

    /** @var int */
    protected $containerId;

    /** @var string */
    protected $data = '';

    /**
     * AbstractCycloBranch constructor.
     * @param ServiceEntityRepository $repository
     * @param int $containerId
     */
    public function __construct(ServiceEntityRepository $repository, int $containerId) {
        $this->repository = $repository;
        $this->containerId = $containerId;
    }

    /**
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param array $okStack
     * @param array $errorStack
     * @return array
     * @see ICycloBranch::import()
     */
    abstract public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array;

    /**
     * Exporting data to a file
     */
    public abstract function download(): string;

    /**
     * @see ICycloBranch::export()
     */
    public final function export(): Response {
        $this->download();
        $response = new Response($this->data, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/plain');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'data.txt'
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

}
