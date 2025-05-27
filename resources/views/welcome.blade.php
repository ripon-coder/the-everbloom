<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <div class="flex justify-center items-center h-screen">
        <div
            class="bg-white p-8 border border-gray-300 rounded-lg shadow-md space-y-3 sm:flex sm:justify-center sm:items-center sm:space-x-5">
            <img class="h-24 w-24 mx-auto rounded-full ring-4 ring-blue-600 sm:mx-0 sm:shrink-0 transform-gpu hover:scale-105 duration-500"
                src="{{ asset('bag.jpg') }}" alt="">
            <div class="text-center space-y-3 sm:text-left">
                <p class="font-bold uppercase">This is my youtube</p>
                <button
                    class="border font-bold bg-[#2563EB] text-white text-sm p-1 uppercase rounded-br-md rounded-bl-md hover:bg-blue-800 duration-500">Click
                    Me</button>
            </div>
        </div>
    </div>
</body>

</html>
