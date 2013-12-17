<?php

namespace Richpolis\PublicacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\PublicacionesBundle\Entity\CategoriasPublicacion;

class CategoriasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categoria')
            ->add('descripcion','textarea', array(
                    'attr' => array(
                        'class' => 'tinymce',
                        'data-theme' => 'advanced' // Skip it if you want to use default theme
                    )
                ))
            ->add('tipoCategoria','choice',array(
                'label'=>'Tipo',
                'empty_value'=>false,
                'choices'=>Categorias::getArrayTipoCategorias(),
                'preferred_choices'=>Categorias::getPreferedTipoCategoria()
                ))
            ->add('posicion',"hidden")
            ->add('isActive',null,array('label'=>'Activo?','required'=>false))
            ->add('isCategoria',null,array('label'=>'Categorias?','required'=>false))    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicacionesBundle\Entity\CategoriasPublicacion'
        ));
    }

    public function getName()
    {
        return 'richpolis_publicacionesbundle_categoriaspublicaciontype';
    }
}
