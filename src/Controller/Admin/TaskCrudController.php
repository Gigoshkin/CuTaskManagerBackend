<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('nameEnglish'),
            TextField::new('nameGeorgian')
                ->hideOnIndex(),
            TextEditorField::new('descriptionEnglish')
                ->hideOnIndex(),
            TextEditorField::new('descriptionGeorgian')
                ->hideOnIndex(),
            DateTimeField::new('expiresAt'),
            AssociationField::new('owner')
        ];
    }

}
