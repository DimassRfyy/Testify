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
                    <a href="{{ url()->previous() }}" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold">Manage Category</a>
                    <span class="text-[#7F8190] last:text-[#0A090B]">/</span>
                    <a href="#" class="text-[#7F8190] last:text-[#0A090B] last:font-semibold ">Edit Category</a>
                </div>
            </div>
            <div class="header flex flex-col gap-1 px-5 mt-5">
                <h1 class="font-extrabold text-[30px] leading-[45px]">Edit Category</h1>
                <p class="text-[#7F8190]">Provide high quality for best students</p>
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

            {{-- Form Product --}}
            <form method="POST" enctype="multipart/form-data" action="{{ route('dashboard.categories.update', $category) }}" class="flex flex-col gap-[30px] w-[500px] mx-[70px] mt-10">
                @csrf
                @method('PUT')
                
                <div class="flex flex-col gap-[10px]">
                    <p class="font-semibold">Category Name</p>
                    <div class="flex items-center w-[500px] h-[52px] p-[14px_16px] rounded-full border border-[#EEEEEE] transition-all duration-300 focus-within:border-2 focus-within:border-[#0A090B]">
                        <div class="mr-[14px] w-6 h-6 flex items-center justify-center overflow-hidden">
                            <img src="{{asset('/images/icons/note-favorite-outline.svg')}}" class="w-full h-full object-contain" alt="icon">
                        </div>
                        <input value="{{ $category->name }}" type="text" class="font-semibold placeholder:text-[#7F8190] placeholder:font-normal w-full outline-none" placeholder="Write your better category name" name="name" required>
                    </div>
                </div>
               
                <label class="font-semibold flex items-center gap-[10px]"
                    ><input
                    type="radio"
                    name="tnc"
                    class="w-[24px] h-[24px] appearance-none checked:border-[3px] checked:border-solid checked:border-white rounded-full checked:bg-[#2B82FE] ring ring-[#EEEEEE]"
                    checked/>
                    I have read terms and conditions
                </label>
                <div class="flex items-center gap-5">
                    <a href="" class="w-full h-[52px] p-[14px_20px] bg-[#0A090B] rounded-full font-semibold text-white transition-all duration-300 text-center">Add to Draft</a>
                    <button type="submit" class="w-full h-[52px] p-[14px_20px] bg-[#6436F1] rounded-full font-bold text-white transition-all duration-300 hover:shadow-[0_4px_15px_0_#6436F14D] text-center">Save Category</button>
                </div>
            </form>
            {{-- Form Product --}}
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