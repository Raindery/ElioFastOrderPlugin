<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FastOrderController extends StorefrontController
{
    private LineItemFactoryRegistry $factory;

    private EntityRepositoryInterface $productsRepository;

    private CartService $cartService;


    public function __construct(EntityRepositoryInterface $productsRepository, CartService $cartService, LineItemFactoryRegistry $factory)
    {
        $this->productsRepository = $productsRepository;
        $this->cartService = $cartService;
        $this->factory = $factory;
    }

    /**
     * @Route ("/fast-order", name="store-api.fast-order", methods={"GET"})
     * @param Context $context
     * @return Response
     */
    public function showFastOrderForm(Context $context) : Response
    {
        $productNumber = 'f7';
        return $this->renderStorefront('@ElioFastOrder/storefront/form/fast-order.html.twig', ['products' => $this->getProductByProductNumber($context, $productNumber)]);
    }


    /**
     * @Route ("/fast-order/add-to-cart", name="store-api.fast-order.add-to-card", methods={"GET"})
     * @param SalesChannelContext $context
     * @param Cart $cart
     * @return Response
     */
    public function addProductsToCart(SalesChannelContext $context, Cart $cart) : Response
    {
        $productNumber = 'f7';
        $product = $this->getProductByProductNumber($context->getContext(), $productNumber)->first();

        $lineItem = $this->factory->create([
            'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
            'referencedId' => $product->getId(),
            'quantity'=> 10
        ], $context);

        $this->cartService->add($cart, $lineItem, $context);

        return $this->redirectToRoute('frontend.checkout.cart.page');
    }

    /**
     * @param Context $context
     * @param string $productNumber
     * @return ProductCollection
     */
    private function getProductByProductNumber(Context $context, string $productNumber): ProductCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('available', '1'));
        $criteria->addFilter(new PrefixFilter('productNumber', $productNumber));
        $criteria->setLimit(10);

        $product = $this->productsRepository->search($criteria, $context)->getEntities();

        if($product->count() === 0){
            throw new ProductNotFoundException('');
        }

        return $product;
    }
}
