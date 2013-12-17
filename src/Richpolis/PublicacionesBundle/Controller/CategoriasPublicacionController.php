<?php

namespace Richpolis\PublicacionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\PublicacionesBundle\Entity\CategoriasPublicacion;
use Richpolis\PublicacionesBundle\Form\CategoriasPublicacionType;


/**
 * Categorias controller.
 *
 * @Route("/backend/publicaciones")
 */
class CategoriasPublicacionController extends Controller
{
    protected function getFilters()
    {
        $filters=$this->get('session')->get('filters', array());
        return $filters;
    }
    
    /**
     * Lists all Categorias entities.
     *
     * @Route("/", name="publicaciones")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $filters = $this->getFilters();

        if(!isset($filters['categorias_publicaciones']))
            $filters['categorias_publicaciones']=  CategoriasPublicacion::$ABOUT;
        
        $query = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                            ->getQueryCategoriasPorTipoYActivas($filters['publicaciones'],true);
        
        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            10
        );


        
        return array(
            'tipos'         =>  CategoriasPublicacion::getArrayTipoCategorias(),
            'tipo_categoria'=>  $filters['categorias_publicaciones'],
            'pagination' => $pagination,
        );
    }
    
    /**
     * Seleccionar un tipo de categoria.
     *
     * @Route("/seleccionar", name="publicaciones_seleccionar")
     */
    public function selectAction()
    {
        $filters = $this->getFilters();
        
        if(isset($filters['categorias_publicaciones'])){
            return $this->redirect($this->generateUrl('publicaciones'));
        }else{
            return $this->render('PublicacionesBundle:CategoriasPublicacion:select.html.twig', array(
                'tipos'  => CategoriasPublicacion::getArrayTipoCategorias(),
            ));
        }
    }
    
    /**
     * Mostrar categoria por tipo
     *
     * @Route("/list/{tipo}/tipo", name="publicaciones_por_tipo")
     */
    public function porTipoAction($tipo)
    {
        $filters = $this->getFilters();
        if($tipo){
            $filters['categorias_publicaciones']=$tipo;
            $this->get('session')->set('filters',$filters);
            return $this->redirect($this->generateUrl('publicaciones'));
        }else{
            if(isset($filters['categorias_publicaciones'])){
                return $this->redirect($this->generateUrl('publicaciones'));
            }else{
                return $this->render('PublicacionesBundle:CategoriasPublicacion:select.html.twig', array(
                    'tipos'  => CategoriasPublicacion::getArrayTipoCategorias(),
                ));
            }
        }        
    }
    

    /**
     * Finds and displays a CategoriasPublicacion entity.
     *
     * @Route("/{id}/show", name="publicaciones_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $categoria_actual = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                               ->find($id);
        
        $publicaciones = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                ->getCategoriasPorTipoCategoria($categoria_actual->getTipoCategoria(),$categoria_actual->getId());
        
        if (!$categoria_actual) {
            throw $this->createNotFoundException('Unable to find CategoriasPublicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return array(
            'entity'      => $categoria_actual,
            'publicaciones'  => $publicaciones,
            'tipos'       => CategoriasPublicacion::getArrayTipoCategorias(),
            'delete_form' => $deleteForm->createView(),

        );
    }
    
    
    
    /**
     * Mostrar publicaciones segun el tipo.
     *
     * @Route("/show/{tipo}/tipo", name="publicaciones_show_tipo")
     * @Template("PublicacionesBundle:CategoriasPublicacion:show.html.twig")
     */
    public function showCategoriaAction($tipo)
    {
        
        $categoria_actual=$this->getDoctrine()
                ->getRepository('PublicacionesBundle:CategoriasPublicacion')
                ->getCategoriaActualPorTipoCategoria($tipo);
        $publicaciones = $this->getDoctrine()
                ->getRepository('PublicacionesBundle:CategoriasPublicacion')
                ->getCategoriasPorTipoCategoria($tipo,$categoria_actual->getId());
        
        if (!$categoria_actual) {
            throw $this->createNotFoundException('Unable to find CategoriasPublicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($categoria_actual->getId());

        return array(
            'entity'      => $categoria_actual,
            'publicaciones'  => $publicaciones,
            'tipos'       => CategoriasPublicacion::getArrayTipoCategorias(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    public function aboutAction(){
        return $this->forward(
                'PublicacionesBundle:CategoriasPublicacion:showCategoria', 
                array('tipo'=>  CategoriasPublicacion::$ABOUT)
                );
    }
    
    public function distribuidoresAction(){
        return $this->forward(
                'PublicacionesBundle:CategoriasPublicacion:showCategoria', 
                array('tipo'=>  CategoriasPublicacion::$DISTRIBUIDORES)
                );
    }
    

    /**
     * Displays a form to create a new CategoriasPublicacion entity.
     *
     * @Route("/new", name="publicaciones_new")
     * @Template()
     */
    public function newAction()
    {
        $request=$this->getRequest();
        $tipo=$request->query->get('tipo',0);
        $entity = new Categorias();
        $max=$this->getDoctrine()->getRepository('PublicacionesBundle:CategoriasPublicacion')->getMaxPosicion();
        
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
     * Creates a new CategoriasPublicacion entity.
     *
     * @Route("/create", name="publicaciones_create")
     * @Method("POST")
     * @Template("PublicacionesBundle:CategoriasPublicacion:new.html.twig")
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

            return $this->redirect($this->generateUrl('publicaciones_por_tipo', array('tipo' => $entity->getTipoCategoria())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'galeria' => $entity->getStringTipoCategoria(),
        );
    }

    /**
     * Displays a form to edit an existing CategoriasPublicacion entity.
     *
     * @Route("/{id}/edit", name="publicaciones_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriasPublicacion entity.');
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
     * Edits an existing CategoriasPublicacion entity.
     *
     * @Route("/{id}/update", name="publicaciones_update")
     * @Method("POST")
     * @Template("PublicacionesBundle:CategoriasPublicacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoriasPublicacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoriasType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('publicaciones_por_tipo', array('tipo' => $entity->getTipoCategoria())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'galeria' => $entity->getStringTipoCategoria(),
        );
    }

    /**
     * Deletes a CategoriasPublicacion entity.
     *
     * @Route("/{id}/delete", name="publicaciones_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CategoriasPublicacion entity.');
            }
            
            foreach($entity->getGalerias() as $galeria){
                $em->remove($galeria);
            }
            
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('publicaciones'));
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
     * @Route("/galeria/{categoria}/{isActive}", name="publicaciones_galeria")
     * @Template()
     */
    public function galeriaAction($categoria,$isActive){
        $em = $this->getDoctrine()->getManager();
        $entities=$em->getRepository('PublicacionesBundle:Publicacion')
                ->getGaleriaPorCategoriaYStatus($categoria,$isActive);
        
        return array(
            'entities'=>$entities,
            'gallery_status'=>$isActive
        );
    }
    
    /**
     * Ordenar registros.
     *
     * @Route("/ordenar", name="publicaciones_ordenar")
     */
    public function ordenarGaleriaAction()
    {
        $request=$this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $categoria = $this->getDoctrine()->getRepository('PublicacionesBundle:CategoriasPublicacion')->find($request->request->get("categoria"));
            $registro_order = $request->query->get('registro');
            $em=$this->getDoctrine()->getManager();
            $result['ok']="ok";
            foreach($registro_order as $order=>$id)
            {
                $registro=  $this->getDoctrine()->getRepository('PublicacionesBundle:Publicacion')->find($id);
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
     * @Route("/{id}/up", name="publicaciones_up")
     * @Method("GET")
     */
    public function upAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroUp = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')->find($id);
        
        if ($registroUp) {
            $registroDown=$em->getRepository('PublicacionesBundle:CategoriasPublicacion')->getRegistroUpOrDown($registroUp->getPosicion(),true);
            if ($registroDown) {
                $posicion=$registroUp->getPosicion();
                $registroUp->setPosicion($registroDown->getPosicion());
                $registroDown->setPosicion($posicion);
                $em->flush();
            }
        }
        
        return $this->redirect($this->generateUrl('publicaciones',array(
            'page'=>$this->getRequest()->query->get('page', 1)
        )));
    }
    
    /**
     * Subir registro de Categorias.
     *
     * @Route("/{id}/down", name="publicaciones_down")
     * @Method("GET")
     */
    public function downAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $registroDown = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')->find($id);
        
        if ($registroDown) {
            $registroUp=$em->getRepository('PublicacionesBundle:CategoriasPublicacion')->getRegistroUpOrDown($registroDown->getPosicion(),false);
            if ($registroUp) {
                $posicion=$registroUp->getPosicion();
                $registroUp->setPosicion($registroDown->getPosicion());
                $registroDown->setPosicion($posicion);
                $em->flush();
            }
        }
        
        return $this->redirect($this->generateUrl('publicaciones',array(
            'page'=>$this->getRequest()->query->get('page', 1)
        )));
    }
}

