<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Cart\ProductLineItemFactory;
use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FastOrderController extends StorefrontController
{
    private CartService $cartService;
    private SalesChannelRepositoryInterface $productsRepository;
    private ProductLineItemFactory $productLineItemFactory;

    public function __construct(CartService $cartService, SalesChannelRepositoryInterface $productsRepository, ProductLineItemFactory $productLineItemFactory)
    {
        $this->cartService = $cartService;
        $this->productsRepository = $productsRepository;
        $this->productLineItemFactory = $productLineItemFactory;
    }

    /**
     * @Route ("/fast-order", name="store-api.fast-order", methods={"GET"})
     * @return Response
     */
    public function showFastOrderForm() : Response
    {
        return $this->renderStorefront('@ElioFastOrder/storefront/form/fast-order.html.twig');
    }

    /**
     * @Route ("/fast-order/add-to-cart", name="store-api.fast-order.add-to-card", methods={"POST"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function addProductsToCart(Request $request, SalesChannelContext $context) : Response
    {
        /** @var array $productsData */
        $productsData = $request->request->get('productData');

        if(!$productsData){
            throw new MissingRequestParameterException('productNumber');
        }

        $productsCount = 0;
        foreach ($productsData as $productData) {
            $productsCount += (int)$productData['productQuantity'];
        }
        if($productsCount < 10){
            $this->addFlash(self::DANGER, 'The total number of products must be more than 10!');
            return $this->redirectToRoute('store-api.fast-order');
        }


        /** @var LineItem[] $products */
        $products = array();

        foreach ($productsData as $productData) {

            if($productData['productNumber'] === ''){
                $this->addFlash(self::WARNING, 'Product number not entered');
                continue;
            }

            $productId = $this->getProductByProductNumber($context, $productData['productNumber'])->first()->getId();

            $products[] = $this->productLineItemFactory->create($productId, [
                'quantity' => (int)$productData['productQuantity']
            ]);
        }

        $cart = $this->cartService->getCart($context->getToken(), $context);
        $this->cartService->add($cart, $products, $context);

        $this->addFlash(self::SUCCESS, 'Products added to cart ');
        return $this->redirectToRoute('frontend.checkout.cart.page');
    }


    /**
     * @param SalesChannelContext $context
     * @param string $productNumber
     * @return ProductCollection
     */
    private function getProductByProductNumber(SalesChannelContext $context, string $productNumber): ProductCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('available', '1'));
        $criteria->addFilter(new PrefixFilter('productNumber', $productNumber));

        /**
         * @var ProductCollection $products
         */
        $products = $this->productsRepository->search($criteria, $context)->getEntities();

        if($products->count() === 0){
            throw new ProductNotFoundException('');
        }

        return $products;
    }
}
