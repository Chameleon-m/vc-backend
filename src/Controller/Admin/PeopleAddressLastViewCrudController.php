<?php

namespace App\Controller\Admin;

use App\Entity\PeopleAddressLastView;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class PeopleAddressLastViewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PeopleAddressLastView::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Место')
            ->setEntityLabelInPlural('Места')
            ->setSearchFields(['locality_value', 'address', 'note'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('people'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield AssociationField::new('people');
        yield IntegerField::new('locality_type');
        yield TextField::new('locality_value');
        yield TextField::new('address');
        yield TextField::new('note')
//        yield TextareaField::new('note')
//        yield TextEditorField::new('note')
            ->hideOnIndex();

        $dateStart = DateTimeField::new('date_start', 'С какого')->setFormTypeOptions([
            'html5' => true,
            'years' => [2022],
            'widget' => 'single_text',
        ]);
//        if (Crud::PAGE_EDIT === $pageName) {
//            yield $dateStart->setFormTypeOption('disabled', true);
//        } else {
            yield $dateStart;
//        }

        yield DateTimeField::new('date_end', 'По какое')->setFormTypeOptions([
            'html5' => true,
            'years' => range(date('Y'), date('Y') + 5),
            'widget' => 'single_text',
        ]);
    }
}
