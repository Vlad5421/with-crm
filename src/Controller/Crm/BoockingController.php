<?php

namespace App\Controller\Crm;

use App\Entity\Card;
use App\Entity\Service;
use App\Repository\ComplectRepository;
use App\Repository\FilialRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use App\Services\CalendarMaker;
use App\Services\ScheduleMaker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoockingController extends AbstractController
{
    // Календарь - работает
    #[Route('/booking/filials/{filial_id}/services/{service_id}', name: 'app_booking_calendar')]
    public function index(
        Request $request,
        CalendarMaker $calendarMaker,
        ScheduleMaker $scheduleMaker,
        $filial_id,
        $service_id
    ): Response
    {
        $calendar = $calendarMaker->create($request);


        /** @var Service $service */
        $schedule = $scheduleMaker->create($filial_id, $service_id, $calendar->date_string);

        return $this->render("booking/calendar.html.twig", [
            'countRows' => $calendar->count_rows,
            'calenadar' => $calendar,
            'date' => date('d.m.Y'),
            'schedule' => $schedule,
            'filSer' => ['filial' => $filial_id, 'service' => $service_id],
            'page' => 'Запись на услугу'
        ]);
    }

    // Список филиалов - работает
    #[Route('/booking/filials', name: 'app_booking_filials')]
    public function filialsList(FilialRepository $filRepository  ): Response
    {

        //$page = 'add-service';
        //if ($filial == 'all'){
        //    $page = 'filials';
        //}

        return $this->render("booking/filials.html.twig", ['filials' => $filRepository->findAll(), 'page' => 'Выбор услуги']);
    }
    // Список услуг на филиале - работает
    #[Route('/booking/filials/{filial_id}/services', name: 'app_booking_sevices')]
    public function servicesList(ComplectRepository $complectRepository, $filial_id): Response
    {
        $complects = $complectRepository->findBy(['filial' => $filial_id]);

        return $this->render(
            "booking/services.html.twig",
            [
                'complects' => $complects,
                'filial_id' => $filial_id,
                'page' => 'Выбор услуги']
        );
    }

    // Создание записи по апи - работает
    #[Route('/api1/crm/boocking/createcard', name: 'app_api1_crm_boocking_createcard')]
    public function boockingCreate(Request $request,
                                   ComplectRepository $complectRepository,
                                   SessionRepository $sessionR,
                                   UserRepository $specialistR,
                                   EntityManagerInterface $em
    ): Response
    {
        $data = json_decode($request->getContent());

        $complect = $complectRepository->findOneBy(['filial' => $data->filial, 'service' => $data->service]);
        $session = $sessionR->findOneBy(['id'=> $data->time]);
        $specialist = $specialistR->findOneBy(['id'=>$data->spec]);
        $date = new \DateTime(ScheduleMaker::normalsDate($data->date));

        $card = new Card();
        $card
            ->setSession($session)
            ->setSpecialist($specialist)
            ->setComplect($complect)
            ->setDate($date)
        ;

        $em->persist($card);
        $em->flush();

        return $this->json(['ok', 'id' => $card->getId()], 201) ;
    }


    #[Route('/boocking/test', name: 'app_tests')]
    public function testService(ComplectRepository $comRep): Response
    {
        $complect = $comRep->findOneBy(['id' => '1']);
        dd($complect->getUsers()->toArray());

        return $this->render('crm/boocking/add-service.html.twig', ['services' => $serviceRep->findAll(), 'page' => 'Выбор услуги']);
    }


}
