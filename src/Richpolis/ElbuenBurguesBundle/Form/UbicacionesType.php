<?php

namespace Richpolis\ElbuenBurguesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UbicacionesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('direccion','text')
            ->add('latitude','text',array('label'=>'latitude'))
            ->add('longitude','text',array('label'=>'longitude'))
            
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\ElbuenBurguesBundle\Entity\Ubicaciones'
        ));
    }

    public function getName()
    {
        return 'richpolis_elbuenburguesbundle_ubicacionestype';
    }
}
