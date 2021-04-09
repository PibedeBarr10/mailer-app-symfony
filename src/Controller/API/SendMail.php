<?php

namespace App\Controller\API;

use App\Service\CreateCSV;
use App\Service\Mailer;
use App\Service\UserVerification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SendMail extends AbstractController
{
    private Mailer $mailer;
    private CreateCSV $createCSV;
    private UserVerification $userVerification;

    public function __construct(
        Mailer $mailer,
        UserVerification $userVerification,
        CreateCSV $createCSV
    ) {
        $this->mailer = $mailer;
        $this->createCSV = $createCSV;
        $this->userVerification = $userVerification;
    }

    /**
     * @Route("/sendMail", name="sendMail", methods={"GET", "POST"})
     */
    public function sendMail(Request $request): Response
    {
        if (!$this->userVerification->checkUser(
            $request->getUser(),
            $request->getPassword()
        )) {
            return new Response('<p>Błąd - brak dostępu</p>');
        }

        $data = json_decode($request->getContent());
        $email = $data->email;

        if (!$email) {
            return new Response('<p>Błąd - uruchomione bez parametrów</p>');
        }

        $body = $data->body;
        $file_data = $data->file_data;

        $this->createCSV->createCSV($file_data);

        $this->mailer->sendMailWithAttachment(
            $email,
            'Raport mail from Mailer-App',
            'mail/index.html.twig',
            'tasks.csv',
            $body
        );

        return new Response("Wysłano raport na adres: ".$email);
    }
}
