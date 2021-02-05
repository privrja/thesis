<?php

namespace App\Controller;

use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Entity\Container;
use App\Model\ContainerModel;
use App\Structure\SequenceStructure;
use App\Structure\SequenceTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

class SequenceController extends AbstractController {

    /**
     * Add new sequence for logged user
     * @Route("/rest/container/{id}/sequence", name="sequence_new", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Container $container
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param LoggerInterface $logger
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"Sequence"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="",
     *          @SWG\Schema(type="string",
     *              example=""),
     *      ),
     *     @SWG\Response(response="201", description="Create new sequence."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when permisions is insufient.")
     * )
     */
    public function addNewSequence(Container $container, Request $request, EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger) {
        /** @var SequenceTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new SequenceStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        $modelMessage = $model->createNewSequence($container, $trans);
        return ResponseHelper::jsonResponse($modelMessage);
    }


}
