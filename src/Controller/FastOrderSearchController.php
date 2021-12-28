<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;


use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Search\SearchPageLoader;
use Shopware\Storefront\Page\Suggest\SuggestPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */

class FastOrderSearchController extends StorefrontController
{
    private SuggestPageLoader $suggestPageLoader;
    private SearchPageLoader $searchPageLoader;

    public function __construct(SuggestPageLoader $suggestPageLoader, SearchPageLoader $searchPageLoader)
    {
        $this->suggestPageLoader = $suggestPageLoader;
        $this->searchPageLoader = $searchPageLoader;
    }

    /**
     * @Route ("fast-order/suggest", name="frontend.fast-order.suggest", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     * @param SalesChannelContext $context
     * @param Request $request
     * @return Response
     */
    public function suggest(SalesChannelContext $context, Request $request):Response
    {
        $page = $this->suggestPageLoader->load($request, $context);
        return $this->renderStorefront('@ElioFastOrder/storefront/search/fast-order-search-suggest.html.twig',
            ['page' => $page]
        );
    }


    /**
     * @Route ("fast-order/search", name="frontend.fast-order.search", methods={"GET"})
     * @param SalesChannelContext $context
     * @param Request $request
     * @return Response
     */
    public function search(SalesChannelContext $context, Request $request) : Response
    {
        try {
            $page = $this->searchPageLoader->load($request, $context);
            if ($page->getListing()->getTotal() === 1) {
                $product = $page->getListing()->first();
                if ($request->get('search') === $product->getProductNumber()) {
                    $productId = $product->getId();

                    return $this->forwardToRoute('frontend.detail.page', [], ['productId' => $productId]);
                }
            }
        } catch (MissingRequestParameterException $missingRequestParameterException) {
            return $this->forwardToRoute('frontend.home.page');
        }

        return $this->renderStorefront('@Storefront/storefront/page/search/index.html.twig', ['page' => $page]);
    }

}


