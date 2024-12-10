<?php

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaymentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentCategory::select('*')->where('user_id', auth()->id())->orderBy('id', 'desc')->get();
            return Datatables::of($data)
                ->addColumn('checkbox', '<input type="checkbox" name="payment_category_id[]" value="{{$id}}" />')
                ->addColumn('edit', '<button onclick="editPaymentCategory({{$id}})" class="edit-btn btn btn-info btn-circle"><i class="fas fa-edit"></i></button>')
                ->addColumn('delete', '<a href="{{route("payment_category-delete",["id"=>$id])}}" class="btn btn-danger btn-circle"><i class="fas fa-trash"></i></a>')
                ->rawColumns(['checkbox', 'edit', 'delete'])
                ->make(true);
        }
        return view('payment_category.list');
    }


    public function deleteSelected(Request $request)
    {
        PaymentCategory::whereIn('id', $request->selectedIds)->delete();
        return response()->json(['status' => 'success','message' => 'Selected records deleted successfully']);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'id'=>'required',
                'title'=>'required',
                'status'=>'required',
            ]
        );

        //new record
        if($request->id == "0")
            $payment_category=new PaymentCategory();
        else
            $payment_category=PaymentCategory::findOrFail($request->id);

        $payment_category->title = $request['title'];
        $payment_category->user_id = auth()->id();
        $payment_category->status = $request['status'];
        $payment_category->save();

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $payment_category = PaymentCategory::findOrFail($id);
            return response()->json($payment_category, 200); // Return a JSON response with a 200 status code
        } catch (\Exception $e) {

            $errorMessage = 'Payment Category not found.';
            return response()->json(['error' => $errorMessage], 404); // Return a JSON response with a 404 status code
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentCategory $id)
    {
        $id->delete();
        return redirect()->back(); 
    }
}