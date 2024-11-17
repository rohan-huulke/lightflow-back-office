<?php

namespace App\Http\Controllers\Logistics;

use App\Exports\BartoliniOrdersExport;
use App\Exports\DhlOrdersExport;
use App\Exports\GlsOrdersExport;
use App\Exports\PaidOrdersExport;
use App\Helpers\ApiFunctionsHelper;
use App\Http\Controllers\Controller;
use App\Models\OrderLog;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LogisticsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function paidOrders(Request $request)
    {
        $params = [
          'detailedOutcome' => 'Pagato',
          'length' => $request->input('length') ?? '50'
        ];

        if ($request->input('daterange')) {
            $params['orderDate'] = $request->input('daterange');
        }

        if ($request->input('ordernumber')) {
            $params['orderId'] = $request->input('ordernumber');
        }

        if ($request->input('shipping_method')) {
            $params['shippingMethod'] = $request->input('shipping_method');
        }

        if ($request->input('customer')) {
            $params['customer'] = $request->input('customer');
        }

        if($request->input('sort')) {
            $params['sort'] = $request->input('sort');
        }

        $ordersObject = ApiFunctionsHelper::getRequestResult('post', config('constants')['orders']['paidOrders'], $params, []);
        //echo $ordersObject;
        //die();
        $paidOrders = json_decode($ordersObject);

        $shipmentMethodsObject = ApiFunctionsHelper::getRequestResult('post', config('constants')['shipment']['list'], $params, []);
        //echo $shipmentMethodsObject;
        //die();
        $shipmentMethods = json_decode($shipmentMethodsObject);

        return view('logistics.paid-orders.index', compact('paidOrders', 'shipmentMethods'));
    }
}