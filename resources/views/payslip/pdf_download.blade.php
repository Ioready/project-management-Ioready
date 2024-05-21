
<style>
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

.table tbody + tbody {
    border-top: 2px solid #dee2e6;
}

.table .table {
    background-color: #fff;
}

.text-end {
    text-align: right !important;
}

.font-weight-bold {
    font-weight: 700 !important;
}
.watermark {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1; /* Ensure it's behind the content */
    }

    a {
		background: #ffffff;
		border: solid 1px #e6e6e6;
		border-radius: 2px;
		display: inline-block;
		line-height: 100px;
		margin: 5px;
		position: relative;
		text-align: center;
		vertical-align: middle;
		width: 100px;
}

a span {
		background: #f2594b;
		border-radius: 4px;
		color: #ffffff;
		display: inline-block;
		font-size: 11px;
		font-weight: 700;
		line-height: normal;
		padding: 5px 10px;
		position: relative;
		text-transform: uppercase;
		z-index: 1;
}

a span:last-child {
		margin-left: -20px;
}

a:before,
a:after {
		background: #ffffff;
		border: solid 3px #9fb4cc;
		border-radius: 4px;
		content: '';
		display: block;
		height: 35px;
		left: 50%;
		margin: -17px 0 0 -12px;
		position: absolute;
		top: 50%;
		/*transform:translate(-50%,-50%);*/
		
		width: 25px;
}

a:hover:before,
a:hover:after {
		background: #e2e8f0;
}
/*a:before{transform:translate(-30%,-60%);}*/

a:before {
		margin: -23px 0 0 -5px;
}

a:hover {
		background: #e2e8f0;
		border-color: #9fb4cc;
}

a:active {
		background: #dae0e8;
		box-shadow: inset 0 2px 2px rgba(0, 0, 0, .25);
}

a span:first-child {
		display: none;
}

a:hover span:first-child {
		display: inline-block;
}

a:hover span:last-child {
		display: none;
}

</style>

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    var filename = 'Payslip.pdf'; // Define a default filename if input is not available

    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: [0.5, 0.5, 0.5, 0.5], // top, left, bottom, right
            filename: filename,
            image: { type: 'jpeg,jpg,png', quality: 1 },
            html2canvas: { scale: 2, dpi: 300, letterRendering: true },
            jsPDF: { unit: 'in', format: 'A4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>



@php
    $logo=\App\Models\Utility::get_file('uploads/logo');
    $company_logo = \App\Models\Utility::GetLogo();
@endphp
<div class="main-content">
    <div class="card bg-none card-box">
        <div class="card-body">
        <div class="text-end">
            <!-- <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()">
                <span class="ti ti-download"></span> Download
            </a> -->
            <a href="#" class="btn btn-sm btn-primary" onclick="saveAsPDF()"><span>Download</span><span>PDF</span></a>
        </div>
            <div class="invoice" id="printableArea">
                <div class="invoice-number">
                <img src="{{url('/public/images/logo-dark.png')}}" alt="" class=" left_img_upper" width="120px;">
                    <!-- <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png')}}" width="120px;"> -->
                </div>
                <div class="invoice-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="invoice-title"></div>
                            <hr>
                            <div class="row text-sm">
                                <div class="col-md-6">
                                    <address>
                                        <strong>{{ __('Name') }} :</strong> {{$employee->name}}<br>
                                        <strong>{{ __('Position') }} :</strong> {{ __('Employee') }}<br>
                                        <strong>{{ __('Salary Date') }} :</strong> {{ \Carbon\Carbon::parse($payslip->created_at)->format('d/m/Y') }}<br>
                                    </address>
                                </div>
                                <div class="col-md-6 text-end">
                                    <address>
                                        <strong>{{ \Utility::getValByName('company_name') }}</strong><br>
                                        {{ \Utility::getValByName('company_address') }} , {{ \Utility::getValByName('company_city') }},<br>
                                        {{ \Utility::getValByName('company_state') }}-{{ \Utility::getValByName('company_zipcode') }}<br>
                                        <strong>{{ __('Salary Slip') }} :</strong> {{ $payslip->salary_month }}<br>
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table table-md">
                                        <thead>
                                            <tr class="font-weight-bold">
                                                <th>{{ __('Earning') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th class="text-end">{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ __('Basic Salary') }}</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td class="text-end">{{ number_format($payslip->basic_salary, 2) }}</td>
                                            </tr>
                                            @foreach ($payslipDetail['earning']['allowance'] as $allowance)
                                                @php
                                                    $allowance = json_decode($allowance->allowance);
                                                @endphp
                                                @foreach ($allowance as $all)
                                                    <tr>
                                                        <td>{{ __('Allowance') }}</td>
                                                        <td>{{ $all->title }}</td>
                                                        <td>{{ ucfirst($all->type) }}</td>
                                                        <td class="text-end">
                                                            {{ \Auth::user()->salaryPriceFormat($all->amount) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <!-- Repeat similar blocks for 'commission', 'otherPayment', and 'overTime' -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-md">
                                        <thead>
                                            <tr class="font-weight-bold">
                                                <th>{{ __('Deduction') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th class="text-end">{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payslipDetail['deduction']['loan'] as $loan)
                                                @php
                                                    $loans = json_decode($loan->loan);
                                                @endphp
                                                @foreach ($loans as $emploanss)
                                                    <tr>
                                                        <td>{{ __('Loan') }}</td>
                                                        <td>{{ $emploanss->title }}</td>
                                                        <td>{{ ucfirst($emploanss->type) }}</td>
                                                        <td class="text-end">
                                                            {{ \Auth::user()->salaryPriceFormat($emploanss->amount) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <!-- Repeat similar blocks for 'deduction' -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-8"></div>
                                <div class="col-lg-4 text-end text-sm">
                                    <div class="invoice-detail-item pb-2">
                                        <div class="invoice-detail-name font-bold">{{ __('Total Earning') }}</div>
                                        <div class="invoice-detail-value">{{ number_format($payslipDetail['totalEarning'], 2) }}</div>
                                    </div>
                                    <div class="invoice-detail-item">
                                        <div class="invoice-detail-name font-bold">{{ __('Total Deduction') }}</div>
                                        <div class="invoice-detail-value">{{ number_format($payslipDetail['totalDeduction'], 2) }}</div>
                                    </div>
                                    <hr class="mt-2 mb-2">
                                    <div class="invoice-detail-item">
                                        <div class="invoice-detail-name font-bold">{{ __('Net Salary') }}</div>
                                        <div class="invoice-detail-value invoice-detail-value-lg">{{ number_format($payslip->net_payble, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="text-md-right pb-2 text-sm">
                                <div class="last_div">
                                    <div class="left_last">
                                        <p class="payment_p">{{ __('Paid By') }} IOREADY SDN BHD</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
