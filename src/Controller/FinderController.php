<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\Block;
use App\Entity\Container;
use App\Entity\Sequence;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Structure\IValue;
use App\Structure\SequenceFormulaStructure;
use App\Structure\SequenceIdStructure;
use App\Structure\SequenceNameStructure;
use App\Structure\SequenceSmilesStructure;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FinderController extends AbstractController {

    const METHOD_DEFAULT = 'name';

    /**
     * Find by name
     * @Route("/rest/container/{containerId}/name", name="find_name", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Finder"},
     *     @SWG\Response(response="200", description="Results"),
     *     @SWG\Response(response="404", description="Not found"),
     *     @SWG\Response(response="403", description="Insuficient rights"),
     * )
     */
    public function name(Request $request, Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceNameStructure, 'sequenceName', 'likeName', $entityManager, $security, $logger);
    }

    /**
     * Find by formula
     * @Route("/rest/container/{containerId}/formula", name="find_formula", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Finder"},
     *     @SWG\Response(response="200", description="Results"),
     *     @SWG\Response(response="404", description="Not found"),
     *     @SWG\Response(response="403", description="Insuficient rights"),
     * )
     */
    public function formula(Request $request, Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceFormulaStructure, 'sequenceFormula', self::METHOD_DEFAULT, $entityManager, $security, $logger);
    }

    /**
     * Find by similarity
     * @Route("/rest/container/{containerId}/similarity", name="find_smiles", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Finder"},
     *     @SWG\Response(response="200", description="Results"),
     *     @SWG\Response(response="404", description="Not found"),
     *     @SWG\Response(response="403", description="Insuficient rights"),
     * )
     */
    public function smiles(Request $request, Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceSmilesStructure, 'usmiles', 'similarity', $entityManager, $security, $logger);
    }

    /**
     * Find by identifier
     * @Route("/rest/container/{containerId}/identifier", name="find_identifier", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Finder"},
     *     @SWG\Response(response="200", description="Results"),
     *     @SWG\Response(response="404", description="Not found"),
     *     @SWG\Response(response="403", description="Insuficient rights"),
     * )
     */
    public function identifier(Request $request, Container $container, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceIdStructure, 'id', self::METHOD_DEFAULT, $entityManager, $security, $logger);
    }

    private function find(Container $container, Request $request, $structure, string $param, string $method, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            return $this->findBy($container, $request, $structure, $param, $method, $entityManager, $logger);
        }
        if ($this->isGranted("USER_ROLE")) {
            $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
            if ($containerModel->hasContainer($container->getId())) {
                return $this->findBy($container, $request, $structure, $param, $method, $entityManager, $logger);
            }
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_FOUND, Response::HTTP_FORBIDDEN));
    }

    private function findBy(Container $container, Request $request, $structure, string $param, string $method, EntityManagerInterface $entityManager, LoggerInterface $logger) {
        /** @var IValue $trans */
        $trans = RequestHelper::evaluateRequest($request, $structure, $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        if ($method === self::METHOD_DEFAULT) {
            $sequenceRepository = $entityManager->getRepository(Sequence::class);
            return new JsonResponse($sequenceRepository->findBy(['container' => $container, $param => $trans->getValue()]));
        } else {
            return new JsonResponse($this->$method($container->getId(), $trans->getValue(), $entityManager));
        }
    }

    private function likeName(int $containerId, string $name, EntityManagerInterface $entityManager) {
        $sequenceRepository = $entityManager->getRepository(Sequence::class);
        return $sequenceRepository->name($containerId, $name);
    }

    private function similarity(int $containerId, array $smiles, EntityManagerInterface $entityManager) {
        $blockRepository = $entityManager->getRepository(Block::class);
        $ids = $blockRepository->findBlockIds($containerId, $smiles);
        $blocIds = "('" . $ids[0]['id'];
        for ($i = 1; $i < sizeof($ids); $i++) {
            $blocIds .= "', '" . $ids[$i]['id'];
        }
        $blocIds .= "')";
        $sequenceRepository = $entityManager->getRepository(Sequence::class);
        return $sequenceRepository->similarityMore($containerId, $blocIds, $i, sizeof($smiles));
    }

}

