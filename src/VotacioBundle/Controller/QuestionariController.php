<?php

namespace VotacioBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use VotacioBundle\Entity\Questionari;
use VotacioBundle\Entity\Pregunta;
use VotacioBundle\Form\QuestionariType;

/**
 * Questionari controller.
 *
 */
class QuestionariController extends Controller {

    private function security($request) {
        $session = $request->getSession();
        if ($session->get('roleUser') == "ROLE_ADMIN") {
            return true;
        } else
            return false;
    }

    /**
     * Lists all Questionari entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $questionaris = $em->getRepository('VotacioBundle:Questionari')->findAll();

        return $this->render('VotacioBundle:Admin:questionari_index.html.twig', array(
                    'questionaris' => $questionaris,
        ));
    }

    /**
     * Creates a new Questionari entity.
     *
     */
    public function newAction(Request $request) {
        if ($this->security($request)) {
            $questionari = new Questionari();
            $form = $this->createForm('VotacioBundle\Form\QuestionariType', $questionari);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $questionari->upload();
                $em->persist($questionari);
                $em->flush();

                return $this->redirectToRoute('admin_questionari_show', array('id' => $questionari->getId()));
            }

            return $this->render('VotacioBundle:Admin:questionari_new.html.twig', array(
                        'questionari' => $questionari,
                        'form' => $form->createView(),
            ));
        } else {
            return new Response("No tens permissos per accedir a aquest contingut");
        }
    }

    /**
     * Finds and displays a Questionari entity.
     *
     */
    public function showAction(Questionari $questionari, Request $request) {
        if ($this->security($request)) {
            $deleteForm = $this->createDeleteForm($questionari);

            return $this->render('VotacioBundle:Admin:questionari_show.html.twig', array(
                        'questionari' => $questionari,
                        'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return new Response("No tens permissos per accedir a aquest contingut");
        }
    }

    /**
     * Finds and remove a Pregunta entity.
     *
     */
    public function savePreguntesAction(Questionari $questionari, Request $request) {
        if ($this->security($request)) {
            $json = $request->request->get('preguntes');
            $preguntes = json_decode($json, true);
            $em = $this->getDoctrine()->getManager();
            foreach ($preguntes as $pregunta) {
                $objPre = new Pregunta();
                $objPre->setText($pregunta['text']);
                $objPre->setQuestionari($questionari);
                $em->persist($objPre);
                $em->flush();
            }
            return new Response("Respostes desades correctament");
        } else {
            return new Response("No tens permissos per accedir a aquest contingut");
        }
    }

    public function deletePreguntaAction(Questionari $questionari, Request $request) {
        if ($this->security($request)) {
            $em = $this->getDoctrine()->getManager();
            $pregunta = $request->request->get("idPre");
            $pregunta = intval($pregunta);

            foreach ($questionari->getPreguntes() as $item) {
                if ($item->getId() === $pregunta) {
                    $em->remove($item);
                    $em->flush();
                }
            }
            return new Response("Pregunta Eliminada Corretament");
        } else {
            return new Response("No tens permissos per accedir a aquest contingut");
        }
    }

    /**
     * Displays a form to edit an existing Questionari entity.
     *
     */
    public function editAction(Request $request, Questionari $questionari) {
        if ($this->security($request)) {
            $deleteForm = $this->createDeleteForm($questionari);
            $editForm = $this->createForm('VotacioBundle\Form\QuestionariType', $questionari);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($questionari);
                $em->flush();

                return $this->redirectToRoute('admin_questionari_edit', array('id' => $questionari->getId()));
            }

            return $this->render('VotacioBundle:Admin:questionari_edit.html.twig', array(
                        'questionari' => $questionari,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return new Response("No tens permissos per accedir a aquest contingut");
        }
    }

    /**
     * Deletes a Questionari entity.
     *
     */
    public function deleteAction(Request $request, Questionari $questionari) {
        if ($this->security($request)) {
            $form = $this->createDeleteForm($questionari);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($questionari);
                $em->flush();
            }

            return $this->redirectToRoute('admin_questionari_index');
        } else {
            return new Response("No tens permissos per accedir a aquest contingut");
        }
    }

    /**
     * Creates a form to delete a Questionari entity.
     *
     * @param Questionari $questionari The Questionari 192bomentity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Questionari $questionari) {

            return $this->createFormBuilder()
                            ->setAction($this->generateUrl('admin_questionari_delete', array('id' => $questionari->getId())))
                            ->setMethod('DELETE')
                            ->getForm()
            ;
            return new Response("No tens permissos per accedir a aquest contingut");
        
    }

}
