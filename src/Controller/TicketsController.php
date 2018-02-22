<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Tickets;
use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class TicketsController extends Controller
{

    /**
     * @Route("/tickets", name="tickets_admin")
     */
    public function selectEvent(Connection $connection, Environment $twig)
    {


        $event = $this->getDoctrine()
            ->getRepository(Events::class)
            ->findAll();

        $tickets = $connection->fetchAll("SELECT *  FROM tickets INNER JOIN events ON tickets.event_id = events.id");


        return new Response($twig->render('tickets/new.html.twig',  [
            "event" => $event,
            "tickets" => $tickets
        ]));
    }

    /**
     * @Route("/tickets/all", name="tickets_all")
     */
    public function getTickets(Connection $connection)
    {

        $tickets = $connection->fetchAll("SELECT *  FROM tickets INNER JOIN events ON tickets.event_id = events.id");



        return new JsonResponse($tickets, 200);

    }


    /**
     * @Route("/tickets/new", name="tickets_new_id")
     * @Method("POST")
     */
    public function newTickets(Request $request, Connection $connection)
    {


        $eventId = $request->request->get('select_event');
        $prix_depart = $request->request->get('prix_depart');
        $nombre_ticket = $request->request->get('nombre_ticket');

        for ($i = $nombre_ticket; $i >= 0; $i--)
        {
            $sql = "INSERT INTO tickets
( start_price, status, seat_number, event_id) VALUES ( :start_price, :status, :seat_number, :event)";

            $stmt = $connection->prepare($sql);

            $stmt->bindValue(':start_price', $prix_depart);
            $stmt->bindValue(':status', "M");
            $stmt->bindValue(':event', $eventId);
            $stmt->bindValue(':seat_number', rand(1, 200));
            $result = $stmt->execute();

        }




        return $this->redirectToRoute('tickets');
    }

}
