<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;


use phpDocumentor\Reflection\Types\This;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Search\SearchPageLoader;
use Shopware\Storefront\Page\Suggest\SuggestPageLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

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
     * @Route ("/fs-search-page", name="frontend.fast-order.search", methods={"GET"})
     * @return Response
     */
    public function show() : Response
    {
        return $this->renderStorefront('@ElioFastOrder/storefront/test.html.twig');
    }

    /**
     * @Route ("/fast-order-test", name="frontend.fast-order.test", defaults={"XmlHttpRequest"=true}, methods={"GET"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function search(Request $request, SalesChannelContext $context) :Response
    {
        $productNumber = $request->query->get('searchInput');

        $products = $this->getProductsByNumber($context, $productNumber);

        return new Response('fdsfsdd');
    }

    private function getProductsByNumber(SalesChannelContext $context, string $productNumber) : ?ProductCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('available', '1'));
        $criteria->addFilter(new PrefixFilter('productNumber', $productNumber));
        $criteria->setLimit(10);

        /** @var ProductCollection $products */
        $products = $this->salesChannelRepository->search($criteria, $context)->getEntities();

        return $products;
    }
}


