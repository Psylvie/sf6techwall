<?php

namespace App\Form;

use App\Entity\EntityAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityAttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entityname', null, [
                'attr' => [
                    'placeholder' => 'Entity Name',
                ],
                'label' => 'Entity Name',
                'required' => true,
            ])
            ->add('filepath', FileType::class, [
                'attr' => [
                    'placeholder' => 'File',
                ],
                'label' => 'File',
                'required' => true,
            ])
            ->add('description', null, [
                'attr' => [
                    'placeholder' => 'Description',
                ],
                'label' => 'Description',
                'required' => true,
            ])
            ->add('entityId', null, [
                'attr' => [
                    'placeholder' => 'Entity ID',
                ],
                'label' => 'Entity ID',
                'required' => true,
            ])
            ->add('type', null, [
                'attr' => [
                    'placeholder' => 'Type',
                ],
                'label' => 'Type',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntityAttachment::class,
        ]);
    }
}
