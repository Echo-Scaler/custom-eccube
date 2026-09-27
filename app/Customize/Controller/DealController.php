<?php

/*
 * Deal Page Controller
 * Displays dedicated Deals & Mega Discounts page with live countdown,
 * category filters, discount rate filters, and direct add-to-cart integration.
 */

namespace Customize\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Category;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Form\Type\AddCartType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        BaseInfoRepository $baseInfoRepository,
        PaginatorInterface $paginator
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/deals", name="deal_page", methods={"GET"})
     * @Route("/deal", name="deal_page_alias", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $BaseInfo = $this->baseInfoRepository->get();

        if ($BaseInfo->isOptionNostockHidden()) {
            $this->entityManager->getFilters()->enable('option_nostock_hidden');
        }

        // Query parameters
        $categoryId = $request->query->getInt('category_id');
        $discountFilter = $request->query->get('discount', 'all');
        $orderby = $request->query->get('orderby', 'discount_desc');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 16;

        // Categories list
        $categories = $this->categoryRepository->findBy(['Parent' => null], ['sort_no' => 'DESC']);

        // Build main deals query
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.ProductClasses', 'pc')
            ->where('p.Status = :status')
            ->andWhere('pc.visible = :visible')
            ->andWhere('pc.price01 IS NOT NULL')
            ->andWhere('pc.price01 > pc.price02')
            ->setParameter('status', ProductStatus::DISPLAY_SHOW)
            ->setParameter('visible', true);

        // Category filter
        $currentCategory = null;
        if ($categoryId > 0) {
            $currentCategory = $this->categoryRepository->find($categoryId);
            if ($currentCategory) {
                $qb->innerJoin('p.ProductCategories', 'pct')
                    ->andWhere('pct.Category = :Category')
                    ->setParameter('Category', $currentCategory);
            }
        }

        // Discount filter
        if ($discountFilter === '50') {
            $qb->andWhere('pc.price02 <= pc.price01 * 0.50');
        } elseif ($discountFilter === '40') {
            $qb->andWhere('pc.price02 <= pc.price01 * 0.60');
        } elseif ($discountFilter === '30') {
            $qb->andWhere('pc.price02 <= pc.price01 * 0.70');
        } elseif ($discountFilter === '20') {
            $qb->andWhere('pc.price02 <= pc.price01 * 0.80');
        }

        // Ordering
        $qb->groupBy('p.id');

        if ($orderby === 'price_asc') {
            $qb->addSelect('MIN(pc.price02) AS HIDDEN price02_min');
            $qb->orderBy('price02_min', 'ASC');
            $qb->addOrderBy('p.id', 'DESC');
        } elseif ($orderby === 'price_desc') {
            $qb->addSelect('MAX(pc.price02) AS HIDDEN price02_max');
            $qb->orderBy('price02_max', 'DESC');
            $qb->addOrderBy('p.id', 'DESC');
        } elseif ($orderby === 'newest') {
            $qb->orderBy('p.id', 'DESC');
        } else {
            // Default: highest discount percentage first
            $qb->addSelect('MAX((pc.price01 - pc.price02) / pc.price01) AS HIDDEN discount_ratio');
            $qb->orderBy('discount_ratio', 'DESC');
            $qb->addOrderBy('p.id', 'DESC');
        }

        // Paginate deals
        $pagination = $this->paginator->paginate($qb->getQuery(), $page, $limit);

        // Fetch sorted class categories for products in pagination
        $productIds = [];
        foreach ($pagination as $product) {
            $productIds[] = $product->getId();
        }

        $productsWithClasses = !empty($productIds)
            ? $this->productRepository->findProductsWithSortedClassCategories($productIds, 'p.id')
            : [];

        // Build AddCart forms for each product
        $forms = [];
        foreach ($pagination as $product) {
            $builder = $this->formFactory->createNamedBuilder(
                '',
                AddCartType::class,
                null,
                [
                    'product' => $productsWithClasses[$product->getId()] ?? $product,
                    'allow_extra_fields' => true,
                ]
            );
            $forms[$product->getId()] = $builder->getForm()->createView();
        }

        // Spotlight Deal of the Day (3 prominent featured items)
        $spotlightProductIds = [2, 1, 3]; // AirPods Max, Wireless Earbuds, Bose BT
        $spotlightProducts = [];
        $spotlightForms = [];
        foreach ($spotlightProductIds as $sId) {
            try {
                $sp = $this->productRepository->findWithSortedClassCategories($sId);
                if ($sp && $sp->getStatus() && $sp->getStatus()->getId() == ProductStatus::DISPLAY_SHOW) {
                    // Build dedicated form for spotlight
                    $builder = $this->formFactory->createNamedBuilder(
                        '',
                        AddCartType::class,
                        null,
                        [
                            'product' => $sp,
                            'allow_extra_fields' => true,
                        ]
                    );
                    $spotlightForms[$sp->getId()] = $builder->getForm()->createView();
                    $spotlightProducts[] = $sp;
                }
            } catch (\Exception $e) {
                // Ignore missing product ID
                continue;
            }
        }

        // Deal count stats
        $totalDealsCount = (int) $this->entityManager->createQuery(
            'SELECT COUNT(DISTINCT p.id) FROM Eccube\Entity\ProductClass pc JOIN pc.Product p WHERE p.Status = 1 AND pc.visible = true AND pc.price01 > pc.price02'
        )->getSingleScalarResult();

        return $this->render('Deal/index.twig', [
            'pagination' => $pagination,
            'forms' => $forms,
            'spotlight_forms' => $spotlightForms,
            'categories' => $categories,
            'current_category' => $currentCategory,
            'current_category_id' => $categoryId,
            'current_discount' => $discountFilter,
            'current_orderby' => $orderby,
            'spotlight_products' => $spotlightProducts,
            'total_deals_count' => $totalDealsCount,
        ]);
    }
}
