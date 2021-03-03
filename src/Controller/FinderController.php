<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Enum\ContainerVisibilityEnum;
use App\Model\ContainerModel;
use App\Repository\SequenceRepository;
use App\Structure\IValue;
use App\Structure\SequenceFormulaStructure;
use App\Structure\SequenceIdStructure;
use App\Structure\SequenceNameStructure;
use App\Structure\SequenceSmilesStructure;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FinderController extends AbstractController {

    /**
     * @Route("/rest/container/{containerId}/name", name="find_name", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param SequenceRepository $sequenceRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function name(Request $request, Container $container, SequenceRepository $sequenceRepository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceNameStructure, 'sequenceName', $sequenceRepository, $entityManager, $security, $logger);
    }

    /**
     * @Route("/rest/container/{containerId}/formula", name="find_formula", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param SequenceRepository $sequenceRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function formula(Request $request, Container $container, SequenceRepository $sequenceRepository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceFormulaStructure, 'sequenceFormula', $sequenceRepository, $entityManager, $security, $logger);
    }

    /**
     * @Route("/rest/container/{containerId}/similarity", name="find_smiles", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param SequenceRepository $sequenceRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function smiles(Request $request, Container $container, SequenceRepository $sequenceRepository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceSmilesStructure, 'usmiles', $sequenceRepository, $entityManager, $security, $logger);
    }

    /**
     * @Route("/rest/container/{containerId}/identifier", name="find_identifier", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Request $request
     * @param Container $container
     * @param SequenceRepository $sequenceRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function identifier(Request $request, Container $container, SequenceRepository $sequenceRepository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        return $this->find($container, $request, new SequenceIdStructure, 'id', $sequenceRepository, $entityManager, $security, $logger);
    }

    private function find(Container $container, Request $request, $structure, string $param, SequenceRepository $sequenceRepository, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC) {
            /** @var IValue $trans */
            $trans = RequestHelper::evaluateRequest($request, $structure, $logger);
            if ($trans instanceof JsonResponse) {
                return $trans;
            }
            return new JsonResponse($sequenceRepository->findBy(['container' => $container, $param => $trans->getValue()]));
        }
        if ($this->isGranted("USER_ROLE")) {
            $containerModel = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
            if ($containerModel->hasContainer($container->getId())) {
                /** @var IValue $trans */
                $trans = RequestHelper::evaluateRequest($request, $structure, $logger);
                if ($trans instanceof JsonResponse) {
                    return $trans;
                }
                return new JsonResponse($sequenceRepository->findBy(['container' => $container, $param => $trans->getValue()]));
            }
            return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
        return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_NOT_FOUND, Response::HTTP_FORBIDDEN));
    }

}

