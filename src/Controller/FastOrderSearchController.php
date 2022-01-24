<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FastOrderSearchController extends StorefrontController
{
    private SalesChannelRepositoryInterface $salesChannelRepository;

    public function __construct(SalesChannelRepositoryInterface $salesChannelRepository)
    {
        $this->salesChannelRepository = $salesChannelRepository;
    }

    /**
     * @Route ("/fast-order-search-products", name="frontend.fast-order.search", defaults={"XmlHttpRequest"=true}, methods={"GET"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function search(Request $request, SalesChannelContext $context) :Response
    {
        $productNumber = $request->query->get('searchInput');
        /*** @var ProductCollection $products */
        $products = $this->getProductsByNumber($context, $productNumber);

        return $this->renderStorefront('@ElioFastOrder/storefront/search/fast-order-search-result.html.twig', ['products' => $products]);
    }

    /**
     * @Route ("/fast-order-search-products/select-product/{productNumber}", name="frontend.fast-order.select-product", defaults={"XmlHttpRequest"=true}, methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function productSelect(Request $request, SalesChannelContext $context, string $productNumber) : Response
    {
        $selectedProduct = $this->getProductsByNumber($context, $productNumber)->first();
        return new Response($this->renderView('@ElioFastOrder/storefront/search/fast-order-search-selected-product.html.twig', [
            'selectedProduct'=>$selectedProduct
        ]));
    }

    private function getProductsByNumber(SalesChannelContext $context, string $productNumber) : ?ProductCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('available', '1'));
        $criteria->addFilter(new PrefixFilter('productNumber', $productNumber));
        $criteria->setLimit(30);

        /** @var ProductCollection $products */
        $products = $this->salesChannelRepository->search($criteria, $context)->getEntities();

        return $products;
    }
}


