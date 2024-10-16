<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Repository;
use Illuminate\Http\Request;
use Spatie\Valuestore\Valuestore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RepositoriesController extends Controller
{

    // REPO CREATION
    public function create(Request $request)
    {
        $request->validate([
            'alias' => 'required|unique:repositories|max:255',
            'source' => 'required|max:255',
        ]);

        $repo = Repository::create([
            'alias' => $request->input('alias'),
            'source' => $request->input('source')
        ]);


        $schema = Valuestore::make(storage_path('repositories/'.$repo->id.'.json'));
        $schema->flush();

        try {
            $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/tags');
            $tags = json_decode($response->getBody()->getContents());
        } catch (\Throwable $th) {
            $tags = [];
        }

        foreach ($tags as $tag) {
            try {
                $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/contents/composer.json?ref='.$tag->name);
                $content = json_decode($response->getBody()->getContents());
                $composer = json_decode(file_get_contents($content->download_url));
                $composer->sha = $tag->commit->sha;
                $composer->version = $tag->name;
            } catch (\Throwable $th) {
                $composer = null;
            }

            if($composer) {
                $schema->put(
                    [$tag->name => $composer]
                );
            }
        }

        try {
            $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/branches');
            $branches = json_decode($response->getBody()->getContents());
        } catch (\Throwable $th) {
            $branches = [];
        }

        foreach ($branches as $branch) {
            try {
                $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/contents/composer.json?ref='.$branch->name);
                $content = json_decode($response->getBody()->getContents());
                $composer = json_decode(file_get_contents($content->download_url));
                $composer->sha = $branch->commit->sha;
                $composer->version = 'dev-'.$branch->name;
            } catch (\Throwable $th) {
                $composer = null;
            }

            if($composer) {
                $schema->put(
                    ['dev-'.$branch->name => $composer]
                );
            }
        }

        return redirect('/dashboard')->with('success', 'Repository has been added!');
    }

    // REPO UPDATE
    public function update(Request $request)
    {
        if ($request->token != config('makeitprivate.token')) {
            abort(403);
        }

        $repo = Repository::findOrFail($request->id);

        $schema = Valuestore::make(storage_path('repositories/'.$repo->id.'.json'));
        $schema->flush();

        try {
            $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/tags');
            $tags = json_decode($response->getBody()->getContents());
        } catch (\Throwable $th) {
            $tags = [];
        }

        foreach ($tags as $tag) {
            try {
                $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/contents/composer.json?ref='.$tag->name);
                $content = json_decode($response->getBody()->getContents());
                $composer = json_decode(file_get_contents($content->download_url));
                $composer->sha = $tag->commit->sha;
                $composer->version = $tag->name;
            } catch (\Throwable $th) {
                $composer = null;
            }

            if($composer) {
                $schema->put(
                    [$tag->name => $composer]
                );
            }
        }

        try {
            $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/branches');
            $branches = json_decode($response->getBody()->getContents());
        } catch (\Throwable $th) {
            $branches = [];
        }

        foreach ($branches as $branch) {
            try {
                $response = Http::withToken(config('makeitprivate.githubtoken'))->get('https://api.github.com/repos/'.$repo->source.'/contents/composer.json?ref='.$branch->name);
                $content = json_decode($response->getBody()->getContents());
                $composer = json_decode(file_get_contents($content->download_url));
                $composer->sha = $branch->commit->sha;
                $composer->version = 'dev-'.$branch->name;
            } catch (\Throwable $th) {
                $composer = null;
            }

            if($composer) {
                $schema->put(
                    ['dev-'.$branch->name => $composer]
                );
            }
        }

        return 'OK';
    }


    // REPO DELETE
    public function delete(Request $request)
    {
        $repo = Repository::findOrFail($request->id);

        try {
            $schema = Valuestore::make(storage_path('repositories/'.$repo->id.'.json'));
            $schema->flush();
        } catch (\Throwable $th) {
            //
        }

        $repo->delete();

        return redirect('/dashboard')->with('success', 'Repository has been deleted!');
    }

    // REPO SHOW
    public function show()
    {
        $repositories = [
            'packages' => []
        ];

        $repos = Repository::all();

        foreach ($repos as $repo) {
            try {
                $versions = file_get_contents(storage_path('repositories/'.$repo->id.'.json'));
            } catch (\Throwable $th) {
                $versions = '';
            }

            try {
                $versions = json_decode($versions);
            } catch (\Throwable $th) {
                $versions = null;
            }

            if ($versions) {
                foreach ($versions as $version => $composer) {
                    $composer->source = [
                        'reference' => $composer->sha,
                        'type' => 'git',
                        'url' => 'https://github.com/'.$repo->source.'.git'
                    ];
                    $repositories['packages'][$repo->alias][$version] = $composer;
                }
            }
        }

        return response()->json($repositories);
    }
}
