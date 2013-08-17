<?php

namespace Richpolis\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Richpolis\BackendBundle\Entity\Configuraciones;

class ConfiguracionesImagenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('configuracion')
            ->add('file','file',array(
                'label'=>'Imagen',
                'required'=>false,
            ))
            ->add('slug','hidden')
            ->add('texto','hidden')
            ->add('tipoConfiguracion','hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\BackendBundle\Entity\Configuraciones'
        ));
    }

    public function getName()
    {
        return 'richpolis_backendbundle_configuracionestype';
    }
}
