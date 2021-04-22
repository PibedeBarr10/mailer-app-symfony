<?php


namespace App\Controller\API;


use App\Service\Mailer;
use App\Service\UserVerification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SendNewsletterController extends AbstractController
{
    private Mailer $mailer;
    private UserVerification $userVerification;

    public function __construct(Mailer $mailer, UserVerification $userVerification )
    {
        $this->mailer = $mailer;
        $this->userVerification = $userVerification;
    }

    /**
     * @Route("/api/sendNewsletter", name="api_sendNewsletter", methods={"POST"})
     */
    public function sendNewsletter(Request $request): Response
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
            || !key_exists('newsletter_data', $requestData)
        ) {
            return new JsonResponse('No required data', 400);
        }

        $email = $requestData['email'];
        $newsletter_data = $requestData['newsletter_data'];

        $this->mailer->sendMail(
            $email,
            'Newsletter from Blog-App',
            'newsletter/index.html.twig',
            [$newsletter_data]
        );

        return new JsonResponse("Wysłano newsletter na adres: " . $email);
    }
}