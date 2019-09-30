<?php


namespace App\Tests\Security;


use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
  public function testTokenGeneration(){
    $tokenGen = new TokenGenerator();
    $token = $tokenGen->getRandomSectureToken(30);
    $this->assertEquals(30, strlen($token));
//    $this->assertEquals(1, preg_match("/[A-Za-z0-9]/", $token ));
//    $token[16] = '*';
    $this->assertTrue(ctype_alnum($token), 'Token contains incorrect characters');
  }

}