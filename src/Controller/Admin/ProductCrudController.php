<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name', 'Nom du produit'),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex(),
            IntegerField::new('price', 'Prix'),
            TextField::new('pictureFile', 'Photo du produit')->setFormType(VichImageType::class),
            ImageField::new('picture')->setBasePath('/uploads/images')->onlyOnIndex(),
            TextField::new('shortDescription', 'Description'),
            AssociationField::new('category', 'Catégorie')

        ];
    }
    
}
