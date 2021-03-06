<?php

namespace Richpolis\PublicacionesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Richpolis\PublicacionesBundle\Entity\Categorias;

/**
 * CategoriasPublicacionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriasPublicacionRepository extends EntityRepository
{
    public function getMaxPosicion($tipo=0){
        $em=$this->getEntityManager();
        if($tipo==0){
            $query=$em->createQuery('
                SELECT COUNT(p.posicion) as value 
                FROM PublicacionesBundle:CategoriasPublicacion p 
                ORDER BY p.posicion ASC
            ');
        }else{
            $query=$em->createQuery('
                SELECT COUNT(p.posicion) as value 
                FROM PublicacionesBundle:CategoriasPublicacion p 
                WHERE p.tipoCategoria=:tipo 
                ORDER BY p.posicion ASC
            ')->setParameter('tipo', $tipo);
        }
        $max=$query->getResult();
        return $max[0]['value'];
    }
    
    public function getCategoriaActualPorTipoCategoria($tipoCategoria){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                    SELECT p,g FROM PublicacionesBundle:CategoriasPublicacion p 
                    LEFT JOIN p.galerias g 
                    WHERE p.tipoCategoria = :tipo 
                    AND g.isActive=:activeGaleria 
                    AND p.isActive = :active 
                    ORDER BY g.posicion ASC
                ')->setParameters(array('tipo'=> $tipoCategoria,'active'=>true,'activeGaleria'=>true));
        $categorias=$query->getResult();
        if(isset($categorias[0])){
            return $categorias[0];
        }else{
            $query=$em->createQuery('
                    SELECT p FROM PublicacionesBundle:CategoriasPublicacion p 
                    WHERE p.tipoCategoria = :tipo 
                    AND p.isActive = :active
                ')->setParameters(array('tipo'=> $tipoCategoria,'active'=>true));
            $categorias=$query->getResult();
            return $categorias[0];
        }
    }
    
    public function getCategoriaConGaleriaPorId($categoria_id,$active=true){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
               SELECT p,g 
               FROM PublicacionesBundle:CategoriasPublicacion p 
               JOIN p.galerias g 
               WHERE p.id = :categoria 
               AND g.isActive = :active 
               ORDER BY g.posicion DESC
        ')->setParameters(array('categoria'=> $categoria_id,'active'=>$active));
        
        $categorias=$query->getResult();
        if(isset($categorias[0])){
            return $categorias[0];
        }else{
            return null;
        }
    }
    public function getQueryCategoriasPorTipoCategoria($tipoCategoria,$categoria_actual=0,$active=true){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                    SELECT c FROM PublicacionesBundle:CategoriasPublicacion c
                    WHERE c.tipoCategoria = :tipo 
                    AND c.id <> :actual 
                    AND c.isActive = :active 
                    ORDER BY c.posicion ASC 
                ')->setParameters(array(
                    'tipo'=> $tipoCategoria,
                    "actual"=>$categoria_actual,
                    'active'=>$active
                ));
        return $query;
    }
    
    public function getCategoriasPorTipoCategoria($tipoCategoria,$categoria_actual=0,$active=true){
        $query=$this->getQueryCategoriasPorTipoCategoria($tipoCategoria,$categoria_actual,$active);
        return $query->getResult();
    }
    
    public function getQueryCategoriasPorTipoYActivas($tipoCategoria,$todas=false){
        $query = $this->getEntityManager()->createQueryBuilder();
                $query->select('c,p')
                    ->from('Richpolis\PublicacionesBundle\Entity\CategoriasPublicacion', 'c')
                    ->leftJoin('c.publicaciones', 'p')
                    ->where('c.tipoCategoria=:tipo')
                    ->setParameter('tipo', $tipoCategoria)
                    ->orderBy('c.posicion', 'ASC')
                    ->addOrderBy('p.posicion','DESC')
                ; 
        if(!$todas){
            $query->andWhere('c.isActive=:active')
                  ->setParameter('active', true);
        }
        return $query->getQuery();
    }
    
    public function getCategoriasPorTipoYActivas($tipo,$todas=false){
        $query=$this->getQueryCategoriasPorTipoYActivas($tipo, $todas);
        return $query->getResult();
    }
    
    public function getQueryCategoriasPublicacionActivas($todas=false){
        $query=$this->createQueryBuilder('c')
                    ->orderBy('c.posicion', 'ASC');
        if(!$todas){
            $query->andWhere('c.isActive=:active')
                  ->setParameter('active', true);
        }
        return $query->getQuery();
    }
    
    public function getCategoriasPublicacionActivas($todas=false){
        $query=$this->getQueryCategoriasPublicacionActivas($todas);
        return $query->getResult();
    }
    
    public function getCategoriasActuales(){
        $em=$this->getEntityManager();
        $query=$em->createQuery('
                    SELECT DISTINCT c,p 
                    FROM PublicacionesBundle:CategoriasPublicacion c 
                    LEFT JOIN c.publicaciones p 
                    WHERE c.isActive=:active 
                    AND g.isActive=:activeGaleria 
                    ORDER BY c.tipoCategoria,c.posicion ASC, p.posicion DESC 
                ')->setParameters(array('active'=>true,'activeGaleria'=>true));
        return $query->getResult();
    }
    
    public function getCategoriasValidas($registros){
        $categorias=array();
        $categorias['tipo'.Categorias::$GALERIA]=null;
        $categorias['tipo'.Categorias::$RECOMENDACIONES]=null;
        $lugar=0;
        
        foreach($registros as $registro){
            $lugar=$registro->getTipoCategoria();
            $categorias['tipo'.$lugar]=$registro;
        }
        
        return $categorias;        
    }
    
    public function getRegistroUpOrDown($posicionRegistro,$up=true){
        // $up = true, $up = false is down
        if($up){
            //up
            $query=$this->createQueryBuilder('c')
                    ->where('c.posicion>:posicion')
                    ->setParameter('posicion', $posicionRegistro)
                    ->orderBy('c.posicion', 'DESC');
        }else{
            //down
            $query=$this->createQueryBuilder('c')
                    ->where('c.posicion<:posicion')
                    ->setParameter('posicion', $posicionRegistro)
                    ->orderBy('c.posicion', 'DESC');
        }
        
        return $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
    
}