<?php

namespace App\Form;

use App\Entity\Vitrine;
use App\Entity\Manga;
use App\Repository\MangaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Member;

class VitrineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \App\Entity\Vitrine|null $vitrine */
        $vitrine = $options['data'] ?? null;
        $member = $vitrine?->getCreateur();

        $builder
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('publiee', null, [
                'label' => 'Publiée',
                'required' => false,
            ])
            ->add('createur', EntityType::class, [
                'class' => Member::class,
                'choice_label' => 'email',
                'label' => 'Créateur',
                'disabled' => true,
            ])
            ->add('mangas', EntityType::class, [
                'class' => Manga::class,
                'choice_label' => 'titre',
                'multiple' => true,
                'expanded' => true,      // cases à cocher
                'by_reference' => false, // important pour ManyToMany
                'label' => 'Mangas',
                // filtrage aux mangas du créateur, si disponible
                'query_builder' => function (MangaRepository $er) use ($member) {
                    if (!$member) {
                        return $er->createQueryBuilder('m')->where('1 = 0');
                    }
                    return $er->createQueryBuilder('m')
                        ->leftJoin('m.bibliotheque', 'b')
                        ->andWhere('b.proprietaire = :m')
                        ->setParameter('m', $member)
                        ->orderBy('m.titre', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vitrine::class,
        ]);
    }
}
