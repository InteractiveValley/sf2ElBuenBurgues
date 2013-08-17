<?php

namespace Richpolis\ElbuenBurguesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Richpolis\ElbuenBurguesBundle\Entity\Ubicaciones;
use Richpolis\ElbuenBurguesBundle\Form\UbicacionesType;

/**
 * Ubicaciones controller.
 *
 * @Route("/backend/ubicaciones")
 */
class UbicacionesController extends Controller
{
    /**
     * Lists all Ubicaciones entities.
     *
     * @Route("/", name="ubicaciones")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $query = $em->getRepository("ElbuenBurguesBundle:Ubicaciones")->getQueryUbicacionesActivas();

        $paginator = $this->get('knp_paginator');
        
        $pagination = $paginator->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            10
        );

        return array(
            'pagination' => $pagination,
        );
    }

    /**
     * Finds and displays a Ubicaciones entity.
     *
     * @Route("/{id}/show", name="ubicaciones_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElbuenBurguesBundle:Ubicaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ubicaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Ubicaciones entity.
     *
     * @Route("/new", name="ubicaciones_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Ubicaciones();
        $form   = $this->createForm(new UbicacionesType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
             'actual' => 0,
        );
    }

    /**
     * Creates a new Ubicaciones entity.
     *
     * @Route("/create", name="ubicaciones_create")
     * @Method("POST")
     * @Template("ElbuenBurguesBundle:Ubicaciones:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Ubicaciones();
        $form = $this->createForm(new UbicacionesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ubicaciones_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
             'actual' => 0,
        );
    }

    /**
     * Displays a form to edit an existing Ubicaciones entity.
     *
     * @Route("/{id}/edit", name="ubicaciones_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElbuenBurguesBundle:Ubicaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ubicaciones entity.');
        }

        $editForm = $this->createForm(new UbicacionesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Ubicaciones entity.
     *
     * @Route("/{id}/update", name="ubicaciones_update")
     * @Method("POST")
     * @Template("ElbuenBurguesBundle:Ubicaciones:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ElbuenBurguesBundle:Ubicaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ubicaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UbicacionesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ubicaciones_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Ubicaciones entity.
     *
     * @Route("/{id}/delete", name="ubicaciones_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ElbuenBurguesBundle:Ubicaciones')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ubicaciones entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ubicaciones'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * Displays a form to edit an existing Ubicaciones entity.
     *
     * @Route("/actual", name="ubicaciones_actual")
     * @Template("ElbuenBurguesBundle:Ubicaciones:new.html.twig")
     */
    public function actualAction()
    {
        $entity = new Ubicaciones();
        $form   = $this->createForm(new UbicacionesType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'actual' => 1,
        );
    }
}
