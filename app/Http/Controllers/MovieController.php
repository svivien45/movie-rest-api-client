<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class MovieController extends Controller
{
    /**
     * Lista megjelenítése
     */
   public function index(Request $request)
{
    try {
        $response = Http::api()->get('/movies');

        if ($response->failed()) {
            $message = $response->json('message') ?? 'Nem sikerült lekérdezni a filmeket.';
            return back()->with('error', $message);
        }

        $data = $response->json(); // Ez tartalmazza a "movies" kulcsot
        $movies = $data['movies'] ?? []; // Kivesszük a tömböt

        return Inertia::render('Movies/Index', [
            'movies' => $movies, // most már csak a tömb megy a React komponensnek
            'isAuthenticated' => $this->isAuthenticated()
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Nem sikerült kommunikálni az API-val: ' . $e->getMessage());
    }
}


    /**
     * Egy film adatainak megjelenítése
     */
    public function show($id)
    {
        try {
            $response = Http::api()
                ->withToken(session('api_token'))
                ->get("/movies/$id");

            if ($response->failed()) {
                return back()->with('error', 'A film nem található.');
            }

            $movie = $response->json();

            return Inertia::render('Movies/Show', [
                'movie' => $movie
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    /**
     * Új film létrehozása (Űrlap)
     */
    public function create()
    {
        return Inertia::render('Movies/Create', [
            'isAuthenticated' => session()->has('api_token')
        ]);
    }

    /**
     * Új film mentése
     */
    public function store(Request $request)
    {
        try {
            $response = Http::api()
                ->withToken(session('api_token'))
                ->post('/movies', [
                    'title'       => $request->title,
                    'year'        => $request->year,
                    'description' => $request->description
                ]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a filmet.';
                return back()->with('error', $message);
            }

            return redirect()->route('movies.index')->with('success', 'Film létrehozva!');

        } catch (\Exception $e) {
            return back()->with('error', 'Kommunikációs hiba: ' . $e->getMessage());
        }
    }

    /**
     * Film szerkesztő nézet
     */
    public function edit($id)
    {
        try {
            $response = Http::api()
                ->withToken(session('api_token'))
                ->get("/movies/$id");

            if ($response->failed()) {
                return back()->with('error', 'A film nem található.');
            }

            return Inertia::render('Movies/Edit', [
                'movie' => $response->json(),
                'isAuthenticated' => session()->has('api_token')
            ]);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Film frissítése
     */
    public function update(Request $request, $id)
    {
        try {
            $response = Http::api()
                ->withToken(session('api_token'))
                ->put("/movies/$id", [
                    'title'       => $request->title,
                    'year'        => $request->year,
                    'description' => $request->description
                ]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült módosítani a filmet.';
                return back()->with('error', $message);
            }

            return redirect()->route('movies.index')->with('success', 'Film frissítve!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Film törlése
     */
    public function destroy($id)
    {
        try {
            $response = Http::api()
                ->withToken(session('api_token'))
                ->delete("/movies/$id");

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült törölni a filmet.');
            }

            return redirect()->route('movies.index')->with('success', 'Film törölve!');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}
