<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use DataTables;
// use App\SocialIcons;
class PagesController extends Controller
{
    public function index(Request $request)
    {
      
      $pages = \DB::table('pages')->select('id','name','slug','status');
      if($request->ajax()){

        return DataTables::of($pages)
        ->addIndexColumn()
        ->addColumn('name',function($row){
            return $row->name;
        })
        ->addColumn('url',function($row){
           $url = '<a target="_blank" href="' . route('page.show', $row->slug) . '">'. $row->slug .'</a>';
           return $url;
        })
        ->addColumn('status', function($row){
           
            return isset($row->status) && $row->status == '1' ? 'Active' : 'Deactive';
        })
        ->addColumn('action', function($row){
          $btn = '<div class="admin-table-action-block">

                    <a href="' . route('pages.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal' . $row->id . '"><i class="fa fa-trash"></i> </button></div>';

                  $btn .= '<div id="deleteModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="delete-icon"></div>
                      </div>
                      <div class="modal-body text-center">
                        <h4 class="modal-heading">Are You Sure ?</h4>
                        <p>Do you really want to delete these records? This process cannot be undone.</p>
                      </div>
                      <div class="modal-footer">
                        <form method="POST" action="' . route("pages.delete", $row->id) . '">
                          ' . method_field("DELETE") . '
                          ' . csrf_field() . '
                            <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Yes</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>';
              return $btn;
        })
        ->rawColumns(['name','url','status','action'])
        ->make(true);

      }
      return view('admin.pages.index',compact('pages'));
    }

    public function add()
    {
      return view('admin.pages.add');

    }

    public function store(Request $request)
    {


      $newpage = new Page;

      $newpage->name = $request->name;
      $newpage->slug = str_slug($request->name);

      if(isset($request->show_in_menu)){
        $newpage->show_in_menu = 1;
      }else{
        $newpage->show_in_menu = 0;
      }
      
      if(isset($request->status))
      {
        $newpage->status = "1";
      }else{
        $newpage->status = "0";
      }

      $newpage->details = $request->details;
      try{
          $newpage->save();

        return redirect()->route('pages.index')->with('added', 'Page is created !');
      }catch(\Exception $e){
          return back()->with('deleted',$e->getMessage());
      }
      

    }

    public function edit($id)
    {
      $page = Page::findOrFail($id);
      return view('admin.pages.edit',compact('page'));
    }

    public function show($slug)
    {
        $page = Page::where('slug', '=', $slug)->first();
        $menus  = Page::where('show_in_menu','=',1)->get();
        // $si = SocialIcons::all();
        return view('admin.pages.show',compact('page','menus'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::find($id);
        $page->name = $request->name;
        $page->slug = str_slug($request->name);
        $page->details = $request->details;
        if(isset($request->status)){
        
          $page->status = "1";

        }else{

          $page->status = "0";

        }

        if(isset($request->show_in_menu)){
        $page->show_in_menu = 1;
        }else{
          $page->show_in_menu = 0;
        }
        try{
           $page->save();

          return redirect()->route('pages.index')->with('updated','Page is updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
        }
       
    }

    public function destroy($id)
    {
      $page = Page::find($id);
      try{
        $page->delete();
        return redirect()->route('pages.index')->with('deleted','Page has been deleted');
      }catch(\Exception $e){
        return back()->with('deleted',$e->getMessage());
      }
      
    }
}
