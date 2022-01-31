<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Cart\ProductLineItemFactory;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;

use Shopware\Storefront\Page\GenericPageLoaderInterface;
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
    private GenericPageLoaderInterface $genericPageLoader;
    private SystemConfigService $systemConfigService;
    private EntityRepositoryInterface $fastOrderRepository;
    private EntityRepositoryInterface $fastOrderProductLineItemRepository;

    public function __construct(CartService $cartService,
        SalesChannelRepositoryInterface $productsRepository,
        ProductLineItemFactory $productLineItemFactory,
        GenericPageLoaderInterface $genericPageLoader,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $fastOrderRepository,
        EntityRepositoryInterface $fastOrderProductLineItemRepository
    )
    {
        $this->cartService = $cartService;
        $this->productsRepository = $productsRepository;
        $this->productLineItemFactory = $productLineItemFactory;
        $this->genericPageLoader = $genericPageLoader;
        $this->systemConfigService = $systemConfigService;
        $this->fastOrderRepository = $fastOrderRepository;
        $this->fastOrderProductLineItemRepository = $fastOrderProductLineItemRepository;
    }

    /**
     * @Route ("/fast-order", name="storefront.fast-order.page", methods={"GET"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function showFastOrderForm(Request $request, SalesChannelContext $context) : Response
    {
        $page = $this->genericPageLoader->load($request, $context);

        return $this->renderStorefront('@ElioFastOrder/storefront/form/fast-order.html.twig', [
            'page'=> $page
        ]);
    }

    /**
     * @Route ("/fast-order/add-to-cart", name="storefront.fast-order.add-to-card", methods={"POST"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function addProductsToCart(Request $request, SalesChannelContext $context) : Response
    {
        /** @var array $productsData */
        $productsData = $request->request->get('productData');
        if(!$productsData){
            throw new MissingRequestParameterException('productData');
        }
        if(!$this->isProductsDataValidate($productsData, $context)){
            return $this->redirectToRoute('storefront.fast-order.page');
        }

        // Create FastOrder entity
        $fastOrderId = Uuid::randomHex();
        $this->fastOrderRepository->create([
            [
             'id' => $fastOrderId,
             'sessionId' => $request->getSession()->getId(),
             'createdAt' => date('Y-m-d H:i:s'),
            ]
        ], $context->getContext());


        /** @var LineItem[] $products */
        $products = array();
        $fastOrderProductLineItems = [];
        $fastOrderProductPosition  = 1;

        foreach ($productsData as $productData) {
            /** @var int $productQuantity */
            $productQuantity = $productData['productQuantity'];

            $product = $this->getProductByProductNumber($context, $productData['productNumber']);
            $products[] = $this->productLineItemFactory->create($product->getId(), [
                'quantity' => $productQuantity
            ]);

            $fastOrderProductLineItems[] = [
                'id' => Uuid::randomHex(),
                'fastOrderId' => $fastOrderId,
                'productId' => $product->getId(),
                'quantity' => (int)$productQuantity,
                'position' => $fastOrderProductPosition,
            ];

            $fastOrderProductPosition++;
        }

        $this->fastOrderProductLineItemRepository->create($fastOrderProductLineItems, $context->getContext());
        $cart = $this->cartService->getCart($context->getToken(), $context);
        $this->cartService->add($cart, $products, $context);

        $this->addFlash(self::SUCCESS, $this->trans('elio_fast_order.flash.successProductAddedToCart'));
        return $this->forwardToRoute('frontend.checkout.cart.page');
    }

    /**
     * @Route("/fast-order/calculate-product-price/{productNumber}/{productQuantity}", name="storefront.fast-order.change-quantity", defaults={"XmlHttpRequest"=true}, methods={"GET"})
     * @param SalesChannelContext $context
     * @param string $productNumber
     * @param int $productQuantity
     * @return Response
     */
    public function calculateProductPrice(SalesChannelContext $context, string $productNumber, int $productQuantity) : Response
    {
        $product = $this->getProductByProductNumber($context, $productNumber);

        $calculatedPrice = ($product->getCalculatedPrices()->last()->getUnitPrice() * $productQuantity);

        return new Response($this->renderView('@ElioFastOrder/storefront/form/fast-order-form-calculated-price.html.twig', [
            'calculatedPrice' => $calculatedPrice
        ]));
    }

    /**
     * @Route("/fast-order/calculate-total-amount", name="storefront.fast-order.calculate-total-amount", defaults={"XmlHttpRequest"=true}, methods={"GET"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function calculateTotalAmount(Request $request, SalesChannelContext $context) : Response
    {
        /**
         * @var array $productNumbers
         */
        $productNumbers = $request->query->get('productNumbers');
        /**
         * @var array $productQuantities
         */
        $productQuantities = $request->query->get('productQuantities');
        $productNumbersCount = count($productNumbers);

        if(!$productNumbers){
            throw new MissingRequestParameterException('productNumbers');
        }
        if(!$productQuantities){
            throw new MissingRequestParameterException('productQuantities');
        }

        $totalAmount = 0;

        if($productNumbersCount == count($productQuantities)){

            for($i = 0; $i < $productNumbersCount; $i++ ){
                $product = $this->getProductByProductNumber($context, $productNumbers[$i]);
                $totalAmount += ($product->getCalculatedPrices()->last()->getUnitPrice() * $productQuantities[$i]);
            }
        }

        return new Response($this->renderView('@ElioFastOrder/storefront/form/fast-order-form-calculated-price.html.twig', [
            'calculatedPrice' => $totalAmount
        ]));
    }

    /**
     * @Route ("fast-order/reset-price", name="storefront.fast-order.reset-price", defaults={"XmlHttpRequest"=true}, methods={"GET"})
     * @return Response
     */
    public function resetPrice() : Response
    {
        return new Response($this->renderView('@ElioFastOrder/storefront/form/fast-order-form-calculated-price.html.twig'));
    }

    /**
     * @param SalesChannelContext $context
     * @param string $productNumber
     * @return SalesChannelProductEntity|null
     */
    private function getProductByProductNumber(SalesChannelContext $context, string $productNumber): ?SalesChannelProductEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('available', '1'));
        $criteria->addFilter(new EqualsFilter('productNumber', $productNumber));

        /**
         * @var SalesChannelProductEntity $product
         */
        $product = $this->productsRepository->search($criteria, $context)->getEntities()->first();

        if($product === null){
            return null;
        }

        return $product;
    }

    private function isProductsDataValidate(array $productsData, SalesChannelContext $context): bool
    {
        $isAllProductFound = true;
        $productsCount = 0;

        foreach ($productsData as $productData) {
            $productNumber = $productData['productNumber'];

            if($productNumber === ''){
                continue;
            }

            /** @var int $productQuantity */
            $productQuantity = $productData['productQuantity'];

            $product = $this->getProductByProductNumber($context, $productNumber);

            if($product === null){
                $isAllProductFound = false;
                $this->addFlash(self::DANGER, $this->trans('elio_fast_order.flash.dangerProductNotFoundByNumber', ['%number%' => $productNumber]));
                continue;
            }

            $productsCount += $productQuantity;
        }


        if(!$isAllProductFound){
            return false;
        }

        $countFields = $this->systemConfigService->get('ElioFastOrder.config.countFormFields');
        if($productsCount < $countFields){
            $this->addFlash(self::DANGER, $this->trans('elio_fast_order.flash.dangerTotalCountProducts', ['%count%' => $countFields]));
            return false;
        }

        return true;
    }
}
