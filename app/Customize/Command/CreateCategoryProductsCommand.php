<?php

namespace Customize\Command;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Member;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Category;
use Eccube\Repository\CategoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCategoryProductsCommand extends Command
{
    protected static $defaultName = 'customize:create-category-products';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Align categories with storefront and generate 50 products for each category')
            ->addOption('count', null, InputOption::VALUE_OPTIONAL, 'Number of products per category', 50)
            ->addOption('reset-all', null, InputOption::VALUE_NONE, 'Reset dummy products and align categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countPerCategory = (int) $input->getOption('count');

        $io->title('Aligning Categories and Generating 50 Products Per Category');

        $conn = $this->entityManager->getConnection();

        // 1. Clean up products > 5
        $io->section('Cleaning up previous dummy products...');
        $conn->executeStatement('DELETE FROM dtb_customer_favorite_product WHERE product_id > 5');
        $conn->executeStatement('DELETE FROM dtb_product_category WHERE product_id > 5');
        $conn->executeStatement('DELETE FROM dtb_product_image WHERE product_id > 5');
        $conn->executeStatement('DELETE FROM dtb_product_stock WHERE product_class_id IN (SELECT id FROM dtb_product_class WHERE product_id > 5)');
        $conn->executeStatement('DELETE FROM dtb_product_class WHERE product_id > 5');
        $conn->executeStatement('DELETE FROM dtb_product WHERE id > 5');

        // 2. Clean up obsolete categories
        $io->section('Aligning categories to match the 6 Storefront categories...');
        $conn->executeStatement('DELETE FROM dtb_product_category WHERE category_id IN (2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13)');
        $conn->executeStatement('DELETE FROM dtb_category WHERE id IN (11, 12, 13, 4, 6, 10)');
        $conn->executeStatement('DELETE FROM dtb_category WHERE id IN (9, 3)');
        $conn->executeStatement('DELETE FROM dtb_category WHERE id IN (7, 2, 5)');

        // Rename 1 from Computer to Laptop
        $conn->executeStatement("UPDATE dtb_category SET category_name = 'Laptop', parent_category_id = NULL, hierarchy = 1 WHERE id = 1");

        // Ensure proper sort_no and hierarchy for the 6 target categories
        $targetCategoriesConfig = [
            17 => ['name' => 'Furniture', 'sort_no' => 6],
            16 => ['name' => 'Headphone', 'sort_no' => 5],
            8  => ['name' => 'Shoe',      'sort_no' => 4],
            15 => ['name' => 'Bag',       'sort_no' => 3],
            1  => ['name' => 'Laptop',    'sort_no' => 2],
            14 => ['name' => 'Book',      'sort_no' => 1],
        ];

        foreach ($targetCategoriesConfig as $catId => $cfg) {
            $conn->executeStatement(
                "UPDATE dtb_category SET category_name = :name, sort_no = :sort_no, parent_category_id = NULL, hierarchy = 1 WHERE id = :id",
                ['name' => $cfg['name'], 'sort_no' => $cfg['sort_no'], 'id' => $catId]
            );
        }

        // Link existing products (1-5, all headphones) to Headphone (16)
        $conn->executeStatement('DELETE FROM dtb_product_category WHERE product_id <= 5');
        foreach ([1, 2, 3, 4, 5] as $origProdId) {
            $conn->executeStatement(
                "INSERT INTO dtb_product_category (product_id, category_id, discriminator_type) VALUES (:prod_id, 16, 'productcategory')",
                ['prod_id' => $origProdId]
            );
        }

        // Refresh entity manager to reload clean categories
        $this->entityManager->clear();

        $Member = $this->entityManager->find(Member::class, 1);
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_SHOW);
        $SaleType = $this->entityManager->find(SaleType::class, 1);

        $categoryDefinitions = [
            17 => [
                'name' => 'Furniture',
                'code_prefix' => 'FUR',
                'images' => ['cat_furniture.jpg'],
                'price_min' => 12000,
                'price_max' => 88000,
                'models' => [
                    'Nordic Minimalist Armchair',
                    'Ergonomic Mesh Office Chair',
                    'Modern Velvet Lounge Chair',
                    'Solid Oak Coffee Table',
                    'Modular Sectional Sofa',
                    'Adjustable Standing Desk',
                    'Scandinavian Bookshelf',
                    'Accent Recliner Chair',
                    'Walnut Dining Chair',
                    'Bedside Nightstand with Drawer',
                ],
                'desc_short' => 'Premium furniture crafted with sustainable materials, offering modern aesthetics and superior ergonomics for living and working spaces.',
                'desc_detail' => 'Designed with fine craftsmanship, durable hardwood frames, and ergonomic contouring. Fits seamlessly into contemporary modern interiors while offering unmatched everyday comfort and durability.',
            ],
            16 => [
                'name' => 'Headphone',
                'code_prefix' => 'HED',
                'images' => [
                    'cat_headphone.jpg',
                    'prod_headphone_1.jpg',
                    'prod_headphone_2.jpg',
                    'prod_headphone_3.jpg',
                    'prod_headphone_4.jpg',
                    'airpods_max_black.jpg',
                    'airpods_max_blue.jpg',
                    'airpods_max_silver.jpg',
                ],
                'price_min' => 3800,
                'price_max' => 45000,
                'models' => [
                    'Studio Pro ANC Wireless Headphones',
                    'Audiophile Over-Ear BT Headset',
                    'True Wireless Noise-Cancelling Buds',
                    'Ultra-Lightweight Foldable Headphones',
                    'Dynamic Bass Stereo Earphones',
                    'Gaming Surround Sound Headset',
                    'Sport Waterproof Running Earbuds',
                    'Lossless Hi-Res Audio Headset',
                    'Dual-Driver Studio Monitor Buds',
                    'Bluetooth 5.3 Daily Commute Headphones',
                ],
                'desc_short' => 'High performance wireless audio with premium sound quality, active noise cancellation, and ergonomic design.',
                'desc_detail' => 'Experience immersive audio with crystal clear highs and deep dynamic bass. Built with ultra-comfortable memory foam ear cushions, 40-hour battery life, and rapid USB-C fast charging.',
            ],
            8 => [
                'name' => 'Shoe',
                'code_prefix' => 'SHO',
                'images' => ['cat_shoe.jpg'],
                'price_min' => 4500,
                'price_max' => 24000,
                'models' => [
                    'Air Cushion Lightweight Sneakers',
                    'Breathable Mesh Running Shoes',
                    'Classic Low-Top Canvas Sneakers',
                    'Pro Sport Training Trainers',
                    'Ultra-Flex Slip-On Walking Shoes',
                    'Retro Streetwear Fashion Sneakers',
                    'Waterproof Trail Running Shoes',
                    'Comfort Foam Daily Footwear',
                    'Shock-Absorbing Athletic Shoes',
                    'Urban Minimalist White Sneakers',
                ],
                'desc_short' => 'Engineered for peak athletic performance and everyday street comfort with responsive cushioning and breathable construction.',
                'desc_detail' => 'Crafted with premium breathable knit uppers, responsive shock-absorbing midsoles, and high-traction rubber outsoles. Provides maximum all-day support whether running, walking, or exploring the city.',
            ],
            15 => [
                'name' => 'Bag',
                'code_prefix' => 'BAG',
                'images' => ['cat_bag.jpg'],
                'price_min' => 3200,
                'price_max' => 29000,
                'models' => [
                    'Waterproof Travel Duffle Bag',
                    'Urban Commuter Laptop Backpack',
                    'Premium Vegan Leather Tote',
                    'Multi-Compartment Crossbody Bag',
                    'Anti-Theft Travel Daypack',
                    'Lightweight Canvas Weekend Bag',
                    'Compact Messenger Sling Bag',
                    'Business Executive Briefcase',
                    'Roll-Top Cycling Backpack',
                    'Water-Resistant Gym Sport Duffle',
                ],
                'desc_short' => 'Durable, stylish bags engineered with weather-resistant fabrics, reinforced stitching, and smart organization pockets.',
                'desc_detail' => 'Features water-resistant coated fabric, dedicated padded laptop compartments up to 16-inch, heavy-duty YKK zippers, and ergonomic padded shoulder straps for seamless travel and daily commutes.',
            ],
            1 => [
                'name' => 'Laptop',
                'code_prefix' => 'LAP',
                'images' => ['cat_laptop.jpg'],
                'price_min' => 58000,
                'price_max' => 248000,
                'models' => [
                    'UltraBook Slim 14-inch Laptop',
                    'Pro Creator 16-inch OLED Laptop',
                    'Gaming RTX Power Laptop',
                    'Thin & Light Business Notebook',
                    '2-in-1 Touchscreen Convertible Laptop',
                    'Portable Workstation 15.6-inch',
                    'Fanless Silent Ultra-Portable Laptop',
                    'Developer Edition High-Memory Laptop',
                    'High-Refresh Studio Laptop',
                    'Compact 13-inch Travel Notebook',
                ],
                'desc_short' => 'Next-generation computing power with blazing fast processors, vivid high-resolution display, and all-day battery life.',
                'desc_detail' => 'Equipped with the latest high-performance processors, vibrant anti-glare display with high color accuracy, fast NVMe SSD storage, and all-day battery endurance in an ultra-sleek aerospace-grade aluminum chassis.',
            ],
            14 => [
                'name' => 'Book',
                'code_prefix' => 'BOK',
                'images' => ['cat_book.jpg'],
                'price_min' => 1600,
                'price_max' => 6500,
                'models' => [
                    'Modern Web Architecture Hardcover',
                    'The UX Design System Handbook',
                    'Mindful Creativity & Innovation',
                    'Data Science in Practice',
                    'The Minimalist Life Philosophy',
                    'Mastering Modern Typography',
                    'Artificial Intelligence & Humanity',
                    'The Product Manager Guide',
                    'Storytelling with Visual Design',
                    'Global Economics & Sustainable Future',
                ],
                'desc_short' => 'Thought-provoking, beautifully published books on design, technology, self-growth, and business innovation.',
                'desc_detail' => 'Printed on premium FSC-certified acid-free archival paper with elegant cloth hardcover binding. Packed with actionable insights, detailed illustrations, and world-class domain expertise.',
            ],
        ];

        $totalCreated = 0;

        foreach ($categoryDefinitions as $catId => $def) {
            $Category = $this->entityManager->find(Category::class, $catId);
            if (!$Category) {
                $io->warning(sprintf('Category ID %d not found, skipping.', $catId));
                continue;
            }

            $catName = $def['name'];
            $codePrefix = $def['code_prefix'];
            $images = $def['images'];
            $models = $def['models'];
            $priceMin = $def['price_min'];
            $priceMax = $def['price_max'];

            $io->section(sprintf('Generating %d products for [%s] (ID: %d)', $countPerCategory, $catName, $catId));

            for ($i = 1; $i <= $countPerCategory; $i++) {
                $modelBase = $models[($i - 1) % count($models)];
                $productName = sprintf('%s #%02d', $modelBase, $i);
                $productCode = sprintf('%s-%03d', $codePrefix, $i);

                // Varied prices
                $spread = $priceMax - $priceMin;
                $step = ($spread / $countPerCategory);
                $price02 = (float) round(($priceMin + ($i - 1) * $step) / 100) * 100;
                $price01 = (float) round(($price02 * 1.15) / 100) * 100;
                $stock = 25 + (($i * 3) % 76);

                $imageFile = $images[($i - 1) % count($images)];

                // 1. Create Product
                $Product = new Product();
                $Product
                    ->setName($productName)
                    ->setCreator($Member)
                    ->setStatus($ProductStatus)
                    ->setDescriptionList($def['desc_short'])
                    ->setDescriptionDetail(sprintf('%s %s is built for quality, durability, and daily performance.', $def['desc_detail'], $productName))
                    ->setSearchWord(sprintf('%s, %s, %s, %s', $catName, $modelBase, $codePrefix, $productCode))
                    ->setCreateDate(new \DateTime())
                    ->setUpdateDate(new \DateTime());

                $this->entityManager->persist($Product);
                $this->entityManager->flush();

                // 2. Create ProductStock
                $ProductStock = new ProductStock();
                $ProductStock
                    ->setStock($stock)
                    ->setCreator($Member)
                    ->setCreateDate(new \DateTime())
                    ->setUpdateDate(new \DateTime());

                $this->entityManager->persist($ProductStock);
                $this->entityManager->flush();

                // 3. Create ProductClass
                $ProductClass = new ProductClass();
                $ProductClass
                    ->setCode($productCode)
                    ->setCreator($Member)
                    ->setStock($stock)
                    ->setProductStock($ProductStock)
                    ->setProduct($Product)
                    ->setSaleType($SaleType)
                    ->setPrice01($price01)
                    ->setPrice02($price02)
                    ->setStockUnlimited(false)
                    ->setSaleLimit(5)
                    ->setVisible(true)
                    ->setCurrencyCode('JPY')
                    ->setCreateDate(new \DateTime())
                    ->setUpdateDate(new \DateTime());

                $this->entityManager->persist($ProductClass);
                $this->entityManager->flush();

                $ProductStock->setProductClass($ProductClass);
                $ProductStock->setProductClassId($ProductClass->getId());
                $this->entityManager->flush();

                $Product->addProductClass($ProductClass);

                // 4. Create ProductImage
                $ProductImage = new ProductImage();
                $ProductImage
                    ->setCreator($Member)
                    ->setFileName($imageFile)
                    ->setSortNo(1)
                    ->setCreateDate(new \DateTime())
                    ->setProduct($Product);

                $this->entityManager->persist($ProductImage);
                $this->entityManager->flush();

                $Product->addProductImage($ProductImage);

                // 5. Create ProductCategory
                $ProductCategory = new ProductCategory();
                $ProductCategory
                    ->setCategory($Category)
                    ->setProduct($Product)
                    ->setCategoryId($Category->getId())
                    ->setProductId($Product->getId());

                $this->entityManager->persist($ProductCategory);
                $this->entityManager->flush();

                $Product->addProductCategory($ProductCategory);

                $totalCreated++;

                if ($i % 10 === 0) {
                    $io->write(sprintf(' %d/%d', $i, $countPerCategory));
                }
            }
            $io->newLine();
        }

        $io->success(sprintf('Successfully aligned categories and created %d products (%d per category)!', $totalCreated, $countPerCategory));

        return Command::SUCCESS;
    }
}
