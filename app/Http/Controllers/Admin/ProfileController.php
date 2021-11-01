<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Track;

use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }

    public function create(Request $request)
    {
      // Varidationを行う
      $this->validate($request, Profile::$rules);
      
      $profile = new Profile;
      $form = $request->all();
      
      // フォームから送信されてきた_tokenを削除する
      unset($form['_token']);
      // データベースに保存する
      $profile->fill($form);
      $profile->save();
        return redirect('admin/profile/create');
    }
    
  public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          
          $posts = Profile::where('name', $cond_title)->get();
      } else {
          
          $posts = Profile::all();
      }
      return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }

    public function edit(Request $request)
    {
        
      $profile = Profile::find($request->id);
      if (empty($profile)) {
        abort(404);    
      }
      return view('admin.profile.edit', ['profile_form' => $profile]);
    }

    public function update(Request $request)
  {
      // Validationをかける
      $this->validate($request, Profile::$rules);
      //  Modelからデータを取得する
      $news = Profile::find($request->id);
      // 送信されてきたフォームデータを格納する
      $news_form = $request->all();
      unset($news_form['_token']);
      
      // 該当するデータを上書きして保存する
      $news->fill($news_form)->save();
      $track = new Track();
        $track->profile_id = $news->id;
        $track->edited_at = Carbon::now();
        $track->save();

      return redirect('admin/profile');
  }
  
  public function delete(Request $request)
  {
      // 該当するNews Modelを取得
      $news = Profile::find($request->id);
      // 削除する
      $news->delete();
      return redirect('admin/profile/');
  }
}
