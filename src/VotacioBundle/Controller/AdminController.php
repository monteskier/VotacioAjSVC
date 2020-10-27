<?php

namespace VotacioBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type;
use VotacioBundle\Entity\User;

class AdminController extends Controller {

    private function security($request) {
        $session = $request->getSession();
        if ($session->get('roleUser') == "ROLE_ADMIN") {
            return true;
        } else
            return false;
    }

    public function indexAction(Request $request) {

        if ($this->security($request)) {
            return $this->render('VotacioBundle:Admin:index.html.twig');
        }
        throw $this->createNotFoundException('No ets administrador per accedir a aquest lloc.');
    }

    public function pressencialAction(Request $request) {
        if ($this->security($request)) {
            if ($request->isMethod('POST')) {
                $dni = $request->request->get('dni');
                
                $em = $this->getDoctrine()->getManager();
                $padro = $em->getRepository("VotacioBundle:Padro")->findAll();
                foreach ($padro as $p) {
                    if ($p->getDni() == $dni) {
                        $value = $p->getQuestionaris();
                        if ($value !== 1) {
                            $p->setQuestionaris(1);
                            $em->persist($p);
                            $em->flush();
                            return new Response("S'han dessat els canvis");
                        }if($value == 1){
                            return new Response("Aquest DNI ja ha efectuat una votació");
                        }
                    }
                }
                return new Response("Aquest DNI no correspont a cap ciutadà empadronat a Sant Vicenç de Castellet");
            } else {
                return $this->render("VotacioBundle:Admin:votPressencial.html.twig");
            }
        }
        throw $this->createNotFoundException('No ets administrador per accedir a aquest lloc.');
    }

    public function loginAction(Request $request) {
        $user = new User();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder($user)
                ->add('name', Type\TextType::class)
                ->add('password', Type\PasswordType::class)
                ->add('login', Type\SubmitType::class, array('label' => 'Login'))
                ->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $pass = $data->getPassword();
                    $user = $em->getRepository('VotacioBundle:User')->findOneBy(array("name" => $data->getName()));
                    if ($user->decodePass($data->getPassword()) == FALSE) {

                        throw $this->createNotFoundException('Contrasenya incorrectes');
                    }
                } catch (Exception $ex) {
                    throw $this->createNotFoundException('Usuari inexistent');
                }
                if (!$user) {
                    throw $this->createNotFoundException("Usuari inexistent");
                } else if ($user->decodePass($pass) == true) {
                    $session = new \Symfony\Component\HttpFoundation\Session\Session();
                    $session->set('idUser', $user->getId());
                    $session->set('roleUser', $user->getRole());
                    $session->start();
                    $em->flush();
                }
                if ($user->getRole() == "ROLE_ADMIN") {
                    return $this->redirect($this->generateUrl('votacio_adminHomepage'), 301);
                }
            }
        } else {
            return $this->render('VotacioBundle:Admin:login.html.twig', array("form" => $form->createView()));
        }
    }

    public function logoutAction(Request $request) {
        $session = $request->getSession();
        $session->remove('idUser');
        $session->remove('roleUser');
        return $this->redirect($this->generateUrl('votacio_adminLogin'), 301);
    }

    // nomes executar una vegada
    public function createAdminAction() {

        $em = $this->getDoctrine()->getManager();
        $admin = new User();
        $admin->setName("root");
        $admin->setPassword("AjSVCPress");
        $admin->hashPass();
        $admin->setRole("ROLE_ADMIN");
        $em->persist($admin);
        $em->flush();
        $response = "S'a creat usuari  admin";
        return $this->render('VotacioBundle:Admin:index.html.twig', array("response" => $response));
    }
    public function showResultsAction(Request $request){
        if ($this->security($request)) {
            $em = $this->getDoctrine()->getManager();
            $questionaris = $em->getRepository("VotacioBundle:Questionari")->findAll();
            return $this->render('VotacioBundle:Admin:resultats.html.twig',array("questionaris"=>$questionaris));
        }else{
          throw $this->createNotFoundException('No ets administrador per accedir a aquest lloc.');
        }
    }

}
