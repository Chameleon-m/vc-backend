<?php

namespace App\Controller\Admin;

use App\Entity\PeoplePhoto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class PeoplePhotoCrudController extends AbstractCrudController
{
    private string $uploadBasePath;
    private string $uploadDir;
    private string $peoplePhotoDir;

    public function __construct(string $uploadBasePath, string $uploadDir, string $peoplePhotoDir)
    {
        $this->uploadBasePath = $uploadBasePath;
        $this->uploadDir = $uploadDir;
        $this->peoplePhotoDir = $peoplePhotoDir;
    }

    public static function getEntityFqcn(): string
    {
        return PeoplePhoto::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Фотография')
            ->setEntityLabelInPlural('Фотографии')
            ->setSearchFields(['filename'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('filename'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield AssociationField::new('people', 'Человек');

        yield ImageField::new('filename', 'Фото')
            ->setBasePath($this->uploadBasePath . $this->peoplePhotoDir)
            ->setUploadDir($this->uploadDir . $this->peoplePhotoDir)
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->onlyOnIndex();

        yield IntegerField::new('priority', 'Очерёдность');
    }
}
