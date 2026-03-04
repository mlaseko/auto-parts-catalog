<?php

namespace App\Form\Type;

use App\Api\CatalogApi;
use App\Entity\Languages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageType extends AbstractType
{
    private CatalogApi $catalogApi;

    public function __construct(CatalogApi $catalogApi)
    {
        $this->catalogApi = $catalogApi;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Fetch the languages from the API
        $response = $this->catalogApi->getAllLanguages();
        $languages = $response['data'] ?? [];

        // Map the API response to choices (assuming 'lngDescription' is a valid field in the response)
        $languageChoices = [];
        foreach ($languages as $language) {
            $languageChoices[$language['lngDescription']] = $language['lngId'];
        }

        // Fetch the vehicle types from the API
        $vehicleTypesResponse = $this->catalogApi->getAllVehicleTypes();
        $vehicleTypes = $vehicleTypesResponse['data'] ?? [];

        $vehicleTypesChoices = [];
        foreach ($vehicleTypes as $vehicleType) {
            $vehicleTypesChoices[$vehicleType['vehicleType']] = $vehicleType['id'];
        }

        $builder
            ->add('lngDescription', ChoiceType::class, [
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'placeholder' => 'lngDescription', 'autocomplete' => 'off'],
                'label' => 'Lang list',
                'required' => true,
                'choices' => $languageChoices,
                'placeholder' => 'Please select one Lang',
            ])

            ->add('countryFilter', ChoiceType::class, [
                'mapped' => false,
                'label_attr' => ['class' => 'form-label'],
            ])

            ->add('type', ChoiceType::class, [
                'label_attr' => ['class' => 'form-label'],
                'choices' => $vehicleTypesChoices,
                'attr' => ['class' => 'form-control', 'placeholder' => 'type'],
            ])

            ->add('show', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ])

        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $form
                ->add('countryFilter', null)
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'languages_type';
    }
}
