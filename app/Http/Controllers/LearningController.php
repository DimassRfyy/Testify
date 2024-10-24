<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use App\Models\CourseQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LearningController extends Controller
{
    public function index(){

        // Get data user
        $user = Auth::user();

        // Get data Kelas
        $my_courses = $user->courses()->with('category')->orderBy('id', 'DESC')->get();

        // nextQuestionId -> untuk kelanjutan pertanyaan jika tidak mau di ulang dari awal (Kendala Internet atau listrik mati)
        foreach ($my_courses as $course) {
            $totalQuestionsCount = $course->questions()->count();

            $answerdQuestionCount = StudentAnswer::where('user_id', $user->id)
            ->whereHas('question', function($query) use ($course){
                $query->where('course_id', $course->id);
            })->distinct()->count('course_question_id');

            if ($answerdQuestionCount < $totalQuestionsCount) {
                $firstUnasnweredQuestion = CourseQuestion::where('course_id', $course->id)
                ->whereNotIn('id', function($query) use ($user){
                    $query->select('course_question_id')->from('student_answers')
                    ->where('user_id', $user->id);
                })->orderBy('id', 'asc')->first();

                $course->nextQuestionId = $firstUnasnweredQuestion ? $firstUnasnweredQuestion->id : null;
            } else {
                $course->nextQuestionId = null;
            }
        }



        return view('student.courses.index', [
            'my_courses' => $my_courses,

        ]);
    }

    public function join_course () {
        return view('student.join_course');
    }

    public function store(Request $request)
{
    // Validasi inputan course code
    $request->validate([
        'course_code' => 'required|string|exists:courses,course_code',
    ]);

    // Cari course berdasarkan course_code yang diinputkan user
    $course = Course::where('course_code', $request->course_code)->first();

    if (!$course) {
        throw ValidationException::withMessages([
            'system_error' => ['Kode course tidak valid!'],
        ]);
    }

    $user = auth()->user(); // Mengambil user yang sedang login

    // Cek apakah user sudah terdaftar di course
    $isEnrolled = $course->students()->where('user_id', $user->id)->exists();

    if ($isEnrolled) {
        throw ValidationException::withMessages([
            'system_error' => ['Anda sudah terdaftar dalam course ini!'],
        ]);
    }

    // Mulai transaksi untuk menambahkan user ke course
    DB::beginTransaction();
    try {
        // Menambahkan user sebagai student ke course
        $course->students()->attach($user->id);
        DB::commit();

        // Redirect ke halaman course students index setelah sukses
        return redirect()->route('dashboard.learning.index');
                        
    } catch (\Exception $e) {
        // Rollback jika terjadi error
        DB::rollBack();
        throw ValidationException::withMessages([
            'system_error' => ['System error: ' . $e->getMessage()],
        ]);
    }
}


    public function learning(Course $course, $question){
        $user = Auth::user();

        $isEnrolled = $user->courses()->where('course_id', $course->id)->exists();

        if (!$isEnrolled) {
            abort(404);
        }

        $currentQuestion = CourseQuestion::where('course_id', $course->id)->where('id',$question)->firstOrFail();

          return view('student.courses.learning', [
            'course' => $course,
            'question' => $currentQuestion,
        ]);
    }

    public function learning_finished(Course $course){
         return view('student.courses.learning_finished', [
            'course' => $course
        ]);
    }

    public function learning_rapport(Course $course)
{
    $userId = Auth::id();

    // Ambil jawaban siswa dengan relasi ke pertanyaan
    $studentAnswers = StudentAnswer::with('question')
        ->whereHas('question', function($query) use ($course) {
            // Filter pertanyaan berdasarkan course
            $query->where('course_id', $course->id);
        })->where('user_id', $userId)->get();

    
    $totalQuestions = CourseQuestion::where('course_id', $course->id)->count();
    $correctAnswersCount = $studentAnswers->where('answer', 'correct')->count();

    // Hitung nilai (skala 0-100)
    $score = ($totalQuestions > 0) ? ($correctAnswersCount / $totalQuestions) * 100 : 0;

    // Tentukan apakah lulus (dengan nilai >= 50 dianggap lulus)
    $passed = $score >= 60;

    return view('student.courses.learning_rapport', [
        'passed' => $passed,
        'course' => $course,
        'studentAnswers' => $studentAnswers,
        'totalQuestions' => $totalQuestions,
        'correctAnswersCount' => $correctAnswersCount,
        'score' => $score,
    ]);
}

}
