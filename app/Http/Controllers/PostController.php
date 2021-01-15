<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $carbon = new Carbon();
       $current = Carbon::now(); 
       $posts= Post::orderBy('id','desc')->paginate(2);
       return view('post.index')->with('posts',$posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories=Category::all();
        return view('post.create')->with('categories',$categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'title'        => 'required|max:255',
            'slug'         => 'required|alpha_dash|max:255|unique:post,slug',
            'body'         => 'required',
            'category_id'  => 'required'
        ));
        $post= new \App\Models\Post();

        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->body = $request->body;
        $post->category_id = $request->category_id ;
        $post->image = $request->input('image');
        if ($request->hasFile('image')) {

         $file=$request->file('image');

            $extension=$file->getClientOriginalExtension();

            $name=time().'.'. $extension;

            $file->move(public_path("images"),$name)  ;

            $post->image=$name;         

        } else {

            return $request;

            $post->image='';

        }
        $post->save();
        $request->session()->flash('success', 'Your post was successfully added :))');
        return redirect()->action([PostController::class, 'show'] , [$post->id]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post= Post::find($id);
        $categories=Category::all();
        return view('post.show')->with('categories',$categories)->with('post',$post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post= Post::find($id);
        $categories=Category::all();
        return view('post.edit')->with('categories',$categories)->with('post',$post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $post= Post::find($id);
        if($request->input('slug')==$post->slug) {
        $this->validate($request, array(
            'title' => 'required|max:255',
            'body'  => 'required' ,
            'category_id'  => 'required')); } 
            else
            {
                $this->validate($request, array(
                    'title' => 'required|max:255',
                    'slug'  => 'required|unique:post,slug',
                    'body'  => 'required' ,
                    'category_id'  => 'required' ));} 
       
        $post= Post::find($id);
        $post->title = $request->input('title');
        $post->slug = $request->input('slug');
        $post->body = $request->input('body');
        $post->category_id = $request->category_id ;
        $post->image = $request->input('image');
        if ($request->hasFile('image')) {

         $file=$request->file('image');

            $extension=$file->getClientOriginalExtension();

            $name=time().'.'. $extension;

            $file->move(public_path("images"),$name)  ;

            $post->image=$name;         

        } else {

            return $request;

            $post->image='';

        }
        $post->save(); 
        $request->session()->flash('success','Your post was successfully updated :))');
        return redirect()->action([PostController::class, 'show'] , [$post->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post= Post::find($id);
        $post->delete();
        session()->flash('success','Your post was successfully deleted :))');
        return redirect()->action([PostController::class, 'index']);
    }
}
