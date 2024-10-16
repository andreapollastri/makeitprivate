<div class="p-6 sm:px-20 bg-white border-b border-gray-200">

    <div class="mt-8 text-2xl">
        Private Github Repositories
    </div>

    <div class="mt-6 text-gray-500">
        <form method="post" action="/repo/create" enctype="multipart/form-data">
            @csrf
            Repo Alias <input autocomplete="off" type="text" name="alias" placeholder="vendor/alias" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm ml-2 mr-3"> Github Source <input autocomplete="off" type="text" name="source" placeholder="username/reposity" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm ml-2"> <input class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-3 cursor-pointer" type="submit" value="ADD REPOSITORY TO PROXY">
        </form>
    </div>

    <div class="mt-6 text-gray-500">
        @if(Session::has('success'))
        <div class="text-center py-1 lg:px-4 text-sm">
            <div class="p-2 items-center text-gray-100 leading-none lg:rounded-full flex lg:inline-flex" role="alert">
                <span class="flex rounded-full bg-gray-500 uppercase px-2 py-1 text-xs font-bold mr-3 text-white">Done!</span>
                <span class="font-semibold mr-2 text-left flex-auto">{{ session()->get('success') }}</span>
            </div>
        </div>
        @endif
        @if ($errors->any())
        <div class="text-center py-1 lg:px-4 text-sm">
            @foreach ($errors->all() as $error)
            <div class="p-2 items-center text-gray-100 leading-none lg:rounded-full flex lg:inline-flex" role="alert">
                <span class="flex rounded-full bg-gray-500 uppercase px-2 py-1 text-xs font-bold mr-3 text-white">Error!</span>
                <span class="font-semibold mr-2 text-left flex-auto">{{ $error }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
    @foreach ($repositories as $repository)
    <div class="p-6">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
            </svg>
            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">{{ $repository->alias }}</div>

        </div>

        <div class="ml-12">
            <div class="mt-2 text-sm text-gray-500">
                Source repository:<br>
                <b>https://github.com/{{ $repository->source }}</b>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                Repository webhook:<br>
                <b>{{ config('app.url') }}/repo/update/{{ config('makeitprivate.token') }}/{{ $repository->id }}</b>
            </div>

            <a onclick="return confirm('Are you sure you want to delete from proxy: {{ $repository->alias }}?');" href="/repo/delete/{{ $repository->id }}">
                <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700 mt-8">
                        <div>DELETE FROM PROXY</div>
                </div>
            </a>
        </div>
    </div>
    @endforeach

</div>

<div class="p-6 sm:px-20 bg-white border-b border-gray-200">

    <div class="mt-8 text-2xl">
        Up & Running!
    </div>

    <div class="mt-6 text-gray-500">
        <b>1) Set .env file</b>
        <p>
            Add this parameter to your .env file:
            <code class="text-sm">
                <br>MAKEITPRIVATE_TOKEN="{{ crc32(config('app.key')) }}"
            </code>
        </p>
    </div>

    <div class="mt-6 text-gray-500">
        <b>2) Set Github Token</b>
        <p>Open <a href="/user/profile"><u>User Profile</u></a> page and set your Github Personal Access Token with Repositories granted permission (<a href="https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token" target="_blank"><u>more info</u></a>).</p>
    </div>

    <div class="mt-6 text-gray-500">
        <b>3) Add private repositories</b>
        <p>Use this dashboard to manage your private repositories (such as Packagist).</p>
    </div>

    <div class="mt-6 text-gray-500">
        <b>4) Configure webhooks</b>
        <p>To auto-update your repositories set repositories webhooks (<a href="https://docs.github.com/en/developers/webhooks-and-events/webhooks/about-webhooks" target="_blank"><u>more info</u></a>).</p>
    </div>

    <div class="mt-6 text-gray-500">
        <b>5) Set your project</b>
        <p>
            To use your private repositories into your project, add into it an auth.json file with Github credentials (and .gitignore it!):
            <code class="text-sm">
                <br>{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"http-basic": {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"github.com": {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"username": "YOUR-GITHUB-USERNAME",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"password": "YOUR-GITHUB-PERSONAL-ACCESS-TOKEN"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                }
            </code>
            <br><br>
            The last step is add the proxy configuration into your project composer.json file:
            <code class="text-sm">
                <br>"repositories": [<br>
                &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "composer",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"url": "{{ config('app.url') }}"<br>
                &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                ]
            </code>
        </p>
    </div>
</div>
