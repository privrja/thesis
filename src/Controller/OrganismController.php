<?php

namespace App\Controller;

use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\Constants;
use App\Entity\Container;
use App\Entity\Organism;
use App\Model\ContainerModel;
use App\Repository\OrganismRepository;
use App\Structure\OrganismStructure;
use App\Structure\OrganismTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class OrganismController extends AbstractController {

    /**
     * Get organisms in container
     * @Route("/rest/container/{containerId}/organism", name="organism", methods={"GET"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param OrganismRepository $organismRepository
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Get(
     *     tags={"Organism"},
     *     @SWG\Response(response="200", description="Return list of organisms container."),
     *     @SWG\Response(response="403", description="Return when user has not acces to container."),
     *     @SWG\Response(response="404", description="Return when container not found."),
     * )
     */
    public function index(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, OrganismRepository $organismRepository, LoggerInterface $logger) {
        return StaticController::containerGetData($container, $request, $organismRepository, $this->getDoctrine(), $entityManager, $security->getUser(), $logger, 'findData');
    }

    /**
     * Add new organism
     * @Route("/rest/container/{containerId}/organism", name="organism_new", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     *
     * @SWG\Post(
     *     tags={"Organism"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="JSON with organism attribute - name for new organism",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="organism", type="string"),
     *                  example="{""organism"": ""Micrococcus luteus""}"),
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="201", description="Create new organism."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container is not found.")
     * )
     *
     * @return JsonResponse
     */
    public function addNewOrganism(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var OrganismTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new OrganismStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $message = $model->createNewOrganism($container, $trans);
        return new JsonResponse($message, $message->status, isset($message->id) ? Constants::getLocation('container/' . $container->getId() . '/organism/', $message->id) : []);
    }

    /**
     * Update organism
     * @Route("/rest/container/{containerId}/organism/{organismId}", name="organism_update", methods={"PUT"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("organism", expr="repository.find(organismId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Organism $organism
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Put(
     *     tags={"Organism"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="JSON with organism attribute - name for organism",
     *          @SWG\MediaType(mediaType="application\json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="organism", type="string"),
     *                  example="{""organism"": ""Micrococcus luteus""}"),
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="204", description="Organism updated."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or organism is not found.")
     * )
     */
    public function updateOrganism(Container $container, Organism $organism, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var OrganismTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new OrganismStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        return ResponseHelper::jsonResponse($model->updateOrganism($trans, $container, $organism));
    }

    /**
     * Delete organism
     * @Route("/rest/container/{containerId}/organism/{organismId}", name="organism_delete", methods={"DELETE"})
     * @Entity("container", expr="repository.find(containerId)")
     * @Entity("organism", expr="repository.find(organismId)")
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Organism $organism
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     tags={"Organism"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Organism deleted."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient."),
     *     @SWG\Response(response="404", description="Return when container or organism is not found.")
     *)
     */
    public function deleteOrganism(Container $container, Organism $organism, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        return ResponseHelper::jsonResponse($model->deleteOrganism($container, $organism));
    }

}
