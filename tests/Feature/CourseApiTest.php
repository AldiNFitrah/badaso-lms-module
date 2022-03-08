<?php

namespace Uasoft\Badaso\Module\LMSModule\Tests\Feature;

use Tests\TestCase;
use Uasoft\Badaso\Module\LMSModule\Models\Course;
use Uasoft\Badaso\Module\LMSModule\Models\User;

class BadasoCourseApiTest extends TestCase
{
    public function testCreateCourseWithoutLoginExpectResponse401()
    {
        $url = route('badaso.course.add');
        $response = $this->json('POST', $url);
        $response->assertStatus(401);
    }

    public function testCreateCourseAsLoggedInUserWithValidDataExpectResponse200()
    {
        $loginUrl = '/admin/v1/auth/login';
        $user = User::factory()->create();

        $loginResponse = $this->json('POST', $loginUrl, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('accessToken');

        $url = route('badaso.course.add');
        $response = $this->json('POST', $url, [
            'name' => 'Test course',
            'subject' => 'Test subject',
            'room' => 'Test room',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);
    }

    public function testCreateCourseAsLoggedInUserWithValidDataExpectResponseCreatedCourseWithId()
    {
        $loginUrl = '/admin/v1/auth/login';
        $user = User::factory()->create();

        $loginResponse = $this->json('POST', $loginUrl, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('accessToken');

        $url = route('badaso.course.add');
        $response = $this->json('POST', $url, [
            'name' => 'Test course',
            'subject' => 'Test subject',
            'room' => 'Test room',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);

        $courseData = $response->json('data');
        $this->assertArrayHasKey('id', $courseData);
        $this->assertNotNull($courseData['id']);
        $this->assertEquals('Test course', $courseData['name']);
        $this->assertEquals('Test subject', $courseData['subject']);
        $this->assertEquals('Test room', $courseData['room']);
    }

    public function testCreateCourseAsLoggedInUserWithValidDataExpectCourseCreated()
    {
        $loginUrl = '/admin/v1/auth/login';
        $user = User::factory()->create();

        $loginResponse = $this->json('POST', $loginUrl, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('accessToken');

        $courseCountBefore = Course::count();

        $url = route('badaso.course.add');
        $this->json('POST', $url, [
            'name' => 'Test course',
            'subject' => 'Test subject',
            'room' => 'Test room',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $courseCountAfter = Course::count();
        $course = Course::first();

        $this->assertEquals(0, $courseCountBefore);
        $this->assertEquals(1, $courseCountAfter);
        $this->assertEquals('Test course', $course->name);
        $this->assertEquals('Test subject', $course->subject);
        $this->assertEquals('Test room', $course->room);
    }

    public function testCreateCourseAsLoggedInUsertWithValidDataExpectUserHasTheRoleTeacherForTheCreatedCourse()
    {
        $loginUrl = '/admin/v1/auth/login';
        $user = User::factory()->create();

        $loginResponse = $this->json('POST', $loginUrl, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('accessToken');

        $url = route('badaso.course.add');
        $this->json('POST', $url, [
            'name' => 'Test course',
            'subject' => 'Test subject',
            'room' => 'Test room',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $user->fresh();
        $this->assertEquals(1, CourseUser::count());
        $this->assertEquals(1, $user->courses->count());
        $this->assertEquals(CourseUserRole::TEACHER, $user->courses->first()->pivot->role);
    }

    public function testCreateCourseAsLoggedInUserWithoutEitherNameSubjectOrRoomExpectResponse422()
    {
        $loginUrl = '/admin/v1/auth/login';
        $user = User::factory()->create();

        $loginResponse = $this->json('POST', $loginUrl, [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('accessToken');

        $url = route('badaso.course.add');
        $response1 = $this->json('POST', $url, [
            'subject' => 'Test subject',
            'room' => 'Test room',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response2 = $this->json('POST', $url, [
            'name' => 'Test course',
            'room' => 'Test room',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response3 = $this->json('POST', $url, [
            'name' => 'Test course',
            'subject' => 'Test subject',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response1->assertStatus(422);
        $response2->assertStatus(422);
        $response3->assertStatus(422);
    }
}