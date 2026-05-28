<?php

namespace App\Controller;

use App\Repository\Catalog\CategoryRepository;
use App\Repository\Catalog\CrossReferenceRepository;
use App\Repository\Catalog\OitmRepository;
use App\Service\Catalog\CatalogSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/catalog', name: 'catalog_')]
class CatalogController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly OitmRepository $oitmRepository,
        private readonly CrossReferenceRepository $crossReferenceRepository,
        private readonly CatalogSearchService $catalogSearchService,
    ) {
    }

    /** /catalog — list all part groups */
    #[Route('', name: 'groups')]
    public function groups(): Response
    {
        $groups = $this->categoryRepository->findPartGroups();

        return $this->render('catalog/groups.html.twig', [
            'groups' => $groups,
        ]);
    }

    /** /catalog/group/{partGroup} — list components within a group */
    #[Route('/group/{partGroup}', name: 'components')]
    public function components(string $partGroup): Response
    {
        $components = $this->categoryRepository->findPartComponentsByGroup($partGroup);

        return $this->render('catalog/components.html.twig', [
            'partGroup'  => $partGroup,
            'components' => $components,
        ]);
    }

    /** /catalog/group/{partGroup}/component/{partComponent} — product list */
    #[Route('/group/{partGroup}/component/{partComponent}', name: 'results')]
    public function results(Request $request, string $partGroup, string $partComponent): Response
    {
        $vehicleId = $request->query->getInt('vehicleId') ?: null;
        $page      = max(1, $request->query->getInt('page', 1));
        $limit     = 50;
        $offset    = ($page - 1) * $limit;

        $products = $this->catalogSearchService->browseByGroupAndComponent(
            $partGroup,
            $partComponent,
            $vehicleId,
            $limit,
            $offset,
        );

        return $this->render('catalog/results.html.twig', [
            'partGroup'     => $partGroup,
            'partComponent' => $partComponent,
            'products'      => $products,
            'vehicleId'     => $vehicleId,
            'page'          => $page,
            'limit'         => $limit,
        ]);
    }

    /** /catalog/search?q=... — OEM / article number / item code search */
    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $query     = trim((string) $request->query->get('q', ''));
        $vehicleId = $request->query->getInt('vehicleId') ?: null;
        $products  = [];

        if ($query !== '') {
            $products = $this->catalogSearchService->search($query, $vehicleId);
        }

        return $this->render('catalog/results.html.twig', [
            'query'         => $query,
            'partGroup'     => null,
            'partComponent' => null,
            'products'      => $products,
            'vehicleId'     => $vehicleId,
            'page'          => 1,
        ]);
    }

    /** /catalog/product/{id} — product detail with cross references */
    #[Route('/product/{id}', name: 'detail')]
    public function detail(int $id): Response
    {
        $product = $this->oitmRepository->findById($id);

        if ($product === null) {
            throw $this->createNotFoundException('Product not found.');
        }

        $crossReferences = $this->crossReferenceRepository->findByOitmId($id);

        return $this->render('catalog/detail.html.twig', [
            'product'         => $product,
            'crossReferences' => $crossReferences,
        ]);
    }
}
