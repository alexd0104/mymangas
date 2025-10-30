<?php

namespace App\Form;

use App\Entity\Vitrine;
use App\Entity\Manga;
use App\Repository\MangaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// IMPORTANT: si tu veux typer explicitement, dé-commente EntityType ci-dessous
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class VitrineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Vitrine|null $vitrine */
        $vitrine = $options['data'] ?? null;
        $member  = $vitrine?->getCreateur();                 // propriétaire de la vitrine
        $biblio  = $member?->getBibliotheque();              // sa bibliothèque

        $builder
            ->add('description')
            ->add('publiee')
            ->add('createur', null, [
                'disabled' => true,                           // on n’autorise pas à changer le créateur ici
            ])
            ->add('mangas',
                // tu peux laisser "null" mais je conseille d'utiliser explicitement EntityType:
                EntityType::class,
                [
                    'class'        => Manga::class,
                    'choice_label' => function (Manga $m) {
                        // libellé sympa dans le formulaire
                        $serie = $m->getSerie();
                        $tome  = $m->getTome();
                        return $tome ? sprintf('%s — tome %d', $serie, $tome) : $serie;
                    },

                    // clé pour sauvegarder correctement une collection ManyToMany
                    'by_reference' => false,

                    // multi-sélection; "expanded: true" = cases à cocher
                    'multiple'     => true,
                    'expanded'     => true,

                    // on filtre les mangas proposés : uniquement ceux de la biblio du créateur
                    'query_builder' => function (MangaRepository $er) use ($biblio) {
                        $qb = $er->createQueryBuilder('m')
                                ->orderBy('m.serie', 'ASC')
                                ->addOrderBy('m.tome', 'ASC');

                        if ($biblio !== null) {
                            $qb->andWhere('m.bibliotheque = :b')
                               ->setParameter('b', $biblio);
                        } else {
                            // cas défensif : si pas de biblio (ne devrait pas arriver), on vide la liste
                            $qb->andWhere('1 = 0');
                        }

                        return $qb;
                    },
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vitrine::class,
        ]);
    }
}
