<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
//use DB;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$posts = Post::all();
        //return Post::where('title', 'Post Two')->get();
        //$posts = DB::select('SELECT * FROM posts');
        //$posts = Post::orderBy('title', 'desc')->take(1)->get();
        //$posts = Post::orderBy('title', 'desc')->get();

        $posts = Post::orderBy('created_at', 'desc')->paginate(10);
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required',
        'body' => 'required',
        'cover_image' => 'image|nullable|max:1999'
    ]);

    // Создание директории, если её нет
    if (!Storage::disk('public')->exists('cover_images')) {
        Storage::disk('public')->makeDirectory('cover_images');
    }

    // Обработка загрузки файла
    if ($request->hasFile('cover_image')) {
        // Получаем имя файла с расширением
        $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
        // Получаем только имя файла
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        // Получаем только расширение файла
        $extension = $request->file('cover_image')->getClientOriginalExtension();
        // Имя файла для сохранения
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        // Загружаем изображение
        $path = $request->file('cover_image')->storeAs('cover_images', $fileNameToStore);
    } else {
        // Если изображение не загружено, используем изображение по умолчанию
        $fileNameToStore = 'noimage.jpg';
    }

    // Создаем новый пост
    $post = new Post;
    $post->title = $request->input('title');
    $post->body = $request->input('body');
    $post->user_id = auth()->user()->id;
    $post->cover_image = $fileNameToStore;
    $post->save();

    return redirect('/posts')->with('success', 'Post Created');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return redirect('/posts')->with('error', 'Post not found');
        }

        //Check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }

        return view('posts.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        // Обработка загрузки файла
        if ($request->hasFile('cover_image')) {
            // Получаем имя файла с расширением
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Получаем только имя файла
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Получаем только расширение файла
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Имя файла для сохранения
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            // Log the intended storage path
            \Log::info("Updating image to: public/cover_images/$fileNameToStore");

            // Загружаем изображение
            $path = $request->file('cover_image')->storeAs('cover_images', $fileNameToStore);
        }

        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if ($request->hasFile('cover_image')) {
            $post->cover_image = $fileNameToStore;
        }
        $post->save();

        return redirect('/posts')->with('success', 'Post Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        //Check for correct user
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized Page');
        }
        if($post->cover_image != 'noimage.jpeg'){
            //Delete the image
            Storage::delete('public/cover_images/' . $post->cover_image);
        }
        $post->delete();
        return redirect('/posts')->with('success', 'Post Deleted');
    }
}
