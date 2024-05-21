{{ Form::model($shippingAddress, array('route' => array('shipping-address.update', $shippingAddress->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Customer Name'),['class'=>'form-label']) }}
            {{ Form::text('customer_name', null, array('class' => 'form-control font-style','required'=>'required')) }}
            @error('name')
            <small class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::number('phone', null, array('class' => 'form-control','required'=>'required','step'=>'0.01')) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('Address', __('Address'),['class'=>'form-label']) }}
            {{ Form::text('address', null, array('class' => 'form-control','required'=>'required')) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('city', __('city'),['class'=>'form-label']) }}
            {{ Form::text('city', null, array('class' => 'form-control','required'=>'required')) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('State', __('State'),['class'=>'form-label']) }}
            {{ Form::text('state', null, array('class' => 'form-control','required'=>'required')) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('Country', __('Country'),['class'=>'form-label']) }}
            {{ Form::text('country', null, array('class' => 'form-control','required'=>'required')) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('Zip Code', __('Zip Code'),['class'=>'form-label']) }}
            {{ Form::number('zip_code', null, array('class' => 'form-control','required'=>'required')) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
