<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FastOrderController extends StorefrontController
{
    /**
     * @Route ("/fast-order", name="store-api.fast-order", methods={"GET"})
     * @return Response
     */
    public function showFastOrderForm() : Response
    {
        return new Response('Fast Order Plugin Route Test', Response::HTTP_OK);
    }
}
