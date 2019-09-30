<?php


namespace App\Tests\Mailer;


use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;

class MailerTest extends TestCase
{
  public function testConfirmationEmail() {
    $user = new User();
    $user->setMail('abc@d.com');
    $mailFrom = 'mailfrom@admin.com';

    $switftMailer = $this->getMockBuilder(Swift_Mailer::class)
        ->disableOriginalConstructor()
        ->getMock();

    $twigMock = $this->getMockBuilder(\Twig_Environment::class)
        ->disableOriginalConstructor()
        ->getMock();

//    $twigMock->expects($this->once())->method('render');

    $twigMock->expects($this->once())->method('render')
      ->with('email/registration.html.twig', ['user'=>$user,]);
    $switftMailer->expects($this->once())->method('send')
      ->with($this->callback(function($subject) use ($mailFrom){
        $messageStr = (string)$subject;
        dump($messageStr);
        return strpos($messageStr, "From: ".$mailFrom)
            && strpos($messageStr, "Content-Type: text/html");


//        dump($messageStr);
//        return true;
      }))->willReturn('This is a message body');

    $mailer = new Mailer($switftMailer, $twigMock, $mailFrom);
    $mailer->sendConfirmationEmail($user);

  }
}