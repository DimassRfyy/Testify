<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CourseQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CourseQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        //
        $students = $course->students()->orderBy('id', 'DESC')->get();
        return view('admin.questions.create', [
            'course' => $course,
            'students' => $students
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answers' => 'required|array',
            'answers.*' => 'required|string',
            'correct_answer' => 'required|integer',
            'questionImage' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp', 
        ]);
    
        DB::beginTransaction();
    
        try {
            
            if ($request->hasFile('questionImage')) {
                $imagePath = $request->file('questionImage')->store('question_images', 'public');
                $validated['questionImage'] = $imagePath;
            }
    
            // Buat pertanyaan baru di tabel course_questions
            $question = $course->questions()->create([
                'question' => $request->question,
                'questionImage' => $validated['questionImage'] ?? null, // Menyimpan path image jika ada
            ]);
    
            // Simpan jawaban dan tanda correct answer
            foreach ($request->answers as $index => $answerText) {
                $isCorrect = ($request->correct_answer == $index);
                $question->answers()->create([
                    'answer' => $answerText,
                    'is_correct' => $isCorrect
                ]);
            }
    
            DB::commit();
    
            return redirect()->route('dashboard.courses.show', $course->id);
    
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()]
            ]);
    
            throw $error;
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(CourseQuestion $courseQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseQuestion $courseQuestion)
    {
        //
        $course = $courseQuestion->course;
        $students = $course->students()->orderBy('id', 'DESC')->get();
        return view('admin.questions.edit', [
            'courseQuestion' => $courseQuestion,
            'course' => $course,
            'students' => $students
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseQuestion $courseQuestion)
{
    $validated = $request->validate([
        'question' => 'required|string|max:255',
        'answers' => 'required|array',
        'answers.*' => 'required|string',
        'correct_answer' => 'required|integer',
        'questionImage' => 'sometimes|nullable|image|mimes:png,jpg,jpeg,svg,webp', 
    ]);

    DB::beginTransaction();

    try {
        // Jika ada file gambar yang diupload, simpan file baru dan hapus gambar lama jika ada
        if ($request->hasFile('questionImage')) {
            // Hapus gambar lama jika ada
            if ($courseQuestion->questionImage) {
                Storage::delete('public/' . $courseQuestion->questionImage);
            }

            // Simpan gambar baru
            $imagePath = $request->file('questionImage')->store('question_images', 'public');
            $validated['questionImage'] = $imagePath;
        }

        // Perbarui pertanyaan dan gambar (jika ada)
        $courseQuestion->update([
            'question' => $validated['question'],
            'questionImage' => $validated['questionImage'] ?? $courseQuestion->questionImage, // Simpan gambar jika ada, atau gunakan gambar lama
        ]);

        // Hapus semua jawaban lama
        $courseQuestion->answers()->delete();

        // Simpan jawaban baru dengan tanda correct answer
        foreach ($request->answers as $index => $answerText) {
            $isCorrect = ($request->correct_answer == $index);
            $courseQuestion->answers()->create([
                'answer' => $answerText,
                'is_correct' => $isCorrect
            ]);
        }

        DB::commit();

        // Redirect setelah update berhasil
        return redirect()->route('dashboard.courses.show', $courseQuestion->course_id);

    } catch (\Exception $e) {
        DB::rollBack();
        $error = ValidationException::withMessages([
            'system_error' => ['System error! ' . $e->getMessage()]
        ]);

        throw $error;
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseQuestion $courseQuestion)
    {
        try{
            $courseQuestion->delete();
            return redirect()->route('dashboard.courses.show', $courseQuestion->course_id);
        }
        catch(\Exception $e){
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error!'. $e->getMessage()]
            ]); 

            throw $error;
        }
    }
}
