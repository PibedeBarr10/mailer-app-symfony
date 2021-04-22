<?php

namespace App\Controller\API;

use App\Service\CreateCSV;
use App\Service\Mailer;
use App\Service\UserVerification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SendMailController extends AbstractController
{
    private Mailer $mailer;
    private CreateCSV $createCSV;
    private UserVerification $userVerification;

    public function __construct(
        Mailer $mailer,
        UserVerification $userVerification,
        CreateCSV $createCSV
    )
    {
        $this->mailer = $mailer;
        $this->createCSV = $createCSV;
        $this->userVerification = $userVerification;
    }

    /**
     * @Route("/api/sendMail", name="api_sendMail", methods={"POST"})
     */
    public function sendMail(Request $request): Response
    {
        if (!$this->userVerification->checkUser(
            $request->getUser(),
            $request->getPassword()
        )) {
            return new JsonResponse('No auth', 401);
        }

        $requestData = $request->request->all();

        if (empty($requestData)) {
            $requestData = json_decode($request->getContent(), true);
        }

        if (!key_exists('email', $requestData)
            || !key_exists('email_data', $requestData)
            || !key_exists('file_data', $requestData)
        ) {
            return new JsonResponse('No required data', 400);
        }

        $email = $requestData['email'];
        $email_data = $requestData['email_data'];
        $file_data = $requestData['file_data'];

        if (empty($file_data)) {
            $this->mailer->sendMail(
                $email,
                'Raport mail from Mailer-App',
                'mail/index.html.twig',
                $email_data
            );

            return new JsonResponse("Wysłano raport na adres: " . $email);
        }

        $this->createCSV->createCSV($file_data);

        $this->mailer->sendMailWithAttachment(
            $email,
            'Raport mail from Mailer-App',
            'mail/index.html.twig',
            'tasks.csv',
            $email_data
        );

        return new JsonResponse("Wysłano raport na adres: " . $email);
    }
}