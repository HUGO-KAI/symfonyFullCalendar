<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="app_api")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/{id}/edit", name="api.event.edit", methods={"PUT"})
     * @throws \Exception
     */
    public function majEvent(?Calendar $calendar, Request $request): Response
    {
        //On récupère la donnée
        $donnees = json_decode($request->getContent());
        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            //les données sont complètes
            //On initialise un code
            $code = 200;

            //on vérifie si l'id existe
            if(!$calendar){
                //on instance un rdv
                $calendar = new Calendar();

                //on change le code
                $code = 201;
            }
            //on hydrate l'objet avec les données
            $calendar->setTitle($donnees->title)
                ->setDescription($donnees->description)
                ->setStart(new \DateTime($donnees->start))
                ->setAllDay($donnees->allDay)
                ->setBackgroundColor($donnees->backgroundColor)
                ->setBorderColor($donnees->borderColor)
                ->setTextColor($donnees->textColor);
            if ($donnees->allDay) {
                $calendar->setEnd(new \DateTime($donnees->start));
            }else {
                $calendar->setEnd(new \DateTime($donnees->end));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();

            //on retourne le code
            return new Response('ok',200);
        }else{
            //les données sont incomplètes
            return new Response('Données incomplètes', 404);
        }
    }
}
