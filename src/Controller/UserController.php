<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("/user/new", name="user_new")
     */
    public function newUser(Request $request)
    {
        header("Access-Control-Allow-Origin: *");

        $mail = $request->query->get('mail');


        function RandomToken($length = 32){
            if(!isset($length) || intval($length) <= 8 ){
                $length = 32;
            }
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes($length));
            }
            if (function_exists('mcrypt_create_iv')) {
                return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
            }
            if (function_exists('openssl_random_pseudo_bytes')) {
                return bin2hex(openssl_random_pseudo_bytes($length));
            }
        }

        $token = RandomToken(10);

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                "mail" => $mail
            ]);




        if (!$user) {

            $em = $this->getDoctrine()->getManager();

            $NewUser = new User();

            $NewUser->setMail($mail);
            $NewUser->setTokenAuth($token);
            $em->persist($NewUser);
            $em->flush();




            $result = false;
            return new JsonResponse($NewUser->getId(), 200);

        }else{
            $result = true;
            return new JsonResponse($result, 200);

        }




    }

}
