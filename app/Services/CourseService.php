<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function createCourse(array $data, int $instructorId, ?UploadedFile $thumbnail = null): Course
    {
        return DB::transaction(function () use ($data, $instructorId, $thumbnail) {
            $data['instructor_id'] = $instructorId;
            $course = Course::create($data);
            
            if ($thumbnail) {
                $course->addMedia($thumbnail)
                       ->toMediaCollection('thumbnail');
            }
            
            return $course;
        });
    }

    public function updateCourse(Course $course, array $data, ?UploadedFile $thumbnail = null): Course
    {
        return DB::transaction(function () use ($course, $data, $thumbnail) {
            $course->update($data);
            
            if ($thumbnail) {
                $course->clearMediaCollection('thumbnail');
                $course->addMedia($thumbnail)
                       ->toMediaCollection('thumbnail');
            }
            
            return $course;
        });
    }

    public function deleteCourse(Course $course): void
    {
        DB::transaction(function () use ($course) {
            $course->clearMediaCollection('thumbnail');
            $course->delete();
        });
    }

    public function togglePublish(Course $course): void
    {
        $course->update(['is_published' => !$course->is_published]);
    }
}