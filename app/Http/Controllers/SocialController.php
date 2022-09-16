<?php

namespace App\Http\Controllers;

use App\SocialIcons;
use Illuminate\Http\Request;
use DataTables;

class SocialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $social = \DB::table('social_icons')->select('id','title','url','icon','status');
        if($request->ajax()){
            return DataTables::of($social)
            ->addIndexColumn()
            ->addColumn('icon',function($row){
                if ($row->icon) {
                    $icon = '<img src="' . asset('/images/socialicons/' . $row->icon) . '" alt="Pic" width="50px" class="img-responsive">';
                } else {
                    $icon = '<img  src="' . Avatar::create(ucfirst($row->title))->toBase64() . '" alt="Pic" width="50px" class="img-responsive">';
                }

                return $icon;
            })
            ->addColumn('title',function($row){
                return $row->title;
            })
            ->addColumn('url',function($row){
                $url = '<a title="Go to url" target="_blank" href="'.$row->url.'">'.$row->url.'</a>';
                return $url;
            })
            ->addColumn('status',function($row){
                if($row->status == '1'){
                     $status = '<form method="POST" action="' . route("social.deactive", $row->id) . '">
                                  ' . method_field("put") . '
                                  ' . csrf_field() . '
                                   <input type="submit" class="btn btn-sm btn-success" value="Active">
                                </form>';
                }else{
                    $status = '<form method="POST" action="' . route("social.active", $row->id) . '">
                                  ' . method_field("put") . '
                                  ' . csrf_field() . '
                                   <input type="submit" class="btn btn-sm btn-danger" value="Deactive">
                                </form>';
                }
                return $status;
               
            })
            ->addColumn('action',function($row){
                $btn = '<div class="admin-table-action-block">
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
                        <form method="POST" action="' . route("social.delete", $row->id) . '">
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
            ->rawColumns(['icon','title','url','status','action'])
            ->make(true);
        }

        return view('admin.socialicons.index',compact('social'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.moresettings.socialicons.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



      $social = new SocialIcons;

      $social->title = $request->title;
      $social->url = $request->url;
      
      if(isset($request->status)){
        $social->status = 1;
      }else{
        $social->status = 0;
      }

      if ($file2 = $request->file('icon')) {

          $name2 = $file2->getClientOriginalName();
          $file2->move('images/socialicons/', $name2);
          $social['icon'] = $name2;

      }
      try{
        $social->save();
        return back()->with('added', 'Icon have been added');
      }catch(\Exception $e){
        return back()->with('deleted',$e->getMessage());
      }
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialIcons  $socialIcons
     * @return \Illuminate\Http\Response
     */
    public function show(SocialIcons $socialIcons)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialIcons  $socialIcons
     * @return \Illuminate\Http\Response
     */
    public function edit(SocialIcons $socialIcons)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SocialIcons  $socialIcons
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialIcons $socialIcons)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialIcons  $socialIcons
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $si = SocialIcons::find($id);
        if($si->icon!="")
        {
          unlink(public_path().'/images/socialicons/'.$si->icon);
        }
        try{
            $si->delete();
        return back()->with('deleted', 'Icon has been deleted !');
    }catch(\Exception $e){
        return back()->with('deleted',$e->getMessage());
    }
        
    }

    public function active($id)
    {
        $s = SocialIcons::find($id);
        try{
            $s->status=1;
            $s->save();
            return back()->with('updated', 'Icon status has been changed to Active !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
        }
        
    }

    public function deactive($id)
    {
        $s = SocialIcons::find($id);
        try{
            $s->status=0;
            $s->save();
            return back()->with('updated', 'Icon status has been changed to Deactive !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
        }
        
    }
}
