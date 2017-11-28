<?php
use \App\Models\User;
use Crypt;

class EncryptUserTest extends TestCase
{

    public function testEncryptUserInfo()
    {
        $user = [
            'email' => 'ikazuchi@gmail.com',
            'phone_number' => '0123456789',
            'first_name' => 'Nam',
            'last_name' => 'Pham',
            'first_name_kana' => 'test'
        ];

        $user = User::create($user);

        $this->assertTrue($user->email == 'ikazuchi@gmail.com');

    }
}