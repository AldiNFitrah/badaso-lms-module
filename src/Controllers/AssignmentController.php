<?php

namespace Uasoft\Badaso\Module\LMSModule\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Uasoft\Badaso\Controllers\Controller;
use Uasoft\Badaso\Helpers\ApiResponse;
use Uasoft\Badaso\Module\LMSModule\Enums\CourseUserRole;
use Uasoft\Badaso\Module\LMSModule\Helpers\CourseUserHelper;
use Uasoft\Badaso\Module\LMSModule\Models\Assignment;

class AssignmentController extends Controller
{
    public function add(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'course_id' => 'required|integer',
                'title' => 'required|string|max:255',
                'due_date' => 'required|date_format:Y-m-d H:i:sP',
                'description' => 'nullable|string|max:65535',
                'point' => 'nullable|integer',
                'file_url' => 'nullable|string|max:65535',
                'link_url' => 'nullable|string|max:65535',
            ]);

            if (!CourseUserHelper::isUserInCourse(
                $user->id,
                $request->input('course_id'),
                CourseUserRole::TEACHER,
            )) {
                throw ValidationException::withMessages([
                    'course_id' => 'Course not found',
                ]);
            }

            $assignment = Assignment::create([
                'course_id' => $request->input('course_id'),
                'topic_id' => $request->input('topic_id'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'due_date' => $request->input('due_date'),
                'point' => $request->input('point'),
                'file_url' => $request->input('file_url'),
                'link_url' => $request->input('link_url'),
                'created_by' => $user->id,
            ]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
