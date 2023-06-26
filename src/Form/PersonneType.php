<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Personne;
use App\Entity\Profile;
use App\Entity\Tag;
use App\Form\DataTransformer\TagsTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PersonneType extends AbstractType
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('name')
            ->add('age')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('profile', EntityType::class, [
                'expanded'=>false,
                'required' => false,
                'class'=>Profile::class,
                'multiple'=>false,
                'attr'=> [
                    'class'=>'select2'
                ]])
            ->add('hobbies', EntityType::class, [
                'expanded'=>false,
                'class'=>Hobby::class,
                'required' => false,
                'multiple'=>true,
                // par ordre alphabetique
                'query_builder'=> function (EntityRepository $er){
                return $er->createQueryBuilder('h')
                    ->orderBy('h.designation', 'ASC');
                },
                'choice_label'=> 'designation',
                'attr'=> [
                    'class'=>'select2'
                ]
                ])
            ->add('job', EntityType::class,[
                'required' => false,
                'class'=>Job::class,
                'attr'=> [
                    'class'=>'select2'
                ]

            ])
            ->add('tags', TextType::class, [
                'mapped' => false, // Ne pas mapper ce champ à l'entité Personne
                'required' => false, // Rendre le champ facultatif
                'attr' => [
                    'placeholder' => 'Ajouter des tags',
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Image de votre profil',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])


            ->add('editer', SubmitType::class);
//            ->add('tags', CollectionType::class, [
//                'entry_type' => EntityType::class,
//                'entry_options' => [
//                    'class' => Tag::class,
//                    'choice_label' => 'name',
//                ],
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false,
//            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
