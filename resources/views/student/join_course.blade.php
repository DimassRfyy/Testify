<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
</head>

<body class="font-poppins text-[#0A090B]">
    <section id="content" class="flex">
        <x-sidebar />
        <div id="menu-content" class="flex flex-col w-full pb-[30px]">
            <x-topbar />
            <div class="flex flex-col gap-10 px-5 mt-5">
                <div class="breadcrumb flex items-center gap-[30px]">
                    <a href="#" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Home</a>
                    <span class="text-[#7F8190] last:text-[#0A090B]">/</span>
                    <a href="" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Manage Courses</a>
                    <span class="text-[#7F8190] last:text-[#0A090B]">/</span>
                    <a href="#" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Course Students</a>
                </div>
            </div>
          

              {{-- Pesan Gagal --}}
                @if($errors->any)
                    <ul>
                        @foreach ($errors->all() as $error )
                            <li class="py-5 px-5 bg-red-700 text-red">
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            {{-- Pesan Gagal --}}

            <form action="{{ route('dashboard.learning.join_course.store') }}" id="add-question" class="mx-[70px] mt-[30px] flex flex-col gap-5" method="POST">
                @csrf
                <h2 class="font-bold text-2xl">Join New Course</h2>
                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Course Code</p>
                    <div
                        class="flex items-center w-[500px] h-[52px] p-[14px_16px] rounded-full border border-[#EEEEEE] focus-within:border-2 focus-within:border-[#0A090B]">
                        <div class="mr-[14px] w-6 h-6 flex items-center justify-center overflow-hidden">
                            <img src="{{asset('/images/icons/note-favorite-outline.svg')}}" class="h-full w-full object-contain" alt="icon">
                        </div>
                        <input type="text"
                            class="font-semibold placeholder:text-[#7F8190] placeholder:font-normal w-full outline-none"
                            placeholder="Write course code" name="course_code">
                    </div>
                </div>
                <button type="submit"
                    class="w-[500px] h-[52px] p-[14px_20px] bg-[#6436F1] rounded-full font-bold text-white transition-all duration-300 hover:shadow-[0_4px_15px_0_#6436F14D] text-center">Join
                    Course</button>
            </form>
        </div>
    </section>

</body>

</html>