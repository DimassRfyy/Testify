<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        // Ambil data siswa dan pertanyaan
        $students = $course->students()->orderBy('id', 'DESC')->get();
        $questions = $course->questions()->orderBy('id', 'DESC')->get();
        $totalQuestion = $questions->count();
    
        foreach ($students as $student) {
            // Ambil jawaban siswa berdasarkan course
            $studentAnswer = StudentAnswer::whereHas('question', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->where('user_id', $student->id)->get();
    
            // Hitung total jawaban dan jawaban benar
            $answerCount = $studentAnswer->count();
            $correctAnswerCount = $studentAnswer->where('answer', 'correct')->count();
    
            // Jika belum menjawab sama sekali
            if ($answerCount == 0) {
                $student->status = "Belum Memulai";
                $student->score = 0;
            } else {
                // Hitung skor dalam skala 0-100
                $student->score = ($correctAnswerCount / $totalQuestion) * 100;
    
                // Tentukan status berdasarkan skor
                if ($student->score < 60) {
                    $student->status = "Belum Lulus";
                } else {
                    $student->status = "Lulus";
                }
            }
        }
    
        return view('admin.students.index', [
            'course' => $course,
            'questions' => $questions,
            'students' => $students,
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        //
        $students = $course->students()->orderBy('id', 'DESC')->get();
        return view('admin.students.add_student', [
            'course' => $course,
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        //
        $request->validate([
            'email' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            $error = ValidationException::withMessages([
                'system_error' => ['Email student tidak tersedia!'],
            ]);
            throw $error;
        }

        $isEnrolled = $course->students()->where('user_id', $user->id)->exists();

        if($isEnrolled){
            $error = ValidationException::withMessages([
                'system_error' => ['Student sudah memiliki hak akses kelas!'],
            ]);
            throw $error;
        }

        DB::beginTransaction();
        try{
            $course->students()->attach($user->id);
            DB::commit();
            return redirect()->route('dashboard.course.course_students.index', $course);
        }
        catch(\Exception $e){
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error!'. $e->getMessage()]
            ]); 

            throw $error;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseStudent $courseStudent)
    {
        //
    }
}
