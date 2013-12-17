<?php

namespace Richpolis\PublicacionesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Repository\CategoriasPublicacionRepository;

class PublicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo')
            ->add('descripcion','genemu_tinymce',array(
                    'attr'=>array('cols' => 50,'rows' => 10,))
                 )
            ->add('categoria','entity', array(
                'class' => 'PublicacionesBundle:CategoriasPublicacion',
                'query_builder' => function(CategoriasPublicacionRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->orderBy('u.id', 'ASC');
                },
                'property'  => 'categoria',
                'label'     => 'Categoria',
                'multiple'  => false
            ))
            ->add('file','file',array(
                'label'=>'Imagen',
                'required'=>false,
                ))
            ->add('posicion','hidden')
            ->add('thumbnail','hidden')
            ->add('slug','hidden')
            ->add('isActive',null,array('label'=>'Activo?'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Richpolis\PublicacionesBundle\Entity\Publicacion'
        ));
    }

    public function getName()
    {
        return 'richpolis_publicacionesbundle_publicaciontype';
    }
}
