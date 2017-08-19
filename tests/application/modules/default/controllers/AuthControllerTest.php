<?php

class AuthControllerTest extends Rad_Test_PHPUnit_BaseControllerTestCase
{
    public function testLoginAction()
    {
        $this->request->setMethod('POST')
            ->setPost(array(
                'login'    => 'admin',
                'password' => 'admvida12'
            ));
        $this->dispatch('/auth/login');

        // no tira una excepcion
        $this->assertFalse($this->response->isException());  

        $body = $this->response->outputBody();

        //ver si contesto ok al login
        $this->assertTrue( ($body == '{"success":true,"usuario":"admin"}'), 'fallo en el login');
    }
}