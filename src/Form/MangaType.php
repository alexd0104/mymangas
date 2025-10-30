<?php

namespace App\Form;

use App\Entity\Bibliotheque;
use App\Entity\Manga;
use App\Entity\Vitrine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MangaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serie')
            ->add('tome')
            ->add('vitrines', EntityType::class, [
                'class' => Vitrine::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('imageName', TextType::class, ['disabled' => true, 'required' => false, 'label' => 'Fichier'])
            ->add('imageFile', VichImageType::class, [
                'required'      => false,
                'allow_delete'  => true,
                'download_uri'  => false,
                'label'         => 'Image (JPG/PNG/WebP…)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Manga::class,
        ]);
    }
}
