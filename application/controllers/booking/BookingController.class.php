<?php


class BookingController
{
	public function httpGetMethod(Http $http)
	{
		$userSession = new UserSession();
		if($userSession->isAuthenticated() == false)
		{
            // On ne peut pas réserver sans être connecté !
			$http->redirectTo('/user/login');
		}
	}

	public function httpPostMethod(Http $http, array $formFields)
	{

        $userSession = new UserSession();
        if($userSession->isAuthenticated() == false)
        {
            // On ne peut pas réserver sans être connecté !
            $http->redirectTo('/user/login');
        }

        // Récupération du compte client de l'utilisateur connecté.
        $userId = $userSession->getUserId();

		// Création de la réservation.
        $bookingModel = new BookingModel();
		$bookingModel->create
		(
			$userId,
			$formFields['bookingYear'].'-'.
                $formFields['bookingMonth'].'-'.
				$formFields['bookingDay'],
		    $formFields['bookingHour'].':'.
                $formFields['bookingMinute'],
			$formFields['numberOfSeats']
		);



		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host = "smtp.gmail.com";
		$mail->SMTPAuth = true;
		$mail->Port = 587;
		$mail->Username = "castejon.morgan@gmail.com";
		$mail->Password = "";
		$mail->IsHTML(true);
		$mail->CharSet = "utf-8";
		$mail->SetFrom('castejon.morgan@gmail.com', 'Expéditeur');
		$mail->Subject = 'Objet de l\'email';

		$mail->Body = 'La réservation contient '.$formFields['numberOfSeats']." de sièges";

		$mail->AddAddress($_SESSION["email"]);

		if(!$mail->Send()) {
		  echo 'Message was not sent.';
		  echo 'Mailer error: ' . $mail->ErrorInfo;
		} else {
		  echo 'Message has been sent.';
		}
        // Redirection vers la page d'accueil.
		$http->redirectTo('/');
	}
}