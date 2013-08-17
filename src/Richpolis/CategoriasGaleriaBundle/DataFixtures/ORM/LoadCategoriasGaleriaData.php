<?php


namespace Richpolis\GalMonBundle\DataFixtures\ORM;
 
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Richpolis\CategoriasGaleriaBundle\Entity\Categorias;
 
class LoadCategoriasData extends AbstractFixture implements OrderedFixtureInterface
{
  public function load(ObjectManager $em)
  {
    $galeria = new Categorias();
    $galeria->setCategoria('Galerias');
    $galeria->setTipoCategoria(Categorias::$GALERIA);
    $galeria->setPosicion(1);
 
    $recomendaciones = new Categorias();
    $recomendaciones->setCategoria('Recomendaciones');
    $recomendaciones->setTipoCategoria(Categorias::$RECOMENDACIONES);
    $recomendaciones->setPosicion(2);
    
    $publicaciones = new Categorias();
    $publicaciones->setCategoria('Publicaciones');
    $publicaciones->setTipoCategoria(Categorias::$PUBLICACIONES);
    $publicaciones->setPosicion(3);
    
    $em->persist($galeria);
    $em->persist($recomendaciones);
    $em->persist($publicaciones);
    
    $em->flush();
 

  }
 
  public function getOrder()
  {
    return 1; // the order in which fixtures will be loaded
  }
}