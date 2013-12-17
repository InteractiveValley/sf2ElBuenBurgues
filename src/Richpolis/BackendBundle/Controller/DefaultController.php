<?php

namespace Richpolis\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="backend_homepage")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    
    /**
     * @Route("/navegar", name="backend_navegar")
     * @Template()
     */
    public function navegarAction()
    {
        return array();
    }
    
    /**
     * @Route("/seleccionar/galeria", name="backend_categorias")
     */
    public function categoriasAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:select');
    }
    
    /**
     * @Route("/galerias", name="backend_galerias_oficial")
     */
    public function galeriaOficialAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:oficial');
    }
    
    /**
     * @Route("/galeria/recomendaciones", name="backend_galerias_recomendaciones")
     */
    public function galeriaRecomendacionesAction()
    {
        return $this->forward('CategoriasGaleriaBundle:Categorias:recomendaciones');
    }
    
    /**
     * @Route("/ubicaciones", name="backend_ubicaciones")
     */
    public function ubicacionesAction()
    {
        return $this->forward('ElbuenBurguesBundle:Ubicaciones:index');
    }
    
    /**
     * @Route("/ubicacion/actual", name="backend_ubicacion_actual")
     */
    public function ubicacionActualAction()
    {
        return $this->forward('ElbuenBurguesBundle:Ubicaciones:actual');
    }
    
    
    /**
     * @Route("/configuraciones", name="backend_configuraciones")
     */
    public function configuracionesAction()
    {
        return $this->forward('BackendBundle:Configuraciones:index');
    }
    
    /**
     * @Route("/productos/mini/burgues", name="backend_publicacion_mini_burgues")
     */
    public function publicacionMiniBurguesAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:publicacionesMiniBurgues');
    }
    
    /**
     * @Route("/productos/shakes", name="backend_publicacion_shakes")
     */
    public function publicacionShakesAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:publicacionesSkahes');
    }
    
    /**
     * @Route("/productos/fries", name="backend_publicacion_fries")
     */
    public function publicacionFriesAction()
    {
        return $this->forward('PublicacionesBundle:Publicacion:publicacionesFries');
    }
    
    /**
     * @Route("/login", name="backend_login")
     * @Template()
     */
    
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // obtiene el error de inicio de sesión si lo hay
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'BackendBundle:Default:login.html.twig',
            array(
                // último nombre de usuario ingresado
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
    
    /**
     * @Route("/login_check", name="backend_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="backend_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
    
    
    
}
