<?php

namespace Richpolis\ElbuenBurguesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\BackendBundle\Entity\Contacto;
use Richpolis\BackendBundle\Form\ContactoType;

/**
 * Ubicaciones controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller {
    
    private $categorias = null;
    protected function getFilters()
    {
        $filters=$this->get('session')->get('filters', array());
        return $filters;
    }
    
    protected function setFilters($filters){
        $this->get('session')->set('filters',$filters);
    }

    protected function getCategoriaDefault(){
        $filters = $this->getFilters();
        $categoria = null;
        if(isset($filters['publicaciones'])){
            $this->getCategoriasPublicacion();
            foreach($this->categorias as $cat){
                if($cat->getId()==$filters['publicaciones']){
                    $categoria= $cat;
                    break;
                }
            }
            return $categoria;
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
     * Lists all Ubicaciones entities.
     *
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $ubicacion = $em->getRepository('ElbuenBurguesBundle:Ubicaciones')->getUltimaUbicacion();
        $configuraciones = $em->getRepository('BackendBundle:Configuraciones')->findAll();
        
        $categoriasProductos = $em->getRepository('PublicacionesBundle:CategoriasPublicacion')
                                  ->getCategoriasPublicacionActivas();
        if($this->categorias == null)
            $this->categorias = $categoriasProductos;
            
        
        $tipo_galeria = \Richpolis\CategoriasGaleriaBundle\Entity\Categorias::$GALERIA;
        $tipo_recomendaciones = \Richpolis\CategoriasGaleriaBundle\Entity\Categorias::$RECOMENDACIONES;
        
        $contacto = new \Richpolis\BackendBundle\Entity\Contacto();
        $form = $this->createForm(new \Richpolis\BackendBundle\Form\ContactoType(), $contacto);
        
        return array(
            'ubicacion' =>  $ubicacion,
            'configuraciones' => $configuraciones,
            'tipo_galeria' => $tipo_galeria,
            'tipo_recomendaciones' =>  $tipo_recomendaciones,
            'categoriasProductos'=>$categoriasProductos,
            'categoriaActual'=> $this->getCategoriaDefault(),
            'form' => $form->createView(),
        );
    }
    
    /**
     * Lista los ultimos tweets.
     *
     * @Route("/last-tweets/{username}/", name="last_tweets")
     */
    public function lastTweetsAction($username, $limit = 10, $age = null)
    {
        /* @var $twitter FetcherInterface */
        $twitter = $this->get('knp_last_tweets.last_tweets_fetcher');

        try {
            $tweets = $twitter->fetch($username, $limit);
        } catch (TwitterException $e) {
            $tweets = array();
        }

        $response = $this->render('ElbuenBurguesBundle:Default:lastTweets.html.twig', array(
            'username' => $username,
            'tweets'   => $tweets,
        ));

        if ($age) {
            $response->setSharedMaxAge($age);
        }

        return $response;
    }
    
    /**
     * Finds and displays a Ubicaciones entity.
     *
     * @Route("/galeria/item/show/{slug}/", name="galeria_item_show")
     */
    public function galeriaItemShowAction($slug)
    {
        //$request=$this->getRequest();
        //if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('CategoriasGaleriaBundle:Categorias')->findOneBySlug($slug);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Categorias entity.');
            }


            return $this->render('ElbuenBurguesBundle:Default:itemGaleria.html.twig', array(
                'entity'   => $entity,
                'galerias' =>$entity->getGalerias(),
                'link_video'=>  \Richpolis\CategoriasGaleriaBundle\Entity\Galerias::$LINK_VIDEO,
            ));
        //}else{
        //    return new Response();
        //}
    }
    
    /**
     * @Route("/contacto", name="frontend_contacto")
     * @Method({"GET", "POST"})
     * @Template("ElbuenBurguesBundle:Default:contacto.html.twig")
     */
    public function contactoAction() {
        $contacto = new Contacto();
        $form = $this->createForm(new ContactoType(), $contacto);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $datos=$form->getData();
                
                $message = \Swift_Message::newInstance()
                        ->setSubject('Contacto desde pagina')
                        ->setFrom($datos->getEmail())
                        ->setTo($this->container->getParameter('richpolis.emails.to_email'))
                        ->setBody($this->renderView('BackendBundle:Default:contactoEmail.html.twig', array('datos' => $datos)), 'text/html');
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('noticia', 'Gracias por enviar su correo!');

                // Redirige - Esto es importante para prevenir que el usuario
                // reenvíe el formulario si actualiza la página
                $ok=true;
                $error=false;
                $mensaje="Se ha enviado el mensaje";
                $contacto = new Contacto();
                $form = $this->createForm(new ContactoType(), $contacto);
            }else{
                $ok=false;
                $error=true;
                $mensaje="El mensaje no se ha podido enviar";
            }
        }else{
            $ok=false;
            $error=false;
            $mensaje="Violacion de acceso";
        }
        
         $em = $this->getDoctrine()->getEntityManager();
         //$configuraciones = $em->getRepository('BackendBundle:Configuraciones')->findAll(); 

        return array(
              /*'configuraciones'=>$configuraciones,*/
              'form' => $form->createView(),
              'ok'=>$ok,
              'error'=>$error,
              'mensaje'=>$mensaje,
        );
    }
    
    /**
     * Lista los platillos.
     *
     * @Route("/platillos/{categoriaId}", name="frontend_platillos")
     */
    public function platillosAction(Request $request,$categoriaId)
    {
        $em = $this->getDoctrine()->getManager();
        
        $filters = $this->getFilters();
        
        if(!isset($filters['publicaciones'])){
            $filters['publicaciones']=$this->getCategoriaDefault();
            $this->setFilters($filters);
        }
            
        if($filters['publicaciones']!=$categoriaId){
            $filters['publicaciones']=$categoriaId;
            $this->setFilters($filters);
        }
                
        $query = $em->getRepository("PublicacionesBundle:Publicacion")
                    ->getQueryPublicacionPorCategoriaActivas($filters['publicaciones']);

        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            7
        );
        
        $datosPagina=$pagination->getPaginationData();
        
        $paginaNext=(isset($datosPagina["next"]))?$datosPagina["next"]:0;
        
        $paginaPrevious=(isset($datosPagina["previous"]))?$datosPagina["previous"]:0;
            
        $response = $this->render('ElbuenBurguesBundle:Default:platillos.html.twig', array(
            'pagination' => $pagination,
            'siguiente' => $paginaNext,
            'anterior' => $paginaPrevious,
            
        ));

        return $response;
    }
    
    /**
     * Lista las categorias de galerias.
     *
     * @Route("/categorias/galeria/{tipo}/", name="frontend_categorias_galerias")
     */
    public function categoriasGaleriaAction($tipo)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $query = $em->getRepository("CategoriasGaleriaBundle:Categorias")->getQueryCategoriasGaleriaActivas($tipo);

        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            6
        );
        
        $datosPagina=$pagination->getPaginationData();
        
        $paginaNext=(isset($datosPagina["next"]))?$datosPagina["next"]:0;
        
        $paginaPrevious=(isset($datosPagina["previous"]))?$datosPagina["previous"]:0;

        $tipo_galeria = \Richpolis\CategoriasGaleriaBundle\Entity\Categorias::$GALERIA;    

        $response = $this->render('ElbuenBurguesBundle:Default:categoriasGaleria.html.twig', array(
            'pagination' => $pagination,
            'siguiente' => $paginaNext,
            'anterior' => $paginaPrevious,
            'tipo_galeria'=> $tipo_galeria
        
        ));

        return $response;
    }
    
    /**
     * Lista las recomendaciones.
     *
     * @Route("/recomendaciones/{tipo}/", name="frontend_recomendaciones")
     */
    public function recomendacionesAction($tipo)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $query = $em->getRepository("CategoriasGaleriaBundle:Galerias")->getQueryGaleriaPorTipoCategoria($tipo);

        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            3
        );
        
        $datosPagina=$pagination->getPaginationData();
        
        $paginaNext=(isset($datosPagina["next"]))?$datosPagina["next"]:0;
        
        $paginaPrevious=(isset($datosPagina["previous"]))?$datosPagina["previous"]:0;
        
        $tipo_recomendaciones = \Richpolis\CategoriasGaleriaBundle\Entity\Categorias::$RECOMENDACIONES;
        
        $response = $this->render('ElbuenBurguesBundle:Default:recomendaciones.html.twig', array(
            'pagination' => $pagination,
            'siguiente' => $paginaNext,
            'anterior' => $paginaPrevious,
            'tipo_recomendaciones' => $tipo_recomendaciones,
        ));

        return $response;
    }
}

?>
