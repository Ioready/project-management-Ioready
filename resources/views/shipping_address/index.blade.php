@extends('layouts.admin')
@section('page-title')
    {{__('Manage Shipping Address')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Shipping Address')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        @can('create constant tax')
            <a href="#" data-url="{{ route('shipping-address.create') }}" data-ajax-popup="true" data-title="{{__('Create Shipping Address')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.account_setup')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{__('Name')}}</th>
                                <th> {{__('Phone Number')}}</th>
                                <th> {{__('Address')}}</th>
                                <th> {{__('City')}}</th>
                                <th> {{__('State')}}</th>
                                <th> {{__('Country')}}</th>
                                <th> {{__('Zip_code')}}</th>
                                <th width="10%"> {{__('Action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($shipping as $address)
                                <tr class="font-style">
                                    <td>{{ $address->customer_name }}</td>
                                    <td>{{ $address->phone }}</td>
                                    <td>{{ $address->address }}</td>
                                    <td>{{ $address->city }}</td>
                                    <td>{{ $address->state }}</td>
                                    <td>{{ $address->country }}</td>
                                    <td>{{ $address->zip_code }}</td>
                                    <td class="Action">
                                        <span>
                                        
                                                <div class="action-btn bg-primary ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center" data-url="{{ route('shipping-address.edit',$address->id) }}" data-ajax-popup="true" data-title="{{__('Edit shipping addresse')}}" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                                </div>
                                            
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['shipping-address.destroy', $address->id],'id'=>'delete-form-'.$address->id]) !!}
                                                        <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$address->id}}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                                    {!! Form::close() !!}
                                                </div>
                                            
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
