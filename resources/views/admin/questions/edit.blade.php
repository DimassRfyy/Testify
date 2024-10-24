<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="{{ asset('css/output.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
</head>
<body class="font-poppins text-[#0A090B]">
    <section id="content" class="flex">
        <x-sidebar />
        <div id="menu-content" class="flex flex-col w-full pb-[30px]">
            <x-topbar />
            <div class="flex flex-col gap-10 px-5 mt-5">
                <div class="breadcrumb flex items-center gap-[30px]">
                    <a href="{{ url()->previous() }}" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Home</a>
                    <span class="text-[#7F8190] last:text-[#0A090B]">/</span>
                    <a href="{{ url()->previous() }}" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Manage Courses</a>
                    <span class="text-[#7F8190] last:text-[#0A090B]">/</span>
                    <a href="#" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Course Details</a>
                </div>
            </div>
            <div class="header ml-[70px] pr-[70px] w-[940px] flex items-center justify-between mt-10">
                <div class="flex gap-6 items-center">
                    <div class="w-[150px] h-[150px] flex shrink-0 relative overflow-hidden">
                        <img src="{{Storage::url($course->cover)}}" class="w-full h-full object-contain" alt="icon">
                        <p class="p-[8px_16px] rounded-full bg-[#FFF2E6] font-bold text-sm text-[#F6770B] absolute bottom-0 transform -translate-x-1/2 left-1/2 text-nowrap">{{ $course->category->name }}</p>
                    </div>
                    <div class="flex flex-col gap-5">
                        <h1 class="font-extrabold text-[30px] leading-[45px]">{{ $course->name }}</h1>
                        <div class="flex items-center gap-5">
                            <div class="flex gap-[10px] items-center">
                                <div class="w-6 h-6 flex shrink-0">
                                    <img src="{{asset('/images/icons/calendar-add.svg')}}" alt="icon">
                                </div>
                                <p class="font-semibold">{{ \Carbon\Carbon::parse($course->created_at)->format('F j, Y') }}</p>
                            </div>
                            <div class="flex gap-[10px] items-center">
                                <div class="w-6 h-6 flex shrink-0">
                                    <img src="{{asset('/images/icons/profile-2user-outline.svg')}}" alt="icon">
                                </div>
                                <p class="font-semibold">{{ count($students) }} students</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pesan Gagal --}}
                @if($errors->any)
                    <ul>
                        @foreach ($errors->all() as $error )
                            <li class="py-5 px-5 bg-red-700 text-white">
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            {{-- Pesan Gagal --}}

            <form method="POST" enctype="multipart/form-data" action="{{ route('dashboard.course_questions.update', $courseQuestion) }}" id="add-question" class="mx-[70px] mt-[30px] flex flex-col gap-5">
                @csrf
                @method('PUT')
                <h2 class="font-bold text-2xl">Add New Question</h2>
                <p class="font-semibold">Image Question</p>
                <div class="flex gap-5 items-center">
                    <input type="file" name="questionImage" id="icon" class="peer hidden" onchange="previewFile()" data-empty="true">
                    @if ($courseQuestion->questionImage)
                    <div class="relative w-[100px] h-[100px] rounded-full overflow-hidden">
                        <div class="relative file-preview z-10 w-full h-full">
                            <img src="{{Storage::url($courseQuestion->questionImage)}}" class="thumbnail-icon w-full h-full object-cover">
                        </div>
                        <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 text-center font-semibold text-sm text-[#7F8190]">Optional <br>Image</span>
                    </div>
                    <button type="button" class="flex shrink-0 p-[8px_20px] h-fit items-center rounded-full bg-[#0A090B] font-semibold text-white" onclick="document.getElementById('icon').click()">
                        Edit Image
                    </button>
                    @else
                    <div class="relative w-[100px] h-[100px] rounded-full overflow-hidden peer-data-[empty=true]:border-[3px] peer-data-[empty=true]:border-dashed peer-data-[empty=true]:border-[#EEEEEE]">
                        <div class="relative file-preview z-10 w-full h-full hidden">
                            <img src="" class="thumbnail-icon w-full h-full object-cover" alt="thumbnail">
                        </div>
                        <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 text-center font-semibold text-sm text-[#7F8190]">Optional <br>Image</span>
                    </div>
                    <button type="button" class="flex shrink-0 p-[8px_20px] h-fit items-center rounded-full bg-[#0A090B] font-semibold text-white" onclick="document.getElementById('icon').click()">
                        Add Image
                    </button>
                    @endif
                </div>
                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Question</p>
                    <div class="flex items-center w-[500px] h-[52px] p-[14px_16px] rounded-full border border-[#EEEEEE] focus-within:border-2 focus-within:border-[#0A090B]">
                        <div class="mr-[14px] w-6 h-6 flex items-center justify-center overflow-hidden">
                            <img src="{{asset('/images/icons/note-text.svg')}}" class="h-full w-full object-contain" alt="icon">
                        </div>
                        <input value="{{ $courseQuestion->question }}" type="text" class="font-semibold placeholder:text-[#7F8190] placeholder:font-normal w-full outline-none" placeholder="Write the question" name="question">
                    </div>
                </div>

                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Answers</p>

                    @forelse($courseQuestion->answers as $index => $answer)
                         <div class="flex items-center gap-4">
                        <div class="flex items-center w-[500px] h-[52px] p-[14px_16px] rounded-full border border-[#EEEEEE] focus-within:border-2 focus-within:border-[#0A090B]">
                            <div class="mr-[14px] w-6 h-6 flex items-center justify-center overflow-hidden">
                                <img src="{{asset('/images/icons/edit.svg')}}" class="h-full w-full object-contain" alt="icon">
                            </div>
                            <input type="text" value="{{ $answer->answer }}" class="font-semibold placeholder:text-[#7F8190] placeholder:font-normal w-full outline-none" placeholder="Write better answer option" name="answers[]">
                        </div>
                        <label class="font-semibold flex items-center gap-[10px]"
                            ><input
                            type="radio"
                            value="{{ $index }}"
                            {{ $answer->is_correct ? 'checked' : '' }}
                            name="correct_answer"
                            class="w-[24px] h-[24px] appearance-none checked:border-[3px] checked:border-solid checked:border-white rounded-full checked:bg-[#2B82FE] ring ring-[#EEEEEE]"
                            />
                            Correct
                        </label>
                    </div>
                    @empty
                    @endforelse
                   

                </div>
                <button type="submit" class="w-[500px] h-[52px] p-[14px_20px] bg-[#6436F1] rounded-full font-bold text-white transition-all duration-300 hover:shadow-[0_4px_15px_0_#6436F14D] text-center">Save Question</button>
            </form>
        </div>
    </section>
    <script>
        function previewFile() {
            var preview = document.querySelector('.file-preview');
            var fileInput = document.querySelector('input[type=file]');

            if (fileInput.files.length > 0) {
                var reader = new FileReader();
                var file = fileInput.files[0]; // Get the first file from the input

                reader.onloadend = function () {
                    var img = preview.querySelector('.thumbnail-icon'); // Get the thumbnail image element
                    img.src = reader.result; // Update src attribute with the uploaded file
                    preview.classList.remove('hidden'); // Remove the 'hidden' class to display the preview
                }

                reader.readAsDataURL(file);
                fileInput.setAttribute('data-empty', 'false');
            } else {
                preview.classList.add('hidden'); // Hide preview if no file selected
                fileInput.setAttribute('data-empty', 'true');
            }
        }
    </script>

    <script>
        function handleActiveAnchor(element) {
            event.preventDefault();

            const group = element.getAttribute('data-group');
            
            // Reset all elements' aria-checked to "false" within the same data-group
            const allElements = document.querySelectorAll(`[data-group="${group}"][aria-checked="true"]`);
            allElements.forEach(el => {
                el.setAttribute('aria-checked', 'false');
            });
            
            // Set the clicked element's aria-checked to "true"
            element.setAttribute('aria-checked', 'true');
        }
    </script>
</body>
</html>