@php
    $settings_data = \App\Models\Utility::settingsById($invoice->created_by);
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{$settings_data['SITE_RTL'] == 'on'?'rtl':''}}"> 

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <title>Email Template</title>

    <style>
        body {
            padding: 0;
            margin: 0;
        }

        .main_parent_div {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
            font-family: Arial, sans-serif;
            padding-top: 30px;
        }

        @media screen and (max-width:717px) {
            .main_parent_div {
                width: fit-content;
            }
        }

        p {
            margin: 0;
            padding: 0;
        }

        .main_div {
            width: 717px;
            border: 1px solid;
            position: relative;
        }

        .upper_div {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            place-items: center;
        }

        .left_side_upper_div {
            display: flex;
            gap: 1rem;
            align-items: center;
            background: black;
            padding: 1rem;
            border-radius: 50px 0 0 50px;
            margin-bottom: 1rem;
        }

        .left_img_upper {
            height: 22rem;
        }

        .qr_code {
            height: 9rem;
        }

        .img_rightside {
            height: 2rem;
        }

        .rightside_div_text {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .p_tag_rightupper {
            font-weight: 500;
            font-variant-caps: titling-caps;
            padding: 0;
            margin: 0;
        }

        .span_text {
            padding: 0;
            margin: 0;
            font-size:smaller;
            font-weight: normal;
            color: rgb(67, 67, 67);
        }

        .p_first_middle {
            padding: 0;
            margin: 0;
            font-size: larger;
        }

        .bold_middle_p {
            padding: 0;
            margin: 0;
            font-weight: bolder;
        }

        .middle_div {
            display: flex;
            padding: 1rem;
        }

        .left_middle {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .right_middle {
            flex: 1;
        }

        .teable_bottom_div {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .last_div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
        }

        .left_last {
            flex: 1;
        }

        .right_last {
            flex: 1;
            position: relative;
        }

        .left_side_bg{
            background: url("images/background.png");
        }

        .footer_bg_div{
            background: url("images/footer_bg1.png");
        }

        .payment_p{
            font-size: small;
        }
    </style>
      @if($settings_data['SITE_RTL']=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
</head>
<!-- id="boxes" -->
<body>
    <div class="main_parent_div" id="boxes">
        <div class=" main_div">

            <div class="upper_div">
                <div>
                    
                    <img src="{{url('/public/images/text_ioready.png')}}" alt="" class=" left_img_upper">
                </div>
                <div>
               
                <!-- <img src="{{url('/images/qr.png')}}" alt="" class="qr_code"> -->
                {!! DNS2D::getBarcodeHTML(route('invoice.link.copy',\Crypt::encrypt($invoice->invoice_id)), "QRCODE",2,2) !!}
                <!-- <img src="{{url('/images/qr.png')}}" alt="" class="qr_code"> -->
                </div>
                <div class=" left_side_bg">
                    <div class="left_side_upper_div">
                        <img src="{{url('/public/images/ioready.png')}}" alt="" class=" img_rightside">
                        <img src="{{url('/public/images/logo.png')}}" alt="" class=" img_rightside">
                    </div>

                    <p style="font-weight: bold; margin: 0;">InvoiceNo:<span class="span_text"> {{Utility::invoiceNumberFormat($settings,$invoice->invoice_id)}}</span></p>
                    <p style="font-weight: bold; margin: 0;">Issue Date:<span class="span_text"> {{Utility::dateFormat($settings,$invoice->issue_date)}}</span></p>
                    <p style="font-weight: bold; margin: 0;">Due Date:<span class="span_text"> {{Utility::dateFormat($settings,$invoice->due_date)}}</span></p>


                    <div class=" rightside_div_text" style="padding-top: 1rem;">
                    @if($settings['shipping_display']=='on')
                        <p style="font-weight: 700;">Ship To</p>
                        @if(!empty($customer->shipping_name))

                        <p class=" p_tag_rightupper">Name: {{!empty($customer->shipping_name)?$customer->shipping_name:''}} </p>
                        <p class="span_text">Address: {{!empty($customer->shipping_address)?$customer->shipping_address:''}}
                            {{!empty($customer->shipping_city)?$customer->shipping_city:'' . ', '}}
                            {{!empty($customer->shipping_state)?$customer->shipping_state:'' .', '}},
                            {{!empty($customer->shipping_zip)?$customer->shipping_zip:''}}
                            {{!empty($customer->shipping_country)?$customer->shipping_country:''}} </p>
                        <!-- <p class="span_text">Email : {{!empty($customer->billing_phone)?$customer->email:''}}</p> -->
                        <p class="span_text">contact:+ {{!empty($customer->shipping_phone)?$customer->shipping_phone:''}}<br></p>
                        @else
                            -
                        @endif
                        @endif
                        <p class=" p_tag_rightupper">Email:info@ioready.com</p>
                        <p class=" p_tag_rightupper">Number:+60147156675</p>
                    </div>
                </div>
            </div>

            <div class=" middle_div">
                <div class=" left_middle">
                    <p class=" p_first_middle">Invoice To:</p>
                    @if(!empty($customer->billing_name))
                    <!-- <p class="bold_middle_p">Body Image Consultants Ltd</p>
                    <p class="span_text">Address: ###########</p>
                    <p class="span_text">Email : ##########</p>
                    <p class="span_text">contact:+#########</p> -->
                    <p class="bold_middle_p">Body Image Consultants Ltd</p>
                    <p class="span_text">Name: {{!empty($customer->billing_name)?$customer->billing_name:''}}</p>
                    <p class="span_text">Address: {{!empty($customer->billing_address)?$customer->billing_address:''}}  {{!empty($customer->billing_city)?$customer->billing_city:'' .', '}} {{!empty($customer->billing_state)?$customer->billing_state:'',', '}},
                            {{!empty($customer->billing_zip)?$customer->billing_zip:''}}  {{!empty($customer->billing_country)?$customer->billing_country:''}}</p>
                    <!-- <p class="span_text">Email : {{!empty($customer->billing_phone)?$customer->email:''}}</p> -->
                    <p class="span_text">contact:+{{!empty($customer->billing_phone)?$customer->billing_phone:''}}</p>
                    <!-- <p>
                            {{!empty($customer->billing_name)?$customer->billing_name:''}}<br>
                            {{!empty($customer->billing_address)?$customer->billing_address:''}}<br>
                            {{!empty($customer->billing_city)?$customer->billing_city:'' .', '}}<br>
                            {{!empty($customer->billing_state)?$customer->billing_state:'',', '}},
                            {{!empty($customer->billing_zip)?$customer->billing_zip:''}}<br>
                            {{!empty($customer->billing_country)?$customer->billing_country:''}}<br>
                            {{!empty($customer->billing_phone)?$customer->billing_phone:''}}<br>
                        </p> -->
                    @else
                        -
                    @endif
                </div>
                <div>
                    
                    <!-- <p class=" p_first_middle">Invoice Status: Sent</p> -->


                @if ($invoice->status == 0)
                <p class=" p_first_middle"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</p>
                @elseif($invoice->status == 1)
                <p class=" p_first_middle"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</p>
                @elseif($invoice->status == 2)
                <p class=" p_first_middle"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</p>
                @elseif($invoice->status == 3)
                <p class=" p_first_middle"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</p>
                @elseif($invoice->status == 4)
                <p class=" p_first_middle"><strong>{{ __('Invoice Status') }} : </strong>{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</p>
                @endif
                </div>
            </div>

            <div style="padding: 0 0.5rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($invoice->itemData) && count($invoice->itemData) > 0)
                @foreach($invoice->itemData as $key => $item)
                        <tr>
                            <td>
                                <p>{{$item->name}}</p>
                                <span class=" span_text"> @php
                    $unitName = App\Models\ProductServiceUnit::find($item->unit);
            @endphp</span>
                            </td>
                            <td>
                            {{$item->quantity}} {{ ($unitName != null) ?  '('. $unitName->name .')' : ''}}
                            </td>
                            <td>{{Utility::priceFormat($settings,$item->price)}}</td>
                            <td>{{($item->discount!=0)?Utility::priceFormat($settings,$item->discount):'-'}}</td>
                            @php
                        $itemtax = 0;
                    @endphp
                            <td> @if(!empty($item->itemTax))

                            @foreach($item->itemTax as $taxes)
                                @php
                                    $itemtax += $taxes['tax_price'];
                                @endphp
                                <p>{{$taxes['name']}} ({{$taxes['rate']}}) {{$taxes['price']}}</p>
                            @endforeach
                            @else
                            <span>-</span>
                            @endif</td>
                            <td>{{Utility::priceFormat($settings,$item->price * $item->quantity -  $item->discount + $itemtax)}}</td>
                            
                        </tr>
                        @endforeach
            @else
            @endif
                    </tbody>
                </table>


            </div>

            <div style="padding: 1rem;">
                <hr />
                <div class=" teable_bottom_div">
                    <p style="font-size: small;">Sub Total</p>
                    <p style="font-size: small;">{{Utility::priceFormat($settings,$invoice->getSubTotal())}}</p>
                </div>
                @if($invoice->getTotalDiscount())


                <div class=" teable_bottom_div">
                    <p style="font-size: small;">{{__('Discount')}}:</p>
                    <p style="font-size: small;">{{Utility::priceFormat($settings,$invoice->getTotalDiscount())}}</p>
                </div>

                @endif
                        @if(!empty($invoice->taxesData))
                            @foreach($invoice->taxesData as $taxName => $taxPrice)

                <div class=" teable_bottom_div">
                    <p style="font-size: small;">{{$taxName}} :</p>
                    <p style="font-size: small;">{{ Utility::priceFormat($settings,$taxPrice)  }}</p>
                </div>
                @endforeach
                    @endif
               
                <div class=" teable_bottom_div">
                    <p style="font-size: small;">Total</p>
                    <p style="font-size: small;">{{Utility::priceFormat($settings,$invoice->getSubTotal()-$invoice->getTotalDiscount()+$invoice->getTotalTax())}}</p>
                </div>
                <div class=" teable_bottom_div">
                    <p style="font-size: small;">Paid</p>
                    <p style="font-size: small;">{{Utility::priceFormat($settings,($invoice->getTotal()-$invoice->getDue())-($invoice->invoiceTotalCreditNote()))}}</p>
                </div>
                <div class=" teable_bottom_div">
                    <p style="font-size: small;">Credit Note</p>
                    <p style="font-size: small;">{{Utility::priceFormat($settings,($invoice->invoiceTotalCreditNote()))}}</p>
                </div>
                <hr />
                <div class=" teable_bottom_div">
                    <p style="font-size: larger;">Due Amount</p>
                    <p style="font-size: larger;">{{Utility::priceFormat($settings,$invoice->getDue())}}</p>
                </div>

                <p style=" font-weight: bold; font-size: larger; text-align: center;">THOUSAND UNITED STATES DOLLAR</p>
            </div>


            <div class="last_div" style="background-color: #32353a;">
                <div class="left_last">
                    <img src="{{url('/public/images/stamp.png')}}" alt="" style="height: 8rem;">
                    <p class="payment_p" style="color: white; padding-left: 15px;">  IOREADY SDN BHD</p>
                    <img src="{{url('/public/images/bg3.png')}}" alt=""  style="position: absolute; left: 0; bottom: 0; height: 2rem;">
                </div>
                <div class="right_last" style="color: white; padding: 0rem 6rem;">
                    <img src="{{url('/public/images/footer_bg1.png')}}" alt="" style="position: absolute; right: 0; height: 5rem;">
                    <p class="payment_p" style="color: yellow;">Payment To Be Transfer:</p>
                    <!-- <p class="payment_p">Transfer Bank :</p> -->
                    
                        @if(!empty($getCompanyPaymentSetting))
                            

                    <p class="payment_p"> {!! $getCompanyPaymentSetting['bank_details'] !!}</p>
                    
                        
                    @endif
                    <p class="payment_p">Currency : {{$settings['site_currency']}}</p>
                    <p class="payment_p">Country:malaysia</p>
                   
                    <img src="{{url('/public/images/bg2.png')}}" alt="" style="position: absolute; bottom: -1rem; right: -1rem; height: 5rem;">

                </div>
            </div>
            <!-- <div class="invoice-footer">
            <b>{{$settings['footer_title']}}</b> <br>
            {!! $settings['footer_notes'] !!}
        </div> -->
        </div>
    </div>
   <!-- @if(!isset($preview))
    @include('invoice.script');
@endif -->
</body>

</html>