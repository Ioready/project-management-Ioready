{{ Form::open(array('url' => 'shipping-address')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Customer Name'),['class'=>'form-label']) }}
            {{ Form::text('customer_name', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter Customer Name'))) }}
            @error('name')
            <small class="invalid-name" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('Phone', __('Phone'),['class'=>'form-label']) }}
            {{ Form::number('phone', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter Phone Number'))) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('address', __('address'),['class'=>'form-label']) }}
            {{ Form::text('address', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter address'))) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('country', __('country'),['class'=>'form-label']) }}
            {{ Form::text('country', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter country'))) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('state', __('state'),['class'=>'form-label']) }}
            {{ Form::text('state', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter state'))) }}
            @error('rate')
            <small class="invalid-rate" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('city', __('city'),['class'=>'form-label']) }}
            {{ Form::text('city', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter city '))) }}
            @error('city')
            <small class="invalid-city" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('zip_code', __('zip_code'),['class'=>'form-label']) }}
            {{ Form::text('zip_code', '', array('class' => 'form-control','required'=>'required' , 'placeholder'=>__('Enter zip_code Number'))) }}
            @error('zip_code')
            <small class="invalid-zip_code" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </small>
            @enderror
        </div>
        
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
{{ Form::close() }}
