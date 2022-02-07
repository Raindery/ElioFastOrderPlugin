<?php declare(strict_types=1);

namespace Elio\FastOrder\Controller;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
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

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @RouteScope(scopes={"storefront"})
 */
class FastOrderController extends StorefrontController
{
    private CartService $cartService;
    private SalesChannelRepositoryInterface $productsRepository;
    private LineItemFactoryRegistry $lineItemFactoryRegistry;
    private GenericPageLoaderInterface $genericPageLoader;
    private SystemConfigService $systemConfigService;
    private EntityRepositoryInterface $fastOrderRepository;
    private EntityRepositoryInterface $fastOrderProductLineItemRepository;

    public function __construct(CartService $cartService,
        SalesChannelRepositoryInterface $productsRepository,
        LineItemFactoryRegistry $lineItemFactoryRegistry,
        GenericPageLoaderInterface $genericPageLoader,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $fastOrderRepository,
        EntityRepositoryInterface $fastOrderProductLineItemRepository
    )
    {
        $this->cartService = $cartService;
        $this->productsRepository = $productsRepository;
        $this->lineItemFactoryRegistry = $lineItemFactoryRegistry;
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
     * @Route ("/fast-order/add-to-cart", name="storefront.fast-order.add-to-cart", methods={"POST"})
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

            $products[] = $this->lineItemFactoryRegistry->create([
                'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                'referencedId' => $product->getId(),
                'quantity' => $productQuantity,
            ], $context);

            $fastOrderProductLineItems[] = [
                'id' => Uuid::randomHex(),
                'fastOrderId' => $fastOrderId,
                'productId' => $product->getId(),
                'quantity' => $productQuantity,
                'position' => $fastOrderProductPosition,
            ];

            $fastOrderProductPosition++;
        }


        $this->fastOrderProductLineItemRepository->create($fastOrderProductLineItems, $context->getContext());
        $cart = $this->cartService->getCart($context->getToken(), $context);
        $this->cartService->add($cart, $products, $context);

        $this->addFlash(self::SUCCESS, $this->trans('elio_fast_order.flash.successProductAddedToCart'));
        return $this->redirectToRoute('frontend.checkout.cart.page');
    }

    /**
     * @Route ("/fast-order/add-to-cart-form-file", name="storefront.fast-order.add-to-cart-from-file", methods={"POST"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function addProductsFromCsv(Request $request, SalesChannelContext $context):Response
    {
        /** @var UploadedFile|null $file*/
        $file = $request->files->all()['fastOrderCsvFile'];
        if(!$file){
            throw new MissingRequestParameterException('fastOrderCsvFile');
        }
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        /** @var array|null $productsData */
        $productsData = $serializer->decode($file->getContent(), 'csv', ['csv_delimiter' => ';']);
        if(!$productsData === null){
            $this->addFlash(self::DANGER, $this->trans('elio_fast_order.uploadFormFlash.wrongFile'));
        }

        // Get product data keys
        $productDataKeys = array_keys($productsData[0]);
        /** @var string $productNumberKey*/
        $productNumberKey = $productDataKeys[0];
        /**@var string $productQuantityKey*/
        $productQuantityKey = $productDataKeys[1];

        // Handle data from file
        /** @var LineItem[] $productLineItem */
        $productLineItem = [];
        $fastOrderProductPosition = 1;
        $fastOrderProductLineItems = [];
        $fastOrderId = Uuid::randomHex();

        foreach ($productsData as $data) {
            $productNumber = $data[$productNumberKey];
            $productQuantity = $data[$productQuantityKey];

            $product = $this->getProductByProductNumber($context, $productNumber);
            if($product === null){
                $this->addFlash(self::DANGER, $this->trans('elio_fast_order.uploadFormFlash.notFoundProduct', ['%productNumber%' => $productNumber]));
                return $this->redirectToRoute('storefront.fast-order.page');
            }

            $productLineItem[] = $this->lineItemFactoryRegistry->create([
                'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                'referencedId' => $product->getId(),
                'quantity' => $productQuantity,
            ], $context);

            $fastOrderProductLineItems[] = [
                'id' => Uuid::randomHex(),
                'fastOrderId' => $fastOrderId,
                'productId' => $product->getId(),
                'quantity' => (int)$productQuantity,
                'position' => $fastOrderProductPosition,
            ];

            $fastOrderProductPosition++;
        }

        $productLineItem->

        // Create fast order
        $this->fastOrderRepository->create([
            [
                'id' => $fastOrderId,
                'sessionId' => $request->getSession()->getId(),
                'createdAt' => date('Y-m-d H:i:s'),
            ]
        ], $context->getContext());

        // Set data to cart and database
        $this->fastOrderProductLineItemRepository->create($fastOrderProductLineItems, $context->getContext());
        $cart = $this->cartService->getCart($context->getToken(), $context);
        $this->cartService->add($cart, $productLineItem, $context);

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

        $productPrice = $product->getCalculatedPrice();
        if($product->getCalculatedPrices()->count() > 0){
           $productPrice = $product->getCalculatedPrices()->last();
        }

        $calculatedPrice = $productPrice->getUnitPrice() * $productQuantity;

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

                $productPrice = $product->getCalculatedPrice();
                if($product->getCalculatedPrices()->count() > 0){
                    $productPrice = $product->getCalculatedPrices()->last();
                }

                $totalAmount += $productPrice->getUnitPrice() * $productQuantities[$i];
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

        return $this->productsRepository->search($criteria, $context)->getEntities()->first();
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
