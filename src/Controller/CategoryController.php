<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category1Type;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{    
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category, CategoryRepository $categoryRepository, ProductRepository $productRepository, $id): Response
    {
        //$products = $category->getProducts();
        //$products = $productRepository->findByGroup($id);
        
        $products = $productRepository->findBy(['category' => $id]);

        $products_final = $dimensions = [];
        foreach($products as $key => $product){
            if(!in_array($product->getName(), $products_final))
            {
                $products_final[] = $product;
                $dimensions[$product->getName()][] = $product->getDimensions(); 
            }
            else
            {
                $dimensions[$product->getName()][] = $product->getDimensions();
            }
        }
        //dump($products_final);
        //dd($dimensions);

        $categories = $categoryRepository->findAll();
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products_final,
            'dimensions' => $dimensions,
        ]);
    }

}
