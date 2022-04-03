<?php

namespace App\Controller\Admin;

use App\Entity\People;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class PeopleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return People::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Человек')
            ->setEntityLabelInPlural('Люди')
            ->setSearchFields(['first_name', 'second_name', 'middle_name', 'address_residental', 'contacts'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('first_name'))
            ->add(EntityFilter::new('phones'))
            ->add(EntityFilter::new('last_view_addresses'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('second_name', 'Фамилия');
        yield TextField::new('first_name', 'Имя');
        yield TextField::new('middle_name', 'Отчество');
        yield DateTimeField::new('birthday_date', 'Дата рождения')->setFormTypeOptions([
            'html5' => true,
//            'years' => range(date('Y') - 100, date('Y')),
            'widget' => 'single_text',
        ]);
        yield TextField::new('address_residental', 'Место проживания')
            ->setHelp('Где человек проживал постоянно.');
        yield ArrayField::new('contacts', 'Контакты для связи')
            ->setHelp('Можно укзать номер, почту, соцсеть и другие контакты');
        yield ChoiceField::new('state', 'Состояние')
            ->setChoices([
                'submitted' => 'submitted',
                'spam' => 'spam',
                'potential_spam' => 'potential_spam',
                'ham' => 'ham',
                'reject' => 'reject',
                'published' => 'published',
            ]);

    }
}
