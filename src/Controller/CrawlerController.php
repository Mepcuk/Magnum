<?php

namespace App\Controller;

use App\Entity\Catalog;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Panther\Client;

class CrawlerController extends AbstractController
{

    /**
     * @Route("/crawler", name="app_crawler")
     */
    public function index()
    {
        set_time_limit(6000);
        $count = 0;

        $catalogRepository = $this->getDoctrine()->getRepository(Catalog::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $browser = new HttpBrowser(HttpClient::create());
        $browser->request('GET', 'https://www.eis.gov.lv/EIS/Categories/CategoryList.aspx?CategoryId=23411');

        $breadcrumbsCategory = $browser->getCrawler()->filter('.breadcrumbs > a');
        $categoryBreadcrumbData = $breadcrumbsCategory->each(
            function(Crawler $node, $i) {
                $categoryLink = $node->attr('href');
                $categoryName = $node->text();
                return ([
                    'categoryLink' => $this->getCategoryId($categoryLink),
                    'categoryName' => $categoryName
                ]);
            }
        );

        $parentId = -1;
        foreach ($categoryBreadcrumbData as $value ) {
            // Checking if value exist in db
            $catalog = $catalogRepository->findOneBy([
                'description'   => $value['categoryName'],
                'link'          => $value['categoryLink']
            ]);

            // if not exist add to database
            if (!$catalog) {
                $parentId = $this->addCatalog($value['categoryName'], $value['categoryLink'], $parentId, 0);
            }
        }

        // Static menu from breadcrumbs
        $currentStaticBreadcrumb = $browser->getCrawler()->filter('.breadcrumbs .static')->text();
        $currentLink = $browser->getCrawler()->getUri();
        $catalog = $catalogRepository->findOneBy([
            'description'   => $currentStaticBreadcrumb,
            'link'          => $this->getCategoryId($currentLink)
        ]);

        if (!$catalog) {
            $parentId = $this->addCatalog($currentStaticBreadcrumb, $this->getCategoryId($currentLink), $parentId, 0);
        }

        $category = $browser->getCrawler()->filter('.cataloglistcell');
        $categoryData = $category->each(
            function (Crawler $node, $i) {
                $categoryLink = $node->filter('.cataloglistcell > div > div > h3 > a')->attr('href');
                $categoryName = $node->filter('.cataloglistcell > div > div > h3 > a')->text();
                return ([
                    'categoryLink' => $this->getCategoryId($categoryLink),
                    'categoryName' => $categoryName
                ]);
            }
        );



        $client = Client::createChromeClient();

        // Active Category for Product Scanning
        foreach ($categoryData as $value ) {
            // Checking if value exist in db
            $catalog = $catalogRepository->findOneBy([
                'description'   => $value['categoryName'],
                'link'          => $value['categoryLink']
            ]);

            // if not exist add to database
            if (!$catalog) {
//                dd($value);
                $this->addCatalog($value['categoryName'], ($value['categoryLink']), $parentId, 1);
            }
                $browser->request('GET', 'https://www.eis.gov.lv/EIS/Categories/CategoryList.aspx?CategoryId=' . $value['categoryLink']);

                $productData        = $browser->getCrawler()->filter('tr > td > b > a');
                $productsInCategory = $productData->each(
                    function(Crawler $node, $i) {
                        $productId = $node->attr('id');

                        return ([
                            'productId' => $productId
                        ]);
                    }
                );
                // Scanning catalog for products
                foreach ($productsInCategory as $product ) {

                    $jsLink = "document.querySelector('#" .  $product['productId'] . "').click()";
                    $client->request('GET', 'https://www.eis.gov.lv/EIS/Categories/CategoryList.aspx?CategoryId=' . $value['categoryLink']);
                    $client->executeScript($jsLink);
                    sleep(4);

                    $vvPositionNumber   = $client->getCrawler()->filter('#ctl00_uxMainContent_uxFilteredCheapestProductListControl_uxProductInfoControl_uxFaNumberRow > .formfield')->text();
                    $title              = $client->getCrawler()->filter('#ctl00_uxMainContent_uxFilteredCheapestProductListControl_uxProductInfoControl_uxNameRow > .formfield')->text();
                    $itemCode           = $client->getCrawler()->filter('#ctl00_uxMainContent_uxFilteredCheapestProductListControl_uxProductInfoControl_uxSupplierProductCodeRow > .formfield')->text();
                    $priceWoVAT         = $client->getCrawler()->filter('#ctl00_uxMainContent_uxFilteredCheapestProductListControl_uxProductInfoControl_uxOnePriceRow > .formfield')->text();

                    //TODO No check for product exist and logic if need to check if exist, added time
                    $this->addProduct($title, $vvPositionNumber, $itemCode, $priceWoVAT, $catalog);
                    $count++;
                }
        }

        return $this->render('crawler/index.html.twig', [
            'product_count' => $count,
        ]);
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {

        return $this->render('crawler/index.html.twig', [
        ]);
    }


    public function getCategoryId(string $categoryLink)
    {
            $categoryId = ltrim($categoryLink, "https://www.eis.gov.lv/EIS/Categories/CategoryList.aspx?");
//            $categoryId = ltrim($categoryLink, "CategoryList.aspx?");
            if( strlen($categoryId) > 0 ){
                $parentId = ltrim($categoryId, "CategoryId=");
            } else {
                $parentId = 0;
            }
        return $parentId;
    }

    public function addCatalog(string $description, string $categoryId, int $parentId, int $scanCategory)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $addCatalog = new Catalog();

        $addCatalog->setDescription($description);
        $addCatalog->setLink($categoryId);
        $addCatalog->setParentcategory($parentId);
        $addCatalog->setCreatedAt(new \DateTime());
        $addCatalog->setUpdatedAt(new \DateTime());
        $addCatalog->setScanCategory($scanCategory);

        $entityManager->persist($addCatalog);
        $entityManager->flush();
        $parentId = $addCatalog->getLink();

        return $parentId;
    }

    public function addProduct(string $title, string $positionNumber, string $itemCode, float $priceWoVAT , $catalog)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();

        $product->setTitle($title);
        $product->setPositionNumber($positionNumber);
        $product->setProductCode($itemCode);
        $product->setPriceWoVAT($priceWoVAT);
        $product->setCatalog($catalog);
        $product->setUpdatedAt(new \DateTime());

        $entityManager->persist($product);
        $entityManager->flush();

    }
}
