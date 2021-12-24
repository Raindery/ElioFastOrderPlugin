<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FastOrderController extends StorefrontController
{
    private EntityRepositoryInterface $productsRepository;


    public function __construct(EntityRepositoryInterface $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @Route ("/fast-order", name="store-api.fast-order", methods={"GET"})
     * @param Context $context
     * @return Response
     */
    public function showFastOrderForm(Context $context) : Response
    {
        $productNumber = '92bdff6b279945b4aaaea7995b4421e8';
        return $this->renderStorefront('@ElioFastOrder/storefront/form/fast-order.html.twig', ['product' => $this->getProductByProductNumber($context, $productNumber)]);
    }

    private function getProductByProductNumber(Context $context, string $productNumber):ProductEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('available', '1'));
        $criteria->addFilter(new ContainsFilter('productNumber', $productNumber));
        $criteria->setLimit(10);


        $product = $this->productsRepository->search($criteria, $context)->getEntities()->first();

        if($product === null){
            throw new ProductNotFoundException('');
        }

        return $product;
    }
}
