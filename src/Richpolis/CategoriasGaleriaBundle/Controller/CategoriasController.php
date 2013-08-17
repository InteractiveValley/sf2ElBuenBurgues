<?php

namespace Richpolis\CategoriasGaleriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\CategoriasGaleriaBundle\Entity\Categorias;
use Richpolis\CategoriasGaleriaBundle\Form\CategoriasType;


/**
 * Categorias controller.
 *
 * @Route("/categorias")
 */
class CategoriasController extends Controller
{
    protected function getFilters()
    {
        $filters=$this->get('session')->get('filters', array());
        return $filters;
    }
    
    /**
     * Lists all Categorias entities.
     *
     * @Route("/", name="categorias")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $filters = $this->getFilters();

        if(!isset($filters['categorias']))
            $filters['categorias']=Categorias::$GALERIA;
        
        if($filters['categorias']==Categorias::$GALERIA){
            $galeriaoficial=true;
        }else{
            $galeriaoficial=false;
        }

        $query = $em->getRepository('CategoriasGaleriaBundle:Categorias')
                            ->getQueryCategoriasPorTipoYActivas($filters['categorias'],true);
        
        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            10
        );


        
        return array(
            'tipos'         =>  Categorias::getArrayTipoCategorias(),
            'tipo_categoria'=>  $filters['categorias'],
            'pagination' => $pagination,
            'galeriaoficial'=>$galeriaoficial,
        );
    }
    
    /**
     * Seleccionar un tipo de categoria.
     *
     * @Route("/seleccionar", name="categorias_seleccionar")
     */
    public function selectAction()
    {
        $filters = $this->getFilters();
        
        if(isset($filters['categorias'])){
            return $this->redirect($this->generateUrl('categorias'));
        }else{
            return $this->render('CategoriasGaleriaBundle:Categorias:select.html.twig', array(
                'tipos'  => Categorias::getArrayTipoCategorias(),
            ));
        }
    }
    
    /**
     * Mostrar categoria por tipo
     *
     * @Route("/list/{tipo}/tipo", name="categorias_por_tipo")
     */
    public function porTipoAction($tipo)
    {
        $filters = $this->getFilters();
        if($tipo){
            $filters['categorias']=$tipo;
            $this->get('session')->set('filters',$filters);
            return $this->redirect($this->generateUrl('categorias'));
        }else{
            if(isset($filters['categorias'])){
                return $this->redirect($this->generateUrl('categorias'));
            }else{
                return $this->render('CategoriasGaleriaBundle:Categorias:select.html.twig', array(
                    'tipos'  => Categorias::getArrayTipoCategorias(),
                ));
            }
        }        
    }
    

    /**
     * Finds and displays a Categorias entity.
     *
     * @Route("/{id}/show", name="categorias_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $categoria_actual = $em->getRepository('CategoriasGaleriaBundle:Categorias')
                               ->find($id);
        
        $categorias = $em->getRepository('CategoriasGaleriaBundle:Categorias')
                ->getCategoriasPorTipoCategoria($categoria_actual->getTipoCategoria(),$categoria_actual->getId());
        
        if (!$categoria_actual) {
            throw $this->createNotFoundException('Unable to find Categorias entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return array(
            'entity'      => $categoria_actual,
            'categorias'  => $categorias,
            'tipos'       => Categorias::getArrayTipoCategorias(),
            'delete_form' => $deleteForm->createView(),

        );
    }
    
    
    
    /**
     * Mostrar categorias segun el tipo.
     *
     * @Route("/show/{tipo}/tipo", name="categorias_show_tipo")
     * @Template("CategoriasGaleriaBundle:Categorias:show.html.twig")
     */
    public function showCategoriaAction($tipo)
    {
        
        $categoria_actual=$this->getDoctrine()
                ->getRepository('CategoriasGaleriaBundle:Categorias')
                ->getCategoriaActualPorTipoCategoria($tipo);
        $categorias = $this->getDoctrine()
                ->getRepository('CategoriasGaleriaBundle:Categorias')
                ->getCategoriasPorTipoCategoria($tipo,$categoria_actual->getId());
        
        if (!$categoria_actual) {
            throw $this->createNotFoundException('Unable to find Categorias entity.');
        }

        $deleteForm = $this->createDeleteForm($categoria_actual->getId());

        return array(
            'entity'      => $categoria_actual,
            'categorias'  => $categorias,
            'tipos'       => Categorias::getArrayTipoCategorias(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    public function oficialAction(){
        return $this->forward(
                'CategoriasGaleriaBundle:Categorias:showCategoria', 
                array('tipo'=>  Categorias::$GALERIA)
                );
    }
    
    public function recomendacionesAction(){
        return $this->forward(
                'CategoriasGaleriaBundle:Categorias:showCategoria', 
                array('tipo'=>  Categorias::$RECOMENDACIONES)
                );
    }
    

    /**
     * Displays a form to create a new Categorias entity.
     *
     * @Route("/new", name="categorias_new")
     * @Template()
     */
    public function newAction()
    {
        $request=$this->getRequest();
        $tipo=$request->query->get('tipo',0);
        $entity = new Categorias();
        $max=$this->getDoctrine()->getRepository('CategoriasGaleriaBundle:Categorias')->getMaxPosicion();
        
        if(!is_null($max)){
            $entity->setPosicion($max+1);
        }else{
            $entity->setPosicion(1);
        }
        
        if($tipo>0){
            $entity->setTipoCategoria($tipo);
        }
        
        $form   = $this->createForm(new CategoriasType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'galeria' => $entity->getStringTipoCategoria(),
        );
    }

    /**
     * Creates a new Categorias entity.
     *
     * @Route("/create", name="categorias_create")
     * @Method("POST")
     * @Template("CategoriasGaleriaBundle:Categorias:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Categorias();
        $form = $this->createForm(new CategoriasType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorias_por_tipo', array('tipo' => $entity->getTipoCategoria())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'galeria' => $entity->getStringTipoCategoria(),
        );
    }

    /**
     * Displays a form to edit an existing Categorias entity.
     *
     * @Route("/{id}/edit", name="categorias_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CategoriasGaleriaBundle:Categorias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categorias entity.');
        }

        $editForm = $this->createForm(new CategoriasType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'galeria' => $entity->getStringTipoCategoria(),
        );
    }

    /**
     * Edits an existing Categorias entity.
     *
     * @Route("/{id}/update", name="categorias_update")
     * @Method("POST")
     * @Template("CategoriasGaleriaBundle:Categorias:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CategoriasGaleriaBundle:Categorias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categorias entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoriasType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorias_por_tipo', array('tipo' => $entity->getTipoCategoria())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'galeria' => $entity->getStringTipoCategoria(),
        );
    }

    /**
     * Deletes a Categorias entity.
     *
     * @Route("/{id}/delete", name="categorias_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CategoriasGaleriaBundle:Categorias')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Categorias entity.');
            }
            
            foreach($entity->getGalerias() as $galeria){
                $em->remove($galeria);
            }
            
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categorias'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * Galeria de una categoria segun el status del registro.
     *
     * @Route("/galeria/{categoria}/{isActive}", name="categorias_galeria")
     * @Template()
     */
    public function galeriaAction($categoria,$isActive){
        $em = $this->getDoctrine()->getEntityManager();
        $entities=$em->getRepository('CategoriasGaleriaBundle:Galerias')
                ->getGaleriaPorCategoriaYStatus($categoria,$isActive);
        
        return array(
            'entities'=>$entities,
            'gallery_status'=>$isActive
        );
    }
    
    /**
     * Ordenar registros.
     *
     * @Route("/ordenar", name="categorias_ordenar")
     */
    public function ordenarGaleriaAction()
    {
        $request=$this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $categoria = $this->getDoctrine()->getRepository('CategoriasGaleriaBundle:Categorias')->find($request->request->get("categoria"));
            $registro_order = $request->query->get('registro');
            $em=$this->getDoctrine()->getEntityManager();
            $result['ok']="ok";
            foreach($registro_order as $order=>$id)
            {
                $registro=  $this->getDoctrine()->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);
                if($registro->getPosicion()!=($order+1)){
                    try{
                        $registro->setPosicion($order+1);
                        $em->flush();
                    }catch(Exception $e){
                        $result['ok']=$e->getMessage();
                    }    
                }
            }
            
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else {
            return null;
        }
    }
    
    /**
     * Subir registro de Categorias.
     *
     * @Route("/{id}/up", name="categorias_up")
     * @Method("GET")
     */
    public function upAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroUp = $em->getRepository('CategoriasGaleriaBundle:Categorias')->find($id);
        
        if ($registroUp) {
            $registroDown=$em->getRepository('CategoriasGaleriaBundle:Categorias')->getRegistroUpOrDown($registroUp->getPosicion(),true);
            if ($registroDown) {
                $posicion=$registroUp->getPosicion();
                $registroUp->setPosicion($registroDown->getPosicion());
                $registroDown->setPosicion($posicion);
                $em->flush();
            }
        }
        
        return $this->redirect($this->generateUrl('categorias',array(
            'page'=>$this->getRequest()->query->get('page', 1)
        )));
    }
    
    /**
     * Subir registro de Categorias.
     *
     * @Route("/{id}/down", name="categorias_down")
     * @Method("GET")
     */
    public function downAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroDown = $em->getRepository('CategoriasGaleriaBundle:Categorias')->find($id);
        
        if ($registroDown) {
            $registroUp=$em->getRepository('CategoriasGaleriaBundle:Categorias')->getRegistroUpOrDown($registroDown->getPosicion(),false);
            if ($registroUp) {
                $posicion=$registroUp->getPosicion();
                $registroUp->setPosicion($registroDown->getPosicion());
                $registroDown->setPosicion($posicion);
                $em->flush();
            }
        }
        
        return $this->redirect($this->generateUrl('categorias',array(
            'page'=>$this->getRequest()->query->get('page', 1)
        )));
    }
}

