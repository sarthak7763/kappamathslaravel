<?php

namespace App\Http\Controllers;

use App\FAQ;
use Illuminate\Http\Request;
use DataTables;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$faqs = FAQ::all();
        $faqs = \DB::table('faq')->select('id', 'title', 'details')->get();
          if ($request->ajax()) {
                return DataTables::of($faqs)
                ->addIndexColumn()
                 ->addColumn('title', function ($row) {
                    return strip_tags(html_entity_decode(str_limit($row->title, 30)));

                })
                  ->addColumn('details', function ($row) {
                    return str_limit($row->details, 300);

                })
                ->addColumn('action', function ($row) {
                    $btn = ' <div class="admin-table-action-block">

                    <a href="' . route('faq.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
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
                      <form method="POST" action="' . route("faq.delete", $row->id) . '">
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

                ->rawColumns(['title', 'details', 'action'])
                ->make(true);
            }
        return view('admin.faq.index',compact('faqs'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.faq.add');
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
            'title' => 'required'
        )); 

        $newfaq = new FAQ;
        try {
           $newfaq->title = $request->title;
            $newfaq->details = $request->details;

            $newfaq->save();

            return redirect()->route('faq.index')->with('added','FAQ Added'); 
        } catch (\Exception $e) {
            return back()->with('deleted',$e->getMessage());
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FAQ  $fAQ
     * @return \Illuminate\Http\Response
     */
    public function show(FAQ $fAQ)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FAQ  $fAQ
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $faq = FAQ::findOrFail($id);
      return view('admin.faq.edit',compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FAQ  $fAQ
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
         $faq = FAQ::find($id);
         try{
            $faq->title = $request->title;
            $faq->details = $request->details;
            $faq->save();

            return redirect()->route('faq.index')->with('updated','FAQ is updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FAQ  $fAQ
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = FAQ::find($id);
        try{
            $faq->delete();
            return redirect()->route('faq.index')->with('deleted','FAQ has been deleted');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
        }
        
    }
}
