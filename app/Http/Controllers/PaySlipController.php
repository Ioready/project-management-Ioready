<?php

namespace App\Http\Controllers;

use App\Exports\PayslipExport;
use App\Models\Allowance;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\Resignation;
use App\Models\PaySlip;
use App\Models\SaturationDeduction;
use App\Models\Utility;
use App\Models\Termination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;


use PDF;
use Illuminate\Support\Facades\Storage;


class PaySlipController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage pay slip') || \Auth::user()->type != 'client' || \Auth::user()->type != 'company')
        {
            $employees = Employee::where(
                [
                    'created_by' => \Auth::user()->creatorId(),
                ]
            )->first();

            $month = [
                '01' => 'JAN',
                '02' => 'FEB',
                '03' => 'MAR',
                '04' => 'APR',
                '05' => 'MAY',
                '06' => 'JUN',
                '07' => 'JUL',
                '08' => 'AUG',
                '09' => 'SEP',
                '10' => 'OCT',
                '11' => 'NOV',
                '12' => 'DEC',
            ];

            $year = [

                '2023' => '2023',
                '2024' => '2024',
                '2025' => '2025',
                '2026' => '2026',
                '2027' => '2027',
                '2028' => '2028',
                '2029' => '2029',
                '2030' => '2030',
            ];

            return view('payslip.index', compact('employees', 'month', 'year'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'month' => 'required',
                               'year' => 'required',

                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $month = $request->month;
        $year  = $request->year;


        $formate_month_year = $year . '-' . $month;
        $validatePaysilp    = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->pluck('employee_id');
        $payslip_employee   = Employee::where('created_by', \Auth::user()->creatorId())->where('company_doj', '<=', date($year . '-' . $month . '-t'))->count();

        $resignation = Resignation::where('created_by' , \Auth::user()->creatorId())->where('resignation_date', '<=' , date('Y-m-d'))->pluck('employee_id')->toArray();
        
        $termination = Termination::where('created_by' , \Auth::user()->creatorId())->where('termination_date', '<=' , date('Y-m-d'))->pluck('employee_id')->toArray();

        if($payslip_employee > count($validatePaysilp))
        {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->where('company_doj', '<=', date($year . '-' . $month . '-t'))->whereNotIn('employee_id', $validatePaysilp)->whereNotIn('id', $resignation)->whereNotIn('id', $termination)->get();

            $employeesSalary = Employee::where('created_by', \Auth::user()->creatorId())->where('salary', '<=', 0)->first();

            if(!empty($employeesSalary))
            {
                return redirect()->route('payslip.index')->with('error', __('Please set employee salary.'));
            }

            foreach($employees as $employee)
            {

                $payslipEmployee                       = new PaySlip();
                $payslipEmployee->employee_id          = $employee->id;
                $payslipEmployee->net_payble           = $employee->get_net_salary();
                $payslipEmployee->salary_month         = $formate_month_year;
                $payslipEmployee->status               = 0;
                $payslipEmployee->basic_salary         = !empty($employee->salary) ? $employee->salary : 0;
                $payslipEmployee->allowance            = Employee::allowance($employee->id);
                $payslipEmployee->commission           = Employee::commission($employee->id);
                $payslipEmployee->loan                 = Employee::loan($employee->id);
                $payslipEmployee->saturation_deduction = Employee::saturation_deduction($employee->id);
                $payslipEmployee->other_payment        = Employee::other_payment($employee->id);
                $payslipEmployee->overtime             = Employee::overtime($employee->id);
                $payslipEmployee->created_by           = \Auth::user()->creatorId();
                $payslipEmployee->save();

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $payslipNotificationArr = [
                    'year' =>  $formate_month_year,
                ];
                //Slack Notification
                if(isset($setting['payslip_notification']) && $setting['payslip_notification'] ==1)
                {
                    Utility::send_slack_msg('new_monthly_payslip', $payslipNotificationArr);
                }

                //Telegram Notification
                if(isset($setting['telegram_payslip_notification']) && $setting['telegram_payslip_notification'] ==1)
                {
                    Utility::send_telegram_msg('new_monthly_payslip', $payslipNotificationArr);
                }

                //webhook
                $module ='New Monthly Payslip';
                $webhook=  Utility::webhookSetting($module);
                if($webhook)
                {
                    $parameter = json_encode($payslipEmployee);
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                    if($status == true)
                    {
                        return redirect()->back()->with('success', __('Payslip successfully created.'));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }

            }

            return redirect()->route('payslip.index')->with('success', __('Payslip successfully created.'));
        }
        else
        {
            return redirect()->route('payslip.index')->with('error', __('Payslip Already created.'));
        }

    }

    public function destroy($id)
    {
        $payslip = PaySlip::find($id);
        $payslip->delete();

        return true;
    }

    public function showemployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.show', compact('payslip'));
    }


    public function search_json(Request $request)
    {

        $formate_month_year = $request->datePicker;
        $validatePaysilp    = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->get()->toarray();

        $data=[];
        if (empty($validatePaysilp))
        {
            $data=[];
            return;
        } else {
            $paylip_employee = PaySlip::select(
                [
                    'employees.id',
                    'employees.employee_id',
                    'employees.name',
                    'payslip_types.name as payroll_type',
                    'pay_slips.basic_salary',
                    'pay_slips.net_payble',
                    'pay_slips.id as pay_slip_id',
                    'pay_slips.status',
                    'employees.user_id',
                ]
            )->leftjoin(
                'employees',
                function ($join) use ($formate_month_year) {
                    $join->on('employees.id', '=', 'pay_slips.employee_id');
                    $join->on('pay_slips.salary_month', '=', \DB::raw("'" . $formate_month_year . "'"));
                    $join->leftjoin('payslip_types', 'payslip_types.id', '=', 'employees.salary_type');
                }
            )->where('employees.created_by', \Auth::user()->creatorId())->get();


            foreach ($paylip_employee as $employee) {

                if (Auth::user()->type == 'Employee') {
                    if (Auth::user()->id == $employee->user_id) {
                        $tmp   = [];
                        $tmp[] = $employee->id;
                        $tmp[] = $employee->name;
                        $tmp[] = $employee->payroll_type;
                        $tmp[] = $employee->pay_slip_id;
                        $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->salaryPriceFormat($employee->basic_salary) : '-';
                        $tmp[] = !empty($employee->net_payble) ? \Auth::user()->salaryPriceFormat($employee->net_payble) : '-';
                        
                        if ($employee->status == 1) {
                            $tmp[] = 'paid';
                        } else {
                            $tmp[] = 'unpaid';
                        }
                        $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                        $tmp['url']  = route('employee.show', Crypt::encrypt($employee->id));
                        $data[] = $tmp;
                    }
                } else {

                    $tmp   = [];
                    $tmp[] = $employee->id;
                    $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
                    $tmp[] = $employee->name;
                    $tmp[] = $employee->payroll_type;
                    $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->salaryPriceFormat($employee->basic_salary) : '-';
                    $tmp[] = !empty($employee->net_payble) ? \Auth::user()->salaryPriceFormat($employee->net_payble) : '-';
                    
                    if ($employee->status == 1) {
                        $tmp[] = 'Paid';
                    } else {
                        $tmp[] = 'UnPaid';
                    }
                    $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                    $tmp['url']  = route('employee.show', Crypt::encrypt($employee->id));
                    $data[] = $tmp;
                }
            }
           
            return $data;
        }
    }

    public function paysalary($id, $date)
    {
        $employeePayslip = PaySlip::where('employee_id', '=', $id)->where('created_by', \Auth::user()->creatorId())->where('salary_month', '=', $date)->first();

        $account = Employee::find($id);
        Utility::bankAccountBalance($account->account, $employeePayslip->net_payble, 'debit');

        if(!empty($employeePayslip))
        {
            $employeePayslip->status = 1;
            $employeePayslip->save();

            return redirect()->route('payslip.index')->with('success', __('Payslip Payment successfully.'));
        }
        else
        {
            return redirect()->route('payslip.index')->with('error', __('Payslip Payment failed.'));
        }

    }

    public function bulk_pay_create($date)
    {
        $Employees       = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->get();
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        return view('payslip.bulkcreate', compact('Employees', 'unpaidEmployees', 'date'));
    }

    public function bulkpayment(Request $request, $date)
    {
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        foreach($unpaidEmployees as $employee)
        {
            $employee->status = 1;
            $employee->save();
        }

        return redirect()->route('payslip.index')->with('success', __('Payslip Bulk Payment successfully.'));
    }

    public function employeepayslip()
    {
        $employees = Employee::where(
            [
                'user_id' => \Auth::user()->id,
            ]
        )->first();

        $payslip = PaySlip::where('employee_id', '=', $employees->id)->get();

        return view('payslip.employeepayslip', compact('payslip'));

    }

    public function pdf($id, $month)
    {
        $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);
        $payslipDetail = Utility::employeePayslipDetail($id,$month);


        return view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail'));
    }

    public function pdfDownload($id, $month)
{
    $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->first();
    // $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
    $employee = Employee::find($payslip->employee_id);
    $payslipDetail = Utility::employeePayslipDetail($id,$month);

    // return view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail'));
    return view('payslip.pdf_download', compact('payslip', 'employee', 'payslipDetail'));
}

//     public function send($id, $month)
//     {
//         $setings = Utility::settings();
//         if($setings['payslip_sent'] == 1)
//         {
//             $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
//             $employee = Employee::find($payslip->employee_id);

//             $payslip->name  = $employee->name;
//             $payslip->email = $employee->email;

//             $payslipId    = Crypt::encrypt($payslip->id);
//             $payslip->url = route('payslip.payslipPdf', $payslipId);
// //            dd($payslip->url);

//         $month =$payslip->salary_month;
        
//         $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id, $month);



//         $pdf = ' <div class="invoice" id="printableArea">
//         <div class="invoice-number">
//             <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo-dark.png')}}" width="120px;">
//         </div>
//         <div class="invoice-print">
//             <div class="row">
//                 <div class="col-lg-12">
//                     <div class="invoice-title">
//                     </div>
//                     <hr>
//                     <div class="row text-sm">
//                         <div class="col-md-6">
//                             <address>
//                                 <strong>{{__('Name')}} :</strong> {{$employee->name}}<br>
//                                 <strong>{{__('Position')}} :</strong> {{__('Employee')}}<br>
//                                 <strong>{{__('Salary Date')}} :</strong> {{\Auth::user()->dateFormat( $payslip->created_at)}}<br>
//                             </address>
//                         </div>
//                         <div class="col-md-6 text-end">
//                             <address>
//                                 <strong>{{\Utility::getValByName('company_name')}} </strong><br>
//                                 {{\Utility::getValByName('company_address')}} , {{\Utility::getValByName('company_city')}},<br>
//                                 {{\Utility::getValByName('company_state')}}-{{\Utility::getValByName('company_zipcode')}}<br>
//                                 <strong>{{__('Salary Slip')}} :</strong> {{ $payslip->salary_month}}<br>
//                             </address>
//                         </div>
//                     </div>
//                 </div>
//             </div>

//             <div class="row mt-2">
//                 <div class="col-md-12">
//                     <div class="card-body table-border-style">

//                         <div class="table-responsive">
//                             <table class="table table-md">
//                                 <tbody>
//                                 <tr class="font-weight-bold">
//                                     <th>{{__('Earning')}}</th>
//                                     <th>{{__('Title')}}</th>
//                                     <th>{{__('Type')}}</th>
//                                     <th class="text-end">{{__('Amount')}}</th>
//                                 </tr>
//                                 <tr>
//                                     <td>{{__('Basic Salary')}}</td>
//                                     <td>-</td>
//                                     <td>-</td>
//                                     <td class="text-end">{{  \Auth::user()->salaryPriceFormat( $payslip->basic_salary)}}</td>
//                                 </tr>
//                                 @foreach ($payslipDetail['earning']['allowance'] as $allowance)
//                                     @php
//                                         $employess = \App\Models\Employee::find($allowance->employee_id);
//                                         $allowance = json_decode($allowance->allowance);
//                                     @endphp
//                                     @foreach ($allowance as $all)
//                                         <tr>
//                                             <td>{{ __('Allowance') }}</td>
//                                             <td>{{ $all->title }}</td>
//                                             <td>{{ ucfirst($all->type) }}</td>
//                                             @if ($all->type != 'percentage')
//                                                 <!-- <td class="text-end">
//                                                     {{ \Auth::user()->priceFormat($all->amount) }}</td> -->
//                                                     <td class="text-end">
//                                                     {{ \Auth::user()->salaryPriceFormat($all->amount) }}</td>
//                                             @else 
//                                                 <!-- <td class="text-end">{{ $all->amount }}%
//                                                     ({{ \Auth::user()->priceFormat(($all->amount * $payslip->basic_salary) / 100) }})
//                                                 </td> -->
//                                                 <td class="text-end">{{ $all->amount }}%
//                                                     ({{ \Auth::user()->salaryPriceFormat(($all->amount * $payslip->basic_salary) / 100) }})
//                                                 </td>
//                                             @endif
//                                         </tr>
//                                     @endforeach
//                                 @endforeach

//                                 @foreach ($payslipDetail['earning']['commission'] as $commission)
//                                     @php
//                                         $employess = \App\Models\Employee::find($commission->employee_id);
//                                         $commissions = json_decode($commission->commission);
//                                     @endphp
//                                     @foreach ($commissions as $empcom)
//                                         <tr>
//                                             <td>{{ __('Commission') }}</td>
//                                             <td>{{ $empcom->title }}</td>
//                                             <td>{{ ucfirst($empcom->type) }}</td>
//                                             @if ($empcom->type != 'percentage')
//                                                 <!-- <td class="text-end">
//                                                     {{ \Auth::user()->priceFormat($empcom->amount) }}</td> -->
//                                                     <td class="text-end">
//                                                     {{ \Auth::user()->salaryPriceFormat($empcom->amount) }}</td>
//                                             @else 
//                                                 <!-- <td class="text-end">{{ $empcom->amount }}%
//                                                     ({{ \Auth::user()->priceFormat(($empcom->amount * $payslip->basic_salary) / 100) }})
//                                                 </td> -->
//                                                 <td class="text-end">{{ $empcom->amount }}%
//                                                     ({{ \Auth::user()->salaryPriceFormat(($empcom->amount * $payslip->basic_salary) / 100) }})
//                                                 </td>
//                                             @endif
//                                         </tr>
//                                     @endforeach
//                                 @endforeach
//                                 @foreach ($payslipDetail['earning']['otherPayment'] as $otherPayment)
//                                     @php
//                                         $employess = \App\Models\Employee::find($otherPayment->employee_id);
//                                         $otherpay = json_decode($otherPayment->other_payment);
//                                     @endphp
//                                     @foreach ($otherpay as $op)
//                                         <tr>
//                                             <td>{{ __('Other Payment') }}</td>
//                                             <td>{{ $op->title }}</td>
//                                             <td>{{ ucfirst($op->type) }}</td>
//                                             @if ($op->type != 'percentage')
//                                                 <!-- <td class="text-end">
//                                                     {{ \Auth::user()->priceFormat($op->amount) }}</td> -->
//                                                     <td class="text-end">
//                                                     {{ \Auth::user()->salaryPriceFormat($op->amount) }}</td>
//                                             @else 
//                                                 <!-- <td class="text-end">{{ $op->amount }}%
//                                                     ({{ \Auth::user()->priceFormat(($op->amount * $payslip->basic_salary) / 100) }})
//                                                 </td> -->
//                                                 <td class="text-end">{{ $op->amount }}%
//                                                     ({{ \Auth::user()->salaryPriceFormat(($op->amount * $payslip->basic_salary) / 100) }})
//                                                 </td>
//                                             @endif
//                                         </tr>
//                                     @endforeach
//                                 @endforeach
//                                 @foreach ($payslipDetail['earning']['overTime'] as $overTime)
//                                     @php
//                                         $arrayJson = json_decode($overTime->overtime);
//                                         foreach ($arrayJson as $key => $overtime) {
//                                             foreach ($arrayJson as $key => $overtimes) {
//                                                 $overtitle = $overtimes->title;
//                                                 $OverTime = $overtimes->number_of_days * $overtimes->hours * $overtimes->rate;
//                                             }
//                                         }
//                                     @endphp
//                                     @foreach ($arrayJson as $overtime)
//                                         <tr>
//                                             <td>{{ __('OverTime') }}</td>
//                                             <td>{{ $overtime->title }}</td>
//                                             <td>-</td>
//                                             <td class="text-end">
//                                                 <!-- {{ \Auth::user()->priceFormat($overtime->number_of_days * $overtime->hours * $overtime->rate) }} -->
//                                                 {{ \Auth::user()->salaryPriceFormat($overtime->number_of_days * $overtime->hours * $overtime->rate) }}
//                                             </td>
//                                         </tr>
//                                     @endforeach
//                                 @endforeach
//                                 </tbody>
//                             </table>
//                         </div>
//                     </div>
//                     <div class="card-body table-border-style">

//                         <div class="table-responsive">
//                             <table class="table table-striped table-hover table-md">
//                                 <tbody>
//                                 <tr class="font-weight-bold">
//                                     <th>{{__('Deduction')}}</th>
//                                     <th>{{__('Title')}}</th>
//                                     <th>{{__('type')}}</th>
//                                     <th class="text-end">{{__('Amount')}}</th>
//                                 </tr>



//                                 @foreach ($payslipDetail['deduction']['loan'] as $loan)
//                                     @php
//                                         $employess = \App\Models\Employee::find($loan->employee_id);
//                                         $loans = json_decode($loan->loan);
//                                     @endphp
//                                     @foreach ($loans as $emploanss)
//                                         <tr>
//                                             <td>{{ __('Loan') }}</td>
//                                             <td>{{ $emploanss->title }}</td>
//                                             <td>{{ ucfirst($emploanss->type) }}</td>
//                                             @if ($emploanss->type != 'percentage')
//                                                 <!-- <td class="text-end">
//                                                     {{ \Auth::user()->priceFormat($emploanss->amount) }}</td> -->
//                                                     <td class="text-end">
//                                                     {{ \Auth::user()->salaryPriceFormat($emploanss->amount) }}</td>
                                                    
//                                             @else
//                                                 <td class="text-end">{{ $emploanss->amount }}%
//                                                     <!-- ({{ \Auth::user()->priceFormat(($emploanss->amount * $payslip->basic_salary) / 100) }}) -->
//                                                     ({{ \Auth::user()->salaryPriceFormat(($emploanss->amount * $payslip->basic_salary) / 100) }})
//                                                 </td>
//                                             @endif
//                                         </tr>
//                                     @endforeach
//                                 @endforeach

//                                 @foreach ($payslipDetail['deduction']['deduction'] as $deduction)
//                                     @php
//                                         $employess = \App\Models\Employee::find($deduction->employee_id);
//                                         $deductions = json_decode($deduction->saturation_deduction);
//                                     @endphp
//                                     @foreach ($deductions as $saturationdeduc)
//                                         <tr>
//                                             <td>{{ __('Saturation Deduction') }}</td>
//                                             <td>{{ $saturationdeduc->title }}</td>
//                                             <td>{{ ucfirst($saturationdeduc->type) }}</td>
//                                             @if ($saturationdeduc->type != 'percentage')
//                                                 <td class="text-end">
                                                
//                                                 {{ \Auth::user()->salaryPriceFormat($saturationdeduc->amount) }}
//                                                     <!-- {{ \Auth::user()->priceFormat($saturationdeduc->amount) }} -->
//                                                 </td> 
//                                             @else
//                                                 <td class="text-end">{{ $saturationdeduc->amount }}%
//                                                 ({{ \Auth::user()->salaryPriceFormat(($saturationdeduc->amount * $payslip->basic_salary) / 100) }})
//                                                     <!-- ({{ \Auth::user()->priceFormat(($saturationdeduc->amount * $payslip->basic_salary) / 100) }}) -->
//                                                 </td>
//                                             @endif
//                                         </tr>
//                                     @endforeach
//                                 @endforeach
//                                 </tbody>
//                             </table>
//                         </div>
//                     </div>

//                     <div class="row mt-4">
//                         <div class="col-lg-8">
                        
//                         </div>
//                         <div class="col-lg-4 text-end text-sm">
//                             <div class="invoice-detail-item pb-2">
//                                 <div class="invoice-detail-name font-bold">{{__('Total Earning')}}</div>
//                                 <div class="invoice-detail-value">{{ \Auth::user()->salaryPriceFormat($payslipDetail['totalEarning'])}}</div>
//                                 <!-- <div class="invoice-detail-value">{{ \Auth::user()->priceFormat($payslipDetail['totalEarning'])}}</div> -->
//                             </div>
//                             <div class="invoice-detail-item">
//                                 <div class="invoice-detail-name font-bold">{{__('Total Deduction')}}</div>
//                                 <div class="invoice-detail-value">{{ \Auth::user()->salaryPriceFormat($payslipDetail['totalDeduction'])}}</div>
//                                 <!-- <div class="invoice-detail-value">{{ \Auth::user()->priceFormat($payslipDetail['totalDeduction'])}}</div> -->
//                             </div>
//                             <hr class="mt-2 mb-2">
//                             <div class="invoice-detail-item">
//                                 <div class="invoice-detail-name font-bold">{{__('Net Salary')}}</div>
                                
//                                 <div class="invoice-detail-value invoice-detail-value-lg">{{ \Auth::user()->salaryPriceFormat($payslip->net_payble)}}</div>
//                                 <!-- <div class="invoice-detail-value invoice-detail-value-lg">{{ \Auth::user()->priceFormat($payslip->net_payble)}}</div> -->
//                             </div>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
//         <hr>
//         <div class="text-md-right pb-2 text-sm">
//         <div class="last_div">
//         <div class="left_last">
//                 <!-- <img src="{{url('/images/stamp.png')}}" alt="" style="height: 8rem;"> -->
//                 <p class="payment_p" style="color: black; padding-left: 15px;">{{__('Paid By')}}  IOREADY SDN BHD</p>
//                 <!-- <img src="{{url('/images/bg3.png')}}" alt=""  style="position: absolute; left: 0; bottom: 0; height: 2rem;"> -->
//             </div>
//          </div>
//         </div>
//     </div>';


//             $payslipArr = [

//                 'employee_name'=> $employee->name,
//                 'employee_email' => $employee->email,
//                 'payslip_name' =>   $payslip->name,
//                 'payslip_salary_month' => $payslip->salary_month,
//                 'payslip_url' =>$payslip->url,
//                 'pdf' =>$pdf,

//             ];
            
//             $resp = Utility::sendEmailTemplate('payslip_sent', [$employee->id => $employee->email], $payslipArr);



//             return redirect()->back()->with('success', __('Payslip successfully sent.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
//         }

//         return redirect()->back()->with('success', __('Payslip successfully sent.'));

//     }


public function send($id, $month)
{
    $settings = Utility::settings();
    if ($settings['payslip_sent'] == 1) {
        $payslip = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $payslip->name = $employee->name;
        $payslip->email = $employee->email;
        $m =$month;
        $payslipId = Crypt::encrypt($payslip->id);
        $payslip->url = route('payslip.payslipPdf', $payslipId);
        $payslip->downloads = url('payslip/pdf_download/'.$id.'/'.$m);
        
        $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id, $month);

    
        $payslipArr = [
            'employee_name' => $employee->name,
            'employee_email' => $employee->email,
            'payslip_name' => $payslip->name,
            'payslip_salary_month' => $payslip->salary_month,
            'payslip_url' => $payslip->url,
            // 'pdf_download' => storage_path("app/public/{$pdfPath}"),
            'pdf_download' => $payslip->downloads,
            
        ];
        // print_r($payslipArr);die;
        // Send Email with PDF attachment
        Utility::sendEmailTemplate('email.payslip', $payslipArr, function ($message) use ($payslipArr) {
            $message->to($payslipArr['employee_email'])
                    ->subject(__('Your Payslip for ') . $payslipArr['payslip_salary_month'])
                    ->attach($payslipArr['pdf_download']);
        });

        // $payslipArr = [

        //                     'employee_name'=> $employee->name,
        //                     'employee_email' => $employee->email,
        //                     'payslip_name' =>   $payslip->name,
        //                     'payslip_salary_month' => $payslip->salary_month,
        //                     'payslip_url' =>$payslip->url,
        //                     'pdf_download' =>$pdf,
            
        //                 ];
                        
                        $resp = Utility::sendEmailTemplate('payslip_sent', [$employee->id => $employee->email], $payslipArr);

            
        return redirect()->back()->with('success', __('Payslip successfully sent.'));
    }

    return redirect()->back()->with('error', __('Payslip could not be sent.'));
}



    public function payslipPdf($id)
    {
        $payslipId = Crypt::decrypt($id);

        $payslip  = PaySlip::where('id', $payslipId)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $month =$payslip->salary_month;
        
        $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id, $month);

        
        return view('payslip.payslipPdf', compact('payslip', 'employee', 'payslipDetail'));
    }

    public function editEmployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.salaryEdit', compact('payslip'));
    }

    public function updateEmployee(Request $request, $id)
    {


        if(isset($request->allowance) && !empty($request->allowance))
        {
            $allowances   = $request->allowance;
            $allowanceIds = $request->allowance_id;
            foreach($allowances as $k => $allownace)
            {
                $allowanceData         = Allowance::find($allowanceIds[$k]);
                $allowanceData->amount = $allownace;
                $allowanceData->save();
            }
        }


        if(isset($request->commission) && !empty($request->commission))
        {
            $commissions   = $request->commission;
            $commissionIds = $request->commission_id;
            foreach($commissions as $k => $commission)
            {
                $commissionData         = Commission::find($commissionIds[$k]);
                $commissionData->amount = $commission;
                $commissionData->save();
            }
        }

        if(isset($request->loan) && !empty($request->loan))
        {
            $loans   = $request->loan;
            $loanIds = $request->loan_id;
            foreach($loans as $k => $loan)
            {
                $loanData         = Loan::find($loanIds[$k]);
                $loanData->amount = $loan;
                $loanData->save();
            }
        }


        if(isset($request->saturation_deductions) && !empty($request->saturation_deductions))
        {
            $saturation_deductionss   = $request->saturation_deductions;
            $saturation_deductionsIds = $request->saturation_deductions_id;
            foreach($saturation_deductionss as $k => $saturation_deductions)
            {

                $saturation_deductionsData         = SaturationDeduction::find($saturation_deductionsIds[$k]);
                $saturation_deductionsData->amount = $saturation_deductions;
                $saturation_deductionsData->save();
            }
        }


        if(isset($request->other_payment) && !empty($request->other_payment))
        {
            $other_payments   = $request->other_payment;
            $other_paymentIds = $request->other_payment_id;
            foreach($other_payments as $k => $other_payment)
            {
                $other_paymentData         = OtherPayment::find($other_paymentIds[$k]);
                $other_paymentData->amount = $other_payment;
                $other_paymentData->save();
            }
        }


        if(isset($request->rate) && !empty($request->rate))
        {
            $rates   = $request->rate;
            $rateIds = $request->rate_id;
            $hourses = $request->hours;

            foreach($rates as $k => $rate)
            {
                $overtime        = Overtime::find($rateIds[$k]);
                $overtime->rate  = $rate;
                $overtime->hours = $hourses[$k];
                $overtime->save();
            }
        }


        $payslipEmployee                       = PaySlip::find($request->payslip_id);
        $payslipEmployee->allowance            = Employee::allowance($payslipEmployee->employee_id);
        $payslipEmployee->commission           = Employee::commission($payslipEmployee->employee_id);
        $payslipEmployee->loan                 = Employee::loan($payslipEmployee->employee_id);
        $payslipEmployee->saturation_deduction = Employee::saturation_deduction($payslipEmployee->employee_id);
        $payslipEmployee->other_payment        = Employee::other_payment($payslipEmployee->employee_id);
        $payslipEmployee->overtime             = Employee::overtime($payslipEmployee->employee_id);
        $payslipEmployee->net_payble           = Employee::find($payslipEmployee->employee_id)->get_net_salary();
        $payslipEmployee->save();

        return redirect()->route('payslip.index')->with('success', __('Employee payroll successfully updated.'));
    }

    public function export(Request $request)
    {
        $name = 'payslip_' . date('Y-m-d i:h:s');
        $data = Excel::download(new PayslipExport($request), $name . '.xlsx'); ob_end_clean();
        return $data;
    }
}
