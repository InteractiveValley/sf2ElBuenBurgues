<?php

namespace Richpolis\CategoriasGaleriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\CategoriasGaleriaBundle\Entity\Galerias;
use Richpolis\CategoriasGaleriaBundle\Entity\Categorias;
use Richpolis\CategoriasGaleriaBundle\Form\GaleriasType;

use Richpolis\BackendBundle\Utils\qqFileUploader;

/**
 * Galerias controller.
 *
 * @Route("/galerias")
 */
class GaleriasController extends Controller
{
    protected function getFilters()
    {
        $filters=$this->get('session')->get('filters', array());
      
        return $filters;
    }
    
    protected function getCategoriaDefault(){
        $filters = $this->getFilters();
        if(isset($filters['categorias'])){
            return $filters['categorias'];
        }else{
            return Categorias::$GALERIA;
        }
        
    }
    
    /**
     * Lists all Galerias entities.
     *
     * @Route("/", name="galerias")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CategoriasGaleriaBundle:Galerias')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Galerias entity.
     *
     * @Route("/{id}/show", name="galerias_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galerias entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'link_video' => Galerias::$LINK_VIDEO,
        );
    }

    /**
     * Displays a form to create a new Galerias entity.
     *
     * @Route("/new", name="galerias_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Galerias();
        
        $categoriaId=$this->getRequest()->query->get('categoria',$this->getCategoriaDefault());
        
        $tipoArchivo=$this->getRequest()->query->get('tipoArchivo',  Galerias::$IMAGEN);

        $categoria=$this->getDoctrine()->getRepository('CategoriasGaleriaBundle:Categorias')
                                        ->find($categoriaId);

        $max=$this->getDoctrine()->getRepository('CategoriasGaleriaBundle:Galerias')->getMaxPosicion();
        
        if(!is_null($max)){
            $entity->setPosicion($max+1);
        }else{
            $entity->setPosicion(1);
        }
        
        $entity->setCategoria($categoria);
        
        $entity->setTipoArchivo($tipoArchivo);
        
        if($tipoArchivo==Galerias::$IMAGEN){
            $form = $this->createForm(new GaleriasType(), $entity);
        }else{
            $entity->setIsActive(true);
            $form = $this->createForm(new \Richpolis\CategoriasGaleriaBundle\Form\GaleriasLinkVideoType(),$entity);
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'categoria' => $categoria
        );
    }

    /**
     * Creates a new Galerias entity.
     *
     * @Route("/create", name="galerias_create")
     * @Method("POST")
     * @Template("CategoriasGaleriaBundle:Galerias:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Galerias();
        if($request->get('tipoArchivo')==Galerias::$IMAGEN){
            $form = $this->createForm(new GaleriasType(), $entity);
        }else{
            $form = $this->createForm(new \Richpolis\CategoriasGaleriaBundle\Form\GaleriasLinkVideoType(),$entity);
        }
        
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorias_show', array('id' => $entity->getCategoria()->getId())));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Galerias entity.
     *
     * @Route("/{id}/edit", name="galerias_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galerias entity.');
        }
        $categoria=$entity->getCategoria();
        $editForm = $this->createForm(new GaleriasType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'categoria'   => $categoria,
        );
    }

    /**
     * Edits an existing Galerias entity.
     *
     * @Route("/{id}/update", name="galerias_update")
     * @Method("POST")
     * @Template("CategoriasGaleriaBundle:Galerias:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Galerias entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new GaleriasType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorias_show', array('id' => $entity->getCategoria()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Galerias entity.
     *
     * @Route("/{id}/delete", name="galerias_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        $categoria=null;
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Galerias entity.');
            }
            $categoria=$entity->getCategoria();
            
            $em->remove($entity);
            $em->flush();
        }

        if(!$categoria)
            return $this->redirect($this->generateUrl('categorias_show',array('id'=>$categoria->getId())));
        else
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
     * upload registro a galeria.
     *
     * @Route("/upload/registro/galeria/{categoria_id}", name="galerias_upload")
     */
    public function uploadAction($categoria_id){
        
       // list of valid extensions, ex. array("jpeg", "xml", "bmp")
       $allowedExtensions = array("jpeg","png","gif","jpg");
       // max file size in bytes
       $sizeLimit = 6 * 1024 * 1024;
       $request=$this->get("request");
       $uploader = new qqFileUploader($allowedExtensions, $sizeLimit,$request->server);
       $uploads= $this->container->getParameter('richpolis_uploads');
       $result = $uploader->handleUpload($uploads."/galerias/");
       
       // to pass data through iframe you will need to encode all html tags
       /*****************************************************************/
       //$file = $request->getParameter("qqfile");
       $em = $this->getDoctrine()->getEntityManager();
       $max = $em->getRepository('CategoriasGaleriaBundle:Galerias')->getMaxPosicion();
       $categoria=$em->getRepository('CategoriasGaleriaBundle:Categorias')->find($categoria_id);
       if($max == null){
           $max=0;
       }
       if(isset($result["success"])){
           $registro = new Galerias();
           $registro->setArchivo($result["filename"]);
           $registro->setThumbnail($result["filename"]);
           $registro->setTitulo($result["titulo"]);
           $registro->setIsActive(true);
           $registro->setPosicion($max+1);
           $registro->setCategoria($categoria);
           $registro->setTipoArchivo(Galerias::$IMAGEN);
           
           //unset($result["filename"],$result['original'],$result['titulo'],$result['contenido']);
           $em->persist($registro);
           $registro->crearThumbnail();
           $em->flush();
        }
                    
        // create a JSON-response with a 200 status code
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    
    /**
     * upload registro a galeria.
     *
     * @Route("/mostrar/registros/{categoria}/{isActive}", name="galerias_galeria")
     * @Template("CategoriasGaleriaBundle:Categorias:galeria.html.twig")
     */
    public function galeriaAction($categoria,$isActive){
        $em = $this->getDoctrine()->getEntityManager();
        $entities=$em->getRepository('CategoriasGaleriaBundle:Galerias')->getGaleriaPorCategoriaYStatus($categoria,$isActive);
        
        return array(
            'entities'          =>  $entities,
            'gallery_status'    =>  $isActive
        );
    }
    
    /**
     * actualizar datos del registro.
     *
     * @Route("/actualizar/registro/galeria", name="galerias_update_registro")
     */
    public function updateRegistroGaleriaAction(){
        $request=$this->getRequest();
        if ($request->getMethod() == 'POST') {
            $id=$request->request->get('id');
            $titulo=$request->request->get('titulo');
        }elseif($request->getMethod() == 'GET'){
            $id=$request->query->get('id');
            $titulo=$request->query->get('titulo');
        }
        $em = $this->getDoctrine()->getEntityManager();
        $registro = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);
        $registro->setTitulo($titulo);
        $em->flush();
        
        $template=$this->renderView('CategoriasGaleriaBundle:Categorias:item.html.twig', array(
            'entity'=>$registro,
        ));
        if($request->isXmlHttpRequest()){
            $response = new Response($template);
            return $response;
        }else{
            
            return $this->redirect($this->generateUrl('galerias'));
        }
    }
    
    /**
     * actualizar datos del registro.
     *
     * @Route("/eliminar/registro/galeria/{id}", name="galerias_delete_registro")
     */
    public function deleteRegistroGaleriaAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);
        $request = $this->getRequest();
        $result=array();

        if(!$entity || $request->getMethod() != 'POST'){
            $result['ok']="false";
        }else{
            $em->remove($entity);
            $em->flush();
            $result['ok']="ok";
            $result['id']=$id;
        }
        if($request->isXmlHttpRequest()){
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            
            return $this->redirect($this->generateUrl('galerias'));
        }
    }
    
    /**
     * actualizar datos del registro.
     *
     * @Route("/activar/registro/galeria/{id}", name="galerias_activar")
     */
    public function activarAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);
        $request = $this->getRequest();
        $result=array();

        if(!$entity){
            $result['ok']="false";
        }else{
            $entity->setIsActive(true);
            $em->flush();
            $result['ok']="ok";
            $result['id']=$id;
        }
        $template=$this->renderView('CategoriasGaleriaBundle:Categorias:item.html.twig', array(
            'entity'=>$entity,
        ));

        if($request->isXmlHttpRequest()){
            $result['html']=$template;
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            
            return $this->redirect($this->generateUrl('galerias'));
        }
    }
    
    /**
     * Cambiar de tipo de categoria.
     *
     * @Route("/cambiar/tipo/categoria", name="galerias_cambiar_tipo_categoria")
     */
    public function cambiarTipoCategoriaAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $id=$request->query->get('id');
        $tipo=$request->query->get('tipo');
        $id=  str_replace('registro-', "", $id);
        $entity=    $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);
        $categoria= $em->getRepository('CategoriasGaleriaBundle:Categorias')->getCategoriaActualPorTipoCategoria($tipo);
        $posicion=  $em->getRepository('CategoriasGaleriaBundle:Galerias')->getMaxPosicion();
        $result=array();

        if(!$entity){
            $result['ok']="false";
        }elseif(!$categoria){
            $result['ok']="false";
        }else{
            $entity->setCategoria($categoria);
            $entity->setPosicion($posicion);
            $em->flush();
            $entity->actualizaThumbnail();
            $result['ok']="ok";
            $result['id']=$id;
        }
        
        if($request->isXmlHttpRequest()){
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            
            return $this->redirect($this->generateUrl('galerias'));
        }
    }
    
    /**
     * Cambiar a archivo de categoria.
     *
     * @Route("/cambiar/archivo/categoria", name="galerias_cambiar_archivo_categoria")
     */
    public function cambiarArchivoCategoriaAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $id=$request->query->get('id');
        $IdCategoria=$request->query->get('categoria');
        $id=  str_replace('registro-', "", $id);
        $entity=    $em->getRepository('CategoriasGaleriaBundle:Galerias')->find($id);
        $categoria= $em->getRepository('CategoriasGaleriaBundle:Categorias')->find($IdCategoria);
        $posicion=  $em->getRepository('CategoriasGaleriaBundle:Galerias')->getMaxPosicion();
        $result=array();

        if(!$entity){
            $result['ok']="false";
        }elseif(!$categoria){
            $result['ok']="false";
        }else{
            $entity->setCategoria($categoria);
            $entity->setPosicion($posicion);
            $em->flush();
            $entity->actualizaThumbnail();
            $result['ok']="ok";
            $result['id']=$id;
        }
        
        if($request->isXmlHttpRequest()){
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }else{
            
            return $this->redirect($this->generateUrl('galerias'));
        }
    }
}
