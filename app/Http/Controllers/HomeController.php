<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = \Auth::user();
        // ログインしているユーザーを$userに代入している
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        // ASCが昇順DESCが降順
        // Memoモデルからログインしているしているユーザーidと保存されているメモのユーザーidが
        // 合致しているかつstatusが1の値を$memosに代入している
        return view('create', compact('user', 'memos'));
    }

    public function create()
    {
        //ログインしているユーザーの情報をviewにかえす返す
        $user = \Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('create', compact('user', 'memos'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // リクエストの中身を全て取得する
        //  dd($data);
        //$dataの中身を表示して、スクリプトの実行を止めている

        //同じタグがあるか確認する
        $tag_exist = Tag::where('name', $data['tag'])->where('user_id', $data['user_id'])
        ->first();
        //先にタグをインポート
        if (empty($tag_exist['id'])){
            $tag_id = Tag::insertGetId([ 
                'name' => $data['tag'],
                'user_id' => $data['user_id'] 
            ]);
        } else{
            $tag_id = $tag_exist['id'];
        }
        
        //タグIDが判明する
        //タグIDをmemosテーブルに入れる
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

        $memo_id = Memo::insertGetId([
            'content' => $data['content'],
            'user_id' => $data['user_id'], 
            'tag_id' => $tag_id,
            'status' => 1
        ]);
        
        // リダイレクト処理
        return redirect()->route('home');
    }

    public function edit($id){
        // 該当するIDのメモをデータベースから取得
        $user = \Auth::user();
        $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])
          ->first();
        //   dd($memo);
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        $tags = Tag::where('user_id', $user['id'])->get();
        //取得したメモをViewに渡す
        return view('edit', compact('memo', 'user', 'memos', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->all();
        Memo::where('id', $id)
        ->update([
            'content' => $inputs['content'],
            'tag_id' => $inputs['tag_id']
        ]);
        return redirect()->route('home');
    }

    public function delete(Request $request, $id)
    {
        $inputs = $request->all();
        //論理削除
        Memo::where('id', $id)->update(['status' => 2]);
        //物理削除
        Memo::where('id', $id)->delete();
        return redirect()->route('home')->with('success', 'メモの削除が完了しました');
    }
}
