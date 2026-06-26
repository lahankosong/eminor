<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        // Tahan-banting: kalau tabel articles belum dibuat (mis. fixdb.php belum
        // dijalankan setelah deploy), degrade ke halaman kosong, bukan 500.
        try {
            $articles = Article::orderBy('batch')->orderBy('id')->get();
        } catch (\Throwable $e) {
            $articles = collect();
        }
        $grouped = $articles->groupBy('category');
        return view('library.materi', compact('articles', 'grouped'));
    }

    public function show(string $slug)
    {
        try {
            $article = Article::where('slug', $slug)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Throwable $e) {
            // Tabel belum ada / error DB lain → 404 yang rapi, bukan 500.
            abort(404);
        }
        $prev = Article::where('id', '<', $article->id)->orderByDesc('id')->first();
        $next = Article::where('id', '>', $article->id)->orderBy('id')->first();
        return view('library.artikel', compact('article', 'prev', 'next'));
    }

    public function download(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $content  = "# {$article->title}\n";
        $content .= "Kategori: {$article->category_label} | Waktu baca: {$article->reading_time} menit\n";
        $content .= "Sumber: margonoandi.my.id/library/materi/{$article->slug}\n\n";
        $content .= "---\n\n";
        $content .= $article->content_markdown;

        return response($content, 200, [
            'Content-Type'        => 'text/markdown; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $article->slug . '.md"',
        ]);
    }
}
