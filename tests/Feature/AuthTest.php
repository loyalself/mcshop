<?php
use Tests\TestCase;
class AuthTest extends TestCase
{
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRegister(){
        $response = $this->get('wx/auth/register');
        echo $response->getContent();
    }
}
