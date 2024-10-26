<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $courses = Course::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('course_code', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->orderBy('id', 'DESC')
            ->get();
    
        return view('admin.courses.index', [
            'courses' => $courses,
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();
        return view('admin.courses.create', [
            'categories' => $categories
        ]);

        // return view('admin.courses.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'cover' => 'required|image|mimes:png,jpg,jpeg,svg',
            'course_code' => 'nullable|string|unique:courses,course_code',
        ],
        [
            'course_code.unique' => 'Kode course sudah digunakan!',
        ]
    );
    
        DB::beginTransaction();
    
        try {

            if ($request->hasFile('cover')) {
                $coverPath = $request->file('cover')->store('product_cover', 'public');
                $validated['cover'] = $coverPath;
            }
           
            $validated['slug'] = Str::slug($request->name);
     
            $newCourse = Course::create($validated);
    
            DB::commit();
    
            return redirect()->route('dashboard.courses.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()],
            ]);
    
            throw $error;
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
        $students = $course->students()->orderBy('id', 'DESC')->get();
        $questions = $course->questions()->orderBy('id', 'ASC')->paginate(5);
        return view('admin.courses.manage', [
           'course' => $course, 
           'students' => $students,
           'questions' => $questions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
        $categories = Category::all();
        return view('admin.courses.edit',[
            'course' => $course,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|integer',
        'cover' => 'sometimes|image|mimes:png,jpg,jpeg,svg',
        'course_code' => 'nullable|string|unique:courses,course_code,' . $course->id,
    ], [
        'course_code.unique' => 'Kode course sudah digunakan!',
    ]);

    DB::beginTransaction();

    try {
        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada
            if ($course->cover) {
                Storage::disk('public')->delete($course->cover);
            }

            $coverPath = $request->file('cover')->store('product_cover', 'public');
            $validated['cover'] = $coverPath;
        }

        $validated['slug'] = Str::slug($request->name);

        $course->update($validated);

        DB::commit();

        return redirect()->route('dashboard.courses.index');
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
    public function destroy(Course $course)
    {
        try {
            // Hapus file cover jika ada
            if ($course->cover) {
                Storage::disk('public')->delete($course->cover);
            }
    
            // Hapus data course
            $course->delete();
    
            return redirect()->route('dashboard.courses.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ValidationException::withMessages([
                'system_error' => ['System error! ' . $e->getMessage()]
            ]);
    
            throw $error;
        }
    }
}
