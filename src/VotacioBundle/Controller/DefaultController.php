<?php
namespace VotacioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use VotacioBundle\Form\QuestionariType;
use VotacioBundle\Entity\Padro;
use VotacioBundle\Entity\Questionari;
use VotacioBundle\Entity\Smsup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class DefaultController extends Controller
{
    public function indexAction()
    {
        //return $this->render('VotacioBundle:Default:resultats.html.twig');//modificat per mostrar la taula ara que ha acavat el tema 2017/05/30
        $vots = 0;
        $em = $this->getDoctrine()->getManager();
        //$questionaris = $em->getRepository('VotacioBundle:Questionari')->findAll();
        $today = new \DateTime(date("Y-m-d"));
        $query = $em->createQuery(
            'SELECT q
            FROM VotacioBundle:Questionari q
            WHERE q.startDate <= :today and q.endDate >= :today
            ORDER BY q.id ASC'
        )->setParameter('today', $today);

        $questionaris_life = $query->getResult();//Els queencara estan en proces

        $query = $em->createQuery(
            'SELECT q
            FROM VotacioBundle:Questionari q
            WHERE q.endDate < :today or q.startDate > :today
            ORDER BY q.id ASC'
        )->setParameter('today', $today);

        $questionaris_die = $query->getResult();


        $padro = $em->getRepository("VotacioBundle:Padro")->findAll();
        foreach($padro as $p){
          $vots += $p->getQuestionaris();
        }


        return $this->render('VotacioBundle:Default:index.html.twig', array("questionaris_life"=>$questionaris_life, "questionaris_die"=>$questionaris_die, "vots"=>$vots));


      //  return $this->render('VotacioBundle:Default:resultats.html.twig');

    }
    public function indexOfflineAction(){
        return $this->render('VotacioBundle:Default:index_offline.html.twig',array());
    }
    public function registreMesInfoAction(Questionari $quest){
        $serializer = $this->get('serializer');
        $quest = $serializer->serialize($quest, 'json');
        return new Response($quest);
    }
    public function votarAction(Questionari $questionari){

         return $this->render('VotacioBundle:Default:votar.html.twig', array("questionari"=>$questionari));

    }
    public function registreAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $valors = $request->request->get('valors');
        $valors_array = explode(',',$valors);
        $qb = $em->createQueryBuilder();
        $qb->select('q');
        $qb->from('VotacioBundle:Questionari', 'q');
        $qb->where($qb->expr()->in('q.id', $valors_array));

        //ArrayCollection
        $result = $qb->getQuery()->getResult();

        return $this->render('VotacioBundle:Default:registre.html.twig',array("questionaris"=>$result, "valors"=>$valors));
    }
    public function testVot($p){
        $value= $p->getQuestionaris();

            if($value==True){
                return "false3";
            }

        return "true";
    }
    public function testYears($naix){
        $naix = new \DateTime($naix);
        $today = new \DateTime(date("Y-m-d"));
        $anys = ($naix->diff($today)->format('%y'));

        if($anys>=16){
            return "true";
        }else return "false2";
    }
    public function testMobil($p,$mobil_user,$em){
        $mobil_db = $p->getMobil();
        $intents = $p->getIntents();
            if($mobil_db!==NULL && $intents >= 3){//Comprovamos que el campo del mobil esta yeno y por tanto ya se envio un sms, salimos con error5
                return "false5";
            }else if($mobil_db!==NULL && $intents < 3){
                $p->setIntents($intents+1);
                return "try";
            }else if($mobil_db==NULL){ //En El cas de que no hayan datos de mobil del usuario, se revisa que ese mobil no lo haya utilizado otra persona
                $padro = $em->getRepository("VotacioBundle:Padro")->findAll();
                foreach($padro as $p_sub){
                    if($p_sub->getMobil()==$mobil_user){//Si existe alguien que tiene ese mobil , salimos con error4
                        return "false4";
                    }
                }
                return "true";



            }

    }
    public function registreTestAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $dni = $request->request->get('dni');
        $padro = $em->getRepository("VotacioBundle:Padro")->findAll();
        $mobil_user = $request->request->get('mobil');

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        foreach($padro as $p)
        {
            if($p->getDni()==$dni){
                $response = $this->testYears($p->getDataNeix());
                if($response==="false2"){
                    $msg = $serializer->serialize(array("msg" => "Les votacions solament les poden fer adults igual o major a 16 anys. Dispenseu les molèsties".$dni." data neix:".$p->getDataNeix())  , 'json');
                    return new Response ($msg);
                }
                $response = $this->testVot($p);
                if($response==="false3"){
                    $msg = $serializer->serialize(array("msg"=>"Aquest ciutadà ja ha efectuat una votació anteriorment."), 'json');
                    return new Response($msg);
                }
                $response = $this->testMobil($p,$mobil_user,$em);
                if($response==="false4"){
                    $msg =  $serializer->serialize(array("msg" => "Aquest numero de mòbil ja s'ha utilitzat en una anterior votació."),'json');
                    return new Response($msg);
                }else if($response==="false5"){
                    $msg = $serializer->serialize(array("msg" => "S'ha rebassat el màxim d'intents permessos 3."), 'json');
                    return new Response($msg);
                }else if($response=="try"){
                    $session = new \Symfony\Component\HttpFoundation\Session\Session();
                    $session->set('dni',$dni);
                    $session->set('mobil',$mobil_user);
                    $session->start();
                    $msg = $serializer->serialize(array("msg" =>"true", "body"=>"Revisa el la busta de missatges, recorda que solament s'envia un codi una vegada. Et queden:".(3-$p->getIntents())." intents",  "data" => $p->getCodi()), 'json');
                    $em->persist($p);
                    $em->flush();
                    return new Response($msg);
                }
                $p->setMobil($mobil_user);
                //Aqui es crea el codi per enviar el sms;
                //de momento lo dejmos pendiente...
                $codi = rand(10000, 90000);
                $p->setCodi($codi);
                $em->persist($p);
                $em->flush();
                $session = new \Symfony\Component\HttpFoundation\Session\Session();
                $session->set('dni',$dni);
                $session->set('mobil',$mobil_user);
                $session->start();

                //Cridem la funcio de enviment de sms

                $this->sms($codi, $mobil_user);
                //var_dump($session->get('dni'));
                $msg = $serializer->serialize(array("msg" =>"true", "body"=>"NULL", "data" => $p->getCodi()), 'json');
                return new Response($msg);
            }
        }
        $msg = $serializer->serialize(array("msg" => "Aquest DNI/NIF no està empadronat en Sant Vicenç de Castellet"),'json');
        return new Response($msg);
    }
    public function registreTestSmsAction(Request $request){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $mobil = $session->get('mobil');
        $dni = $session->get('dni');
        $ciutada = $em->getRepository("VotacioBundle:Padro")->findOneBy(array("dni"=>$dni));
        $codi = $request->request->get('codi');//Aqui esta el problema!!
        if(intval($mobil==$ciutada->getMobil()) && $ciutada->getCodi()==intval($codi)){
            $msg = array("msg"=>"true");
            $msg = $serializer->serialize($msg,'json');
            return new Response($msg);
        }else{
            $msg = array("msg"=>"false");
            $msg = $serializer->serialize($msg,'json');
            return new Response($msg);
        }
    }
    //funcio de enviar correus
    public function sms($text, $mobil) {
        /*
        $text = ("Gràcies per la seva col·laboració al Pressupost Participatiu de Sant Vicenç de Castellet. El seu codi de vot és:".$text);
        $s = new Smsup('quimet','kKT9y5xb');
        $s->NuevoSMS($text, array($mobil),'',"Pressupostparticipatiu.svc.cat", "607948569");
        */
        $sender = $this->get('smsup.smsupapi.sender');
        $sms = $sender->getNewSms()
                ->setTexto("Gràcies per participar en el Pressupost Participatiu AJSVC 2021 de Sant Vicenç de Castellet. El seu codi de vot és.:".$text)
                ->setNumeros([$mobil]);
        $resul = $sender->enviarSms($sms);
        if ($resul->getHttpcode() === 200) {
            $idenvio = $resul->getResult()[0]['id'];

        }
        else{

        }


    }
    public function registreInfoAction(){
        $em = $this->getDoctrine()->getManager();
        $questionaris = $em->getRepository("VotacioBundle:Questionari")->findAll();
       $msg = "Els resultats de l'anterior enquesta són els següents:";
     return $this->render('VotacioBundle:Default:finalitzacio.html.twig',array("questionaris"=>$questionaris, "missatge"=>$msg));
    }
    public function registreConfirmAction(Request $request){
         $session = $request->getSession();
         $em = $this->getDoctrine()->getManager();
         $questionaris = $em->getRepository("VotacioBundle:Questionari")->findAll();
         //$id = $request->request->get("pregunta");
         $dni = $session->get('dni');
         $valors = $request->request->get('valors');
         $valors_array = explode(',',$valors);
         $padro = $em->getRepository("VotacioBundle:Padro")->findOneBy(array("dni"=>$dni));

         $buffer = $padro->getQuestionaris();
         $padro->setQuestionaris(true);
         $em->persist($padro);
         $em->flush();
         $session->clear();

        /*Fem un cerca de tots els questionaris triats*/

        $qb = $em->createQueryBuilder();
        $qb->select('q');
        $qb->from('VotacioBundle:Questionari', 'q');
        $qb->where($qb->expr()->in('q.id', $valors_array));

        //ArrayCollection
        $result = $qb->getQuery()->getResult();

        /*Aqui va el codi "for" de incrementar el vot de Questionari escollit en el array id valors_array*/

        foreach($result as $q){
            $q->setVots(intval(1+$q->getVots()));
            $em->persist($q);
            $em->flush();

        }
        $missatge = "Gràcies per fer la seva votació, informem que el resultat actual de l'enquesta es el següent:";
        return $this->render('VotacioBundle:Default:finalitzacio.html.twig',array("questionaris"=>$questionaris, "missatge"=>$missatge));


    }
}
