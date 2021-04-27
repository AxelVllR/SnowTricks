<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Tricks;
use App\Repository\GroupsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            //->add('video_urls')
            //->add('updated_at')
            //->add('created_at')
            ->add('group_trick', EntityType::class, [
                'class' => Groups::class,
                'query_builder' => function (GroupsRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                'choice_label' => 'name'
            ])
            /*->add('pictureFiles', FileType::class, [
                'required' => false,
                'multiple' => true
            ])*/
            //->add('updated_by')
            //->add('created_by')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
            'translation_domain' => 'forms'
        ]);
    }
}
