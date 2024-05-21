<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
use App\Models\ShippingAddress;

class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(\Auth::user()->can('manage constant tax'))
        {
            $shipping = ShippingAddress::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('shipping_address.index')->with('shipping', $shipping);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(\Auth::user()->can('create constant tax'))
        {
            return view('shipping_address.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create constant tax'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'customer_name' => 'required|max:20',
                                   'phone' => 'required|numeric',
                                   'address' => 'required',
                                   'country' => 'required',
                                   'state' => 'required',
                                   'city' => 'required',
                                   'zip_code' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $shipping             = new ShippingAddress();
            $shipping->customer_name   = $request->customer_name;
            $shipping->phone       = $request->phone;
            $shipping->address       = $request->address;
            $shipping->country       = $request->country;
            $shipping->state       = $request->state;
            $shipping->city       = $request->city;
            $shipping->zip_code       = $request->zip_code;
            $shipping->created_by = \Auth::user()->creatorId();
            $shipping->save();

            return redirect()->route('shipping-address.index')->with('success', __('Shipping Address successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(ShippingAddress $tax)
    {
        return redirect()->route('shipping-address.index');
    }


    public function edit($id)
    {
        $shippingAddress = ShippingAddress::where('id',$id)->first();
       
            if($shippingAddress->created_by == \Auth::user()->creatorId())
            {
                return view('shipping_address.edit', compact('shippingAddress'));
            }
        
    }


    public function update(Request $request, $id)
    {

        $shipping = ShippingAddress::where('id',$id)->first();

        if(\Auth::user()->can('edit constant tax'))
        {
            if($shipping->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                        'customer_name' => 'required|max:20',
                        'phone' => 'required|numeric',
                        'address' => 'required',
                        'country' => 'required',
                        'state' => 'required',
                        'city' => 'required',
                        'zip_code' => 'required|numeric',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

               
            $shipping->customer_name       = $request->customer_name;
            $shipping->phone       = $request->phone;
            $shipping->address       = $request->address;
            $shipping->country       = $request->country;
            $shipping->state       = $request->state;
            $shipping->city       = $request->city;
            $shipping->zip_code       = $request->zip_code;
            $shipping->save();

                return redirect()->route('shipping-address.index')->with('success', __('Shipping Address successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        $shippingAddress = ShippingAddress::where('id',$id)->first();

        if(\Auth::user()->can('delete constant tax'))
        {
            if($shippingAddress->created_by == \Auth::user()->creatorId())
            {
               
                $shippingAddress->delete();

                return redirect()->route('shipping-address.index')->with('success', __('Shipping Address successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
