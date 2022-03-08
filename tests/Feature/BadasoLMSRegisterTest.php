<?php

namespace Uasoft\Badaso\Module\LMS\Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Uasoft\Badaso\Helpers\CallHelperTest;
use Uasoft\Badaso\Models\Role;
use Uasoft\Badaso\Models\User;
use Uasoft\Badaso\Models\UserRole;

class BadasoLMSRegisterTest extends TestCase
{
   public function testStartInit()
    {
        // init user login
        CallHelperTest::handleUserAdminAuthorize($this);
    }

   /** @test */
   public function testAddUserDatabase()
    {
        $name = Str::random(10);
        $create_user = [
            'name' => $name,
            'username' => $name,
            'email' => $name.'@gmail.com',
            'password' => Hash::make($name),
        ];
        $user = User::create($create_user);
        $this->assertEquals($create_user['name'] ,$user->name);
        $this->assertEquals($create_user['username'] ,$user->username);
        $this->assertEquals($create_user['email'] ,$user->email);
        $this->assertEquals($create_user['password'] ,$user->password);

        $user->delete();
    }

    public function testAddUser()
    {
        $name = Str::random(10);
        $password = Hash::make($name);
        $create_user = [
            'name' => $name,
            'username' => $name,
            'email' => $name.'@gmail.com',
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->json("POST", route('badaso.user.register'), $create_user);
        $response->assertStatus(400);
    }


    public function testAddUserWithNotEnoughParameter()
    {
        $name = Str::random(10);
        $password = Hash::make($name);
        $create_user = [
            'name' => $name,
            'username' => $name,
            'email' => $name.'@gmail.com',
            'password' => $password,
        ];

        $response = $this->json("POST", route('badaso.user.register'), $create_user);
        $response->assertStatus(400);
    }
}