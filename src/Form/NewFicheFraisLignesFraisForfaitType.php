<?php

namespace App\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewFicheFraisLignesFraisForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ficheFrais = $options['fiche'];

        $builder

            ->add('ForfaitEtape', IntegerType::class, [
                'label' => 'Forfait',
                'data' => $ficheFrais->getLigneFraisForfaits()[0]->getQuantite(),

            ])

            ->add('FraisKilometrique', IntegerType::class,  [
                'label' => 'Kilomètres',
                'data' => $ficheFrais->getLigneFraisForfaits()[1]->getQuantite(),
            ])

            ->add('NuiteeHotel', IntegerType::class, [
                'label' => 'Nuitée',
                'data' => $ficheFrais->getLigneFraisForfaits()[2]->getQuantite(),

            ])
            ->add('RepasRestaurant', IntegerType::class, [
                'label' => 'Montant',
                'data' => $ficheFrais->getLigneFraisForfaits()[3]->getQuantite(),
            ])

            ->add('Valider', SubmitType::class, [
                'label' => 'Valider'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'fiche' => null,
        ]);
    }
}
