<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\Publicacion;
use Richpolis\PublicacionesBundle\Form\PublicacionType; 


/**
 * Publicacion controller.
 *
 * @Route("/backend/productos")
 */
class PublicacionController extends Controller
{
    private $categorias = null;
    protected function getFilters()
    {
        $filters=$this->get('session')->get('filters', array());
        return $filters;
    }

    protected function getCategoriaDefault(){
        $filters = $this->getFilters();
        if(isset($filters['publicaciones'])){
            return $filters['publicaciones'];
        }else{
            $this->getCategoriasPublicacion();
            return $this->categorias[0];
        }
    }

    protected function getCategoriasPublicacion(){
        $em = $this->getDoctrine()->getManager();
        if($this->categorias == null){
            $this->categorias = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                                   ->getCategoriasPublicacionActivas();
        }
        return $this->categorias;
    }

    protected function getCategoriaActual($categoriaId){
        $categorias= $this->getCategoriasPublicacion();
        $categoriaActual=null;
        foreach($categorias as $categoria){
            if($categoria->getId()==$categoriaId){
                $categoriaActual=$categoria;
                break;
            }
        }
        return $categoriaActual;
    }
    
    /**
     * Lists all Publicacion entities.
     * @Route("/", name="publicacion")
     * 
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $filters = $this->getFilters();

        if(!isset($filters['publicaciones']))
            return $this->redirect($this->generateUrl('publicacion_seleccionar_categoria'));
        
        $query = $em->getRepository("PublicacionesBundle:Publicacion")
                    ->getQueryPublicacionPorCategoriaActivas($filters['publicaciones']);

        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            10
        );

        return array(
            'categorias'      =>$this->getCategoriasPublicacion(),
            'categoria_actual'=>$this->getCategoriaActual($filters['publicaciones']),
            'pagination'      =>$pagination,
        );
    }
    
    /**
     * Seleccionar un tipo de categoria.
     *
     * @Route("/seleccionar/categoria", name="publicacion_seleccionar_categoria")
     */
    public function selectAction()
    {
        $filters = $this->getFilters();
        
        if(isset($filters['publicaciones'])){
            return $this->redirect($this->generateUrl('publicacion'));
        }else{
            return $this->render('PublicacionesBundle:Publicacion:select.html.twig', array(
                'categorias'  => $this->getCategoriasPublicacion(),
            ));
        }
    }
    
    /**
     * Mostrar por categoria
     *
     * @Route("/list/{categoria}/categoria", name="publicacion_por_categoria")
     */
    public function porCategoriaAction($categoria)
    {
        $filters = $this->getFilters();
        if($categoria){
            $filters['publicaciones']=$categoria;
            $this->get('session')->set('filters',$filters);
            return $this->redirect($this->generateUrl('publicacion'));
        }else{
            if(isset($filters['publicaciones'])){
                return $this->redirect($this->generateUrl('publicacion'));
            }else{
                return $this->render('PublicacionesBundle:Publicacion:select.html.twig', array(
                    'categorias'  => $this->getCategoriasPublicacion(),
                ));
            }
        }        
    }
    
    /**
     * Finds and displays a Publicacion entity.
     *
     * @Route("/{id}/show", name="publicacion_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    public function publicacionesMiniBurguesAction(){
        $em = $this->getDoctrine()->getManager();
        $filters = $this->getFilters();
        $categoria = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                        ->findOneBySlug('mini-burgues');
        if($categoria==null){
            $categoria=$this->getCategoriaDefault();
        }                
        $filters['publicaciones']=$categoria->getId();
        $this->get('session')->set('filters',$filters);
        return $this->redirect($this->generateUrl('publicacion'));
    }
    
    
    public function publicacionesShakesAction(){
        $em = $this->getDoctrine()->getManager();
        $filters = $this->getFilters();
        $categoria = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                        ->findOneBySlug('skakes');
        if($categoria==null){
            $categoria=$this->getCategoriaDefault();
        }                
        $filters['publicaciones']=$categoria->getId();
        $this->get('session')->set('filters',$filters);
        return $this->redirect($this->generateUrl('publicacion'));
    }
    
    public function publicacionesFriesAction(){
        $em = $this->getDoctrine()->getManager();
        $filters = $this->getFilters();
        $categoria = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                        ->findOneBySlug('fries');
        if($categoria==null){
            $categoria=$this->getCategoriaDefault();
        }                
        $filters['publicaciones']=$categoria->getId();
        $this->get('session')->set('filters',$filters);
        return $this->redirect($this->generateUrl('publicacion'));
    }
    
    
    /**
     * Displays a form to create a new Publicacion entity.
     *
     * @Route("/new", name="publicacion_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Publicacion();
        
        $max=$this->getDoctrine()->getRepository('PublicacionesBundle:Publicacion')
                  ->getMaxPosicion();

        if(!is_null($max)){
            $entity->setPosicion($max+1);
        }else{
            $entity->setPosicion(1);
        }
        
        $categoriaId=$this->getRequest()->query->get('categoria',$this->getCategoriaDefault());
        
        $categoria=$this->getDoctrine()->getRepository('PublicacionesBundle:CategoriasPublicacion')
                                        ->find($categoriaId);

        $entity->setCategoria($categoria);
       
        $form   = $this->createForm(new PublicacionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Publicacion entity.
     *
     * @Route("/create", name="publicacion_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:Publicacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Publicacion();
        $categoriaId=$this->getCategoriaDefault();
        $categoria=$this->getDoctrine()->getRepository('PublicacionesBundle:CategoriasPublicacion')
                                        ->find($categoriaId);
        $entity->setCategoria($categoria);
        
        $form = $this->createForm(new PublicacionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('publicacion_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Publicacion entity.
     *
     * @Route("/{id}/edit", name="publicacion_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicacion entity.');
        }

        $editForm = $this->createForm(new PublicacionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Publicacion entity.
     *
     * @Route("/{id}/update", name="publicacion_update")
     * @Method("POST")
     * @Template("PublicacionesBundle:Publicacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Publicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PublicacionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('publicacion_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Publicacion entity.
     *
     * @Route("/{id}/delete", name="publicacion_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Publicacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('publicacion'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * Subir registro de Publicacion.
     *
     * @Route("/{id}/up", name="publicacion_up")
     * @Method("GET")
     */
    public function upAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroUp = $em->getRepository('PublicacionesBundle:Publicacion')
                ->find($id);
        
        if ($registroUp) {
            $registroDown=$em->getRepository('PublicacionesBundle:Publicacion')
                    ->getRegistroUpOrDown($registroUp,true);
            if ($registroDown) {
                $posicion=$registroUp->getPosicion();
                $registroUp->setPosicion($registroDown->getPosicion());
                $registroDown->setPosicion($posicion);
                $em->flush();
            }
        }
        
        return $this->redirect($this->generateUrl('publicacion',array(
            'page'=>$this->getRequest()->query->get('page', 1)
        )));
    }
    
    /**
     * Subir registro de Publicacion.
     *
     * @Route("/{id}/down", name="publicacion_down")
     * @Method("GET")
     */
    public function downAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroDown = $em->getRepository('PublicacionesBundle:Publicacion')->find($id);
        
        if ($registroDown) {
            $registroUp=$em->getRepository('PublicacionesBundle:Publicacion')
                           ->getRegistroUpOrDown($registroDown,false);
            if ($registroUp) {
                $posicion=$registroUp->getPosicion();
                $registroUp->setPosicion($registroDown->getPosicion());
                $registroDown->setPosicion($posicion);
                $em->flush();
            }
        }
        
        return $this->redirect($this->generateUrl('publicacion',array(
            'page'=>$this->getRequest()->query->get('page', 1)
        )));
    }
}
