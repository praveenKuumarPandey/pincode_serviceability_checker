<?php

namespace App\Http\Controllers;

// require 'vendor/autoload.php';

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;
use App\Http\Controllers\Controller;

use SoapClient;
use SoapHeader;

// require_once('vendor/autoload.php');

class pincodeChecker extends Controller
{
    //
    public function index()
    {
        return view('pincode_view');
    }

    public function home()
    {
        return view('pincode_serviceability_home');
    }

    public function uploadServiceabilityDataView()
    {
        return view('upload_serviceability_data_view');
    }

    public function soapClientTest()
    {

        $url = "http://www.dneonline.com/calculator.asmx";
        $url = "https://www.dataaccess.com/webservicesserver/NumberConversion.wso";
        $url = "http://webservices.oorsprong.org/websamples.countryinfo/CountryInfoService.wso";
        $url = "http://webservices.oorsprong.org/websamples.countryinfo/CountryInfoService.wso";

        $soapclient = new SoapClient($url . "?wsdl");
        $param = array('intA' => 500, 'intB' => 5);
        $param = array('intB' => 500);
        $param = array('sCountryISOCode' => 'IN');
        $response = $soapclient->Divide($param);
        $response = $soapclient->Subtract($param);
        $response = $soapclient->CapitalCity($param);
        var_dump($response);
        die;
        $result = json_decode(json_encode($response), true);

        print_r($result);
        die;



        $finderObj = SoapClient::to('https://api.example.com')->GetServicesforPincode();

        $finderObj = $soapClient->GetServicesforPincode();
        var_dump($finderObj);
        die;



    }

    public function getExpectedDateofDelivery_BlueDart($pincode)
    {
        $servicesAvailableCheck = [];
        $servicesAvailableCheck['couriorService'] = 'BlueDart';
        $apitype = 's';
        $area = 'GGN';
        $customercode = '397526';
        $licencekey = 'c54903831b8feeffd010b3fdf2c8b098';
        $loginid = 'GG397526';
        $password = "";

        $servicefinderurl = 'https://netconnect.bluedart.com/Ver1.8/ShippingAPI/Finder/ServiceFinderQuery.svc';
        if (!$servicefinderurl) {
            echo "Please enter Service Finder url from system configuration.";
            exit;
        }
        $soap = new SoapClient(
            $servicefinderurl . '?wsdl',
            array(
                'trace' => 1,
                'style' => SOAP_DOCUMENT,
                'use' => SOAP_LITERAL,
                'soap_version' => SOAP_1_2
            )
        );
        $soap->__setLocation($servicefinderurl);
        $soap->sendRequest = true;
        $soap->printRequest = false;
        $soap->formatXML = true;
        $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', 'http://tempuri.org/IServiceFinderQuery/GetServicesforPincode', true);
        $soap->__setSoapHeaders($actionHeader);

        $params = array(
            'pinCode' => $pincode,
            'profile' =>
                array(
                    'Api_type' => $apitype,
                    'Area' => $area,
                    'Customercode' => $customercode,
                    'IsAdmin' => '',
                    'LicenceKey' => $licencekey,
                    'LoginID' => $loginid,
                    'Password' => $password
                )
        );
        try {

            $result = $soap->__soapCall('GetServicesforPincode', array($params));

            //   var_dump($result);die;
            //echo '<h2>Parameters</h2><pre>'; print_r($result); echo '</pre>';

            /*$holidayList = $result->GetServicesforPincodeResult->BlueDartHolidays->Holiday;
          $holidayhtml = '';
            foreach($holidayList as $value){
                  $time_other_format = $value->HolidayDate;
                  $dt = new DateTime($time_other_format);
            $holidayhtml .= "<p><span><b>". $dt->format('Y-m-d') ." </b></span><span>". $value->Description ."</span></p>";
           

            }*/

            if ($result->GetServicesforPincodeResult->ErrorMessage == 'Valid') {

                if ($result->GetServicesforPincodeResult->ApexInbound == 'Yes') {


                    //echo "Service Available";
                    $pPinCodeFrom = 122001;
                    $pPinCodeTo = $pincode;
                    $pProductCode = 'A';
                    $pSubProductCode = 'C';
                    $pPudate = date("Y-m-d");
                    $pPickupTime = date("H:i");
                    $pudate = $pPudate . 'T00:00:00+00:00';
                    $params = array(
                        'pPinCodeFrom' => $pPinCodeFrom,
                        'pPinCodeTo' => $pPinCodeTo,
                        'pProductCode' => $pProductCode,
                        'pSubProductCode' => $pSubProductCode,
                        'pPudate' => $pudate,
                        'pPickupTime' => $pPickupTime,
                        'profile' =>
                            array(
                                'Api_type' => $apitype,
                                'Area' => $area,
                                'Customercode' => $customercode,
                                'IsAdmin' => '',
                                'LicenceKey' => $licencekey,
                                'LoginID' => $loginid,
                                'Password' => $password
                            )
                    );
                    $servicefinderurl = 'https://netconnect.bluedart.com/Ver1.8/ShippingAPI/Finder/ServiceFinderQuery.svc';
                    if (!$servicefinderurl) {
                        echo "Please enter Service Finder url from system configuration.";
                        exit;
                    }
                    $soap = new SoapClient(
                        $servicefinderurl . '?wsdl',
                        array(
                            'trace' => 1,
                            // 'style'               => SOAP_DOCUMENT,
                            // 'use'                 => SOAP_LITERAL,
                            'soap_version' => SOAP_1_2
                        )
                    );

                    $soap->__setLocation($servicefinderurl);
                    $soap->sendRequest = true;
                    $soap->printRequest = false;
                    $soap->formatXML = true;
                    $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing', 'Action', 'http://tempuri.org/IServiceFinderQuery/GetDomesticTransitTimeForPinCodeandProduct', true);
                    $soap->__setSoapHeaders($actionHeader);
                    $resultTransit = $soap->__soapCall('GetDomesticTransitTimeForPinCodeandProduct', array($params));
                    // print_r($resultTransit);die;
                    $ExpdeliveryDate = $resultTransit->GetDomesticTransitTimeForPinCodeandProductResult->ExpectedDateDelivery;

                    if ($result->GetServicesforPincodeResult->eTailCODAirInbound == 'Yes') {
                        $servicesAvailableCheck['cod'] = 1;
                        $servicesAvailableCheck['delivery'] = 1;
                        $servicesAvailableCheck['tat'] = $ExpdeliveryDate;
                        $servicesAvailableCheck['pincode'] = $pincode;
                        $servicesAvailableCheck['error'] = false;
                        $servicesAvailableCheck['errorMessage'] = '';
                    }
                    // return $ExpdeliveryDate;
                    // dd($servicesAvailableCheck);
                    // var_dump($servicesAvailableCheck);die;
                    return $servicesAvailableCheck;
                }
            } else {

                if ($result->GetServicesforPincodeResult->ErrorMessage != "") {
                    $res_error = $result->GetServicesforPincodeResult->ErrorMessage;
                } else {
                    $res_error = "Invalid Input";
                }
                // include('fedexcheckup.php');

                //    echo "<span class='error_msg'>".$res_error."</span>";die;

                $servicesAvailableCheck['pincode'] = $pincode;
                $servicesAvailableCheck['error'] = true;
                $servicesAvailableCheck['errorMessage'] = $res_error;
                return $servicesAvailableCheck;
            }
        } catch (Exception $e) {
            // echo "unable to call bluedart - <br/>error : ".$e->getMessage();
            $servicesAvailableCheck['pincode'] = $pincode;
            $servicesAvailableCheck['error'] = true;
            $servicesAvailableCheck['errorMessage'] = "Unexpected Error Please Try Again later";
            return $servicesAvailableCheck;
        }
    }

    public function getExpectedDateofDelivery_Delhivery($pincode)
    {
        $servicesAvailableCheck = [];
        $servicesAvailableCheck['couriorService'] = 'Delhivery';
        $filter_codes = $pincode;

        // if ($filter_codes == null || $filter_codes == "") {
        //     $res_error = 'Invalid Pincode';
        //     $servicesAvailableCheck['pincode'] =  $pincode;
        //     $servicesAvailableCheck['error'] =  true;
        //     $servicesAvailableCheck['errorMessage'] =  $res_error;
        //     return $servicesAvailableCheck;
        // }


        $client = new \GuzzleHttp\Client();

        try {

            $response = $client->request('GET', 'https://track.delhivery.com/c/api/pin-codes/json/?filter_codes=' . $filter_codes, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Token 64c3514b553bf81c86c52f259b834de41a5f0dae',
                ]

            ]);

            // echo $response->getBody();die;
            // die;
            // $json = json_decode($str, true);

            //returns an array.
            $resultDel = json_decode($response->getBody(), true);
            if ($resultDel['delivery_codes']) {
                //    var_dump($resultDel);die;
                // print_r($resultDel);die;
                $diliveryCodes = $resultDel['delivery_codes'];
                foreach ($diliveryCodes as $postalCodes) {
                    //    var_dump($postalCodes);die;
                    $postalCode = $postalCodes['postal_code'];
                    $centers = $postalCode['center'];
                    $codAvailable_status = $postalCode['cash'];
                    if ($codAvailable_status == 'Y') {
                        $codAvailable = 1;
                    } else {
                        $codAvailable = 2;
                    }

                    $servicalblea_status = false;
                    foreach ($centers as $center) {
                        if ($center['code'] != 'NSZ') {
                            $servicalblea_status = true;
                        }
                    }

                    if ($servicalblea_status == true) {
                        $servicalble = 1;
                    } else {
                        $servicalble = 2;
                    }
                    // var_dump($servicalblea_status);die;
                    $servicesAvailableCheck['cod'] = $codAvailable;
                    $servicesAvailableCheck['delivery'] = $servicalble;
                    // date_default_timezone_set("Asia/Calcutta");
                    $pincodell = $this->getExpectedDateOfDilivery($pincode);
                    if (!$pincodell) {
                        throw new Exception('pincode doesent available in TAT Table');
                    }
                    // var_dump($pincodell);die;
                    $expectedDeliveryDate = $this->getMaxDate($pincodell['TAT']);
                    // var_dump($expectedDeliveryDate);die;
                    $servicesAvailableCheck['tat'] = $expectedDeliveryDate;
                    $servicesAvailableCheck['pincode'] = $pincode;
                    $servicesAvailableCheck['error'] = false;
                    $servicesAvailableCheck['errorMessage'] = '';

                    // foreach($postalCodes as )
                }
                // dd($resultDel);

                // print_r($servicesAvailableCheck);die;
                return $servicesAvailableCheck;
            } else {
                $res_error = 'Invalid Pincode';
                $servicesAvailableCheck['pincode'] = $pincode;
                $servicesAvailableCheck['error'] = true;
                $servicesAvailableCheck['errorMessage'] = $res_error;
                return $servicesAvailableCheck;
            }
        } catch (Exception $e) {
            // echo $e->getMessage();
            // die;
            $servicesAvailableCheck['pincode'] = $pincode;
            $servicesAvailableCheck['error'] = true;
            $servicesAvailableCheck['errorMessage'] = "Unexpected Error Please Try Again later";
            return $servicesAvailableCheck;
        }
    }


    public function getOrderTrackingDetails()
    {
        $servicesAvailableCheck = [];
        $servicesAvailableCheck['couriorService'] = 'Delhivery';

        // echo "hello";



        $client = new \GuzzleHttp\Client();

        try {

            // https://track.delhivery.com/api/v1/packages/json?waybill=18148810035766&token=64c3514b553bf81c86c52f259b834de41a5f0dae
            $response = $client->request('GET', 'https://track.delhivery.com/api/v1/packages/json?waybill=18148810035766&token=64c3514b553bf81c86c52f259b834de41a5f0dae', [
                'headers' => [
                    'Accept' => 'application/json',
                    // 'Authorization' => 'Token 64c3514b553bf81c86c52f259b834de41a5f0dae',

                ],


            ]);

            echo ($response->getBody());
            die;
            // $json = json_decode($str, true);


        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getTatNDeliveryStatus(Request $request)
    {
        $pincode = $request->get('pincode');
        if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            // not valid
            $invalidPincodeMsg = "Invalid Pincode, Pincode Must be of 6 digit";
            $invalidPincode = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">' . $invalidPincodeMsg . '</div>';
            $pincodeResult = ['html' => $invalidPincode];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
            return response()->json($arr);
        }
        // echo "heloo , ".$pincode;die;
        $servicibilityDetails = $this->getExpectedDateofDelivery_Delhivery($pincode);
        $delhivery_servicibilityDetails = $servicibilityDetails;
        // var_dump($servicibilityDetails); die;
        // dd($delhivery_servicibilityDetails); die;
        if ($servicibilityDetails) {
            if ($servicibilityDetails['error'] == true || $servicibilityDetails['delivery'] == 2) {
                $servicibilityDetails = $this->getExpectedDateofDelivery_BlueDart($pincode);
            }
        } else {
            // echo "hi there";die;
            $servicibilityDetails = $this->getExpectedDateofDelivery_BlueDart($pincode);
        }
        // dd($delhivery_servicibilityDetails, $servicibilityDetails); die;
        // dd($servicibilityDetails); die;

        if ($servicibilityDetails == NULL && $delhivery_servicibilityDetails == NULL) {
            $isServicable = "Your Pin Code is Not Serviceable";
            $deliveryDetail = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">' . $isServicable . '</div>';
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
            return response()->json($arr);
        }
        if (($servicibilityDetails === NULL || $servicibilityDetails['error'] == true) && !isset($delhivery_servicibilityDetails['delivery'])) {
            //   echo "hi there";die;
            // kind of invalid pincode 
            //  not deliverable by blue dart and delivery both and not serviciable by any one 
            $isServicable = "Your Pin Code is Not Serviceable";
            $deliveryDetail = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">' . $isServicable . '</div>';
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
            return response()->json($arr);
        }

        if (isset($delhivery_servicibilityDetails['delivery'])) {
            if ($delhivery_servicibilityDetails['delivery'] === 1) {
                $isDeliveryAvailMsg = "Your Pin Code is serviceable";

                // descusess what message to show 
            } else {
                $isDeliveryAvailMsg = "Your Pin Code is Not Serviceable for Now";
            }
        } else {
            $isDeliveryAvailMsg = "Your Pin Code is Not Serviceable for Now";
        }



        if ($servicibilityDetails) {
            // echo "hi there";die;
            if (isset($delhivery_servicibilityDetails['delivery'])) {
                // echo "hi there";die;

                if ($servicibilityDetails['error'] === true && $delhivery_servicibilityDetails['delivery'] == 2) {
                    // not deliverable by blue dart and delivery both
                    $deliveryDetail = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">' . $servicibilityDetails['errorMessage'] . '</div>';
                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
                    return response()->json($arr);
                } else if ($servicibilityDetails['error'] === true && $delhivery_servicibilityDetails['delivery'] == 1) {

                    // not deliverable by blue dart but deliverable by delhivery without pre order tat information

                    $deliveryDetail = '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
                <i class="and-check" aria-hidden="true"></i>
            </div>
            <div class="col-2 pl__0 pr__0">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
                    return response()->json($arr);
                }
            } else if ($servicibilityDetails['error'] === true) {
                // echo "hi there";die;
                $deliveryDetail = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">' . $isDeliveryAvailMsg . '</div>';
                $pincodeResult = ['html' => $deliveryDetail];
                $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
                return response()->json($arr);
            }
        } else {
            if (isset($delhivery_servicibilityDetails['delivery'])) {

                if ($delhivery_servicibilityDetails['delivery'] === 1) {
                    $deliveryDetail = '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
                <i class="and-check" aria-hidden="true"></i>
            </div>
            <div class="col-2 pl__0 pr__0">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult];
                    return response()->json($arr);

                    // descusess what message to show 
                } else if ($delhivery_servicibilityDetails['delivery'] === 2) {
                    $deliveryDetail = '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
                <i class="and-check" aria-hidden="true"></i>
            </div>
            <div class="col-2 pl__0 pr__0">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult];
                    return response()->json($arr);
                }
            } else {
                // echo "hi there";die;
                $deliveryDetail = '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
                <i class="and-check" aria-hidden="true"></i>
            </div>
            <div class="col-2 pl__0 pr__0">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                $pincodeResult = ['html' => $deliveryDetail];
                $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult];
                return response()->json($arr);
            }
        }

        $pincodelist[] = $servicibilityDetails;
        // var_dump($pincodelist);die;
        // dd($pincodelist);
        // $pincodelist = DB::select('select * from pincode_option_n_tat where pincode = ?', [$pincode]);
        // echo "hello";die;
        // var_dump($pincodelist);die;
        $deliveryDetail = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">Delivery not available</div>';

        $pincodeResult = [];
        if ($pincodelist == null) {
            // echo "pin code null";die;
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
            return response()->json($arr);
        }

        foreach ($pincodelist as $pincoderow) {
            // var_dump($pincoderow); die;
            // dd($pincoderow);

            /**
             * 
             * get current date
             * get tat 
             * get period from current date to  last date according to tat
             * check if any of the date in period is in holiday list 
             * if present increase on 1 in total tat.
             * check if any of the date present in period is saturday or sunday 
             * if present increase 1 in total tat.
             * 
             */

            $current_date = date("Y/m/d");
            $tat = $pincoderow['tat'];
            // var_dump($tat); die;
            // --------------if database wize tat need to be used then use this 2 line------------
            // $max_days = explode('-', $tat);
            // $max_days = end($max_days);
            //  ------------ end --------------



            if (DateTime::createFromFormat('d-M-y', $tat) !== false) {
                // it's a date\
                // echo strtotime("now"), "\n";die;

                $tatDate = DateTime::createFromFormat('d-M-y', $tat)->getTimestamp();
                // var_dump($tatDate);die;
                $tatDateFormated = date("Y/m/d", $tatDate);
                // echo "its a date - ". $tatDateFormated;die;

                $max_days = $this->getDateDiff($current_date, $tatDateFormated);
            } else {
                // echo "its not a date - ". $tat;die;                
                // json to be created with error message
                $deliveryDetail = '<div class="clearfix"></div><div class="col-md-1 col-xs-1 col-sm-1 nopadding"><i class="and-cancel" aria-hidden="true"></i></div><div class="col-2 pl__0 pr__0">Unexpected Error Please try Again Later</div>';
                $pincodeResult = ['html' => $deliveryDetail];
                $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult];
                return response()->json($arr);
            }



            $total_tat = (int) ($max_days);
            // echo $total_tat; die;
            // echo $max_days; die;
            // var_dump($max_days);die;
            // date('d-m-Y',strtotime($startdate . ' +1 day'));
            $max_days_final = (int) $max_days;
            // $max_days_final = (int) $max_days + 2; 
            $lastDate = date("Y/m/d", strtotime($current_date . " +" . $max_days_final . " day"));

            // echo $lastDate;die;


            $period = $this->displayDates($current_date, $lastDate);
            //  var_dump($period); die;
            foreach ($period as $date) {
                // echo $total_tat."<br/>";
                if ($this->isItAHoliday($date)) {
                    $total_tat = $total_tat + 1;
                    if ($this->isItAWeekend($date)) {
                        continue;
                    }
                }

                if ($this->isSaturday($date)) {
                    $total_tat = $total_tat + 2;
                }
                // echo $total_tat."<br/>";

            }
            // echo $total_tat;die;

            $estimatedDeliveryDate = date("Y/m/d", strtotime($current_date . " +" . $total_tat . " day"));
            // var_dump($estimatedDeliveryDate);die;

            $deliveryAvail = "";
            // ----------- start standard and Express delivery dates------------

            $dayDifference = ceil((strtotime($estimatedDeliveryDate) - strtotime($current_date)) / 86400);
            $checkColor = '<i class=" and-check" aria-hidden="true"></i>';
            // dd($pincoderow);
            if ($dayDifference <= 2) {

                if (isset($pincoderow['couriorService']) && $pincoderow['couriorService'] == "BlueDart") {
                    // echo "hi there 1"; die;
                    $incrementedDays = 1;
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 3 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 5 day");
                    $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 3 day");
                    $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                } else {
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 4 day");
                    $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 1 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
                    $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                }
            } elseif ($dayDifference == 3) {
                if (isset($pincoderow['couriorService']) && $pincoderow['couriorService'] == "BlueDart") {
                    // echo "hi there 2"; die;
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 3 days");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 5 days");
                    $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 3 days");
                    $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                } else {
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 days");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 4 days");
                    $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 1 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 2 days");
                    $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                }
            } elseif ($dayDifference > 3) {
                // echo "hi there 3"; die;
                if (isset($pincoderow['couriorService']) && $pincoderow['couriorService'] == "BlueDart") {
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 3 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 5 day");
                    $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                    $ExpressExpDelivery = "NA";
                    $checkColor = '<i class="and-cancel" aria-hidden="true"></i>';
                } else {
                    $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
                    $endDelivery = strtotime($estimatedDeliveryDate . "+ 4 day");
                    $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
                    $ExpressExpDelivery = "NA";
                    $checkColor = '<i class="and-cancel" aria-hidden="true"></i>';
                }
            }

            // ----------- end standard and Express delivery dates--------------
            $deliveryAvail .= '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
											<i class="and-check" aria-hidden="true"></i>
										</div>
										<div class="col-2 pl__0 pr__0">
											<small>Standard Shipping</small>
											<div class="timelines">' . $standardExpDelivery . '</div>
										</div>';


            // --------------Cod Available html element---------------
            if ($pincoderow['cod'] == 1) {
                $cod_html = '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
<i class="and-check" aria-hidden="true"></i>
</div>
<div class="col-10 pl__0">
<small>Cash on Delivery</small>
<div class="timelines"> Available</div>
</div>';
            } else {
                $cod_html = '<div class="col-md-1 col-xs-1 col-sm-1 nopadding">
    <i class="and-cancel" aria-hidden="true"></i>
    </div>
    <div class="col-10 pl__0">
    <small>Cash on Delivery</small>
    <div class="timelines">Not Available</div>
    </div>';
            }
            $returnExchangLine = '<p style="padding-top: 5px; clear: both;"> This item qualifies for our 60 days returns/exchange with full money back and store credit.</p>';

            // --------------Cod Available html element---------------

            $pincodeResult[] = ['pincode' => $pincoderow['pincode'], 'cod' => $pincoderow['cod'], 'delivery' => $pincoderow['delivery'], 'tat' => $total_tat, 'estimatedDeliveryDate' => $estimatedDeliveryDate, 'standardExpDelivery' => $standardExpDelivery, 'expressExpDelivery' => $ExpressExpDelivery, 'html' => $deliveryAvail, 'cod_html' => $cod_html, 'returnExchText' => $returnExchangLine];
        }
        $arr = ['status' => true, 'message' => 'Result Found', 'data' => $pincodeResult];
        // print_r($arr); die;
        return response()->json($arr);
        // dd($users);



    }

    public function displayDates($date1, $date2, $format = 'Y/m/d')
    {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while ($current <= $date2) {
            $dates[] = date($format, $current);
            $current = strtotime($stepVal, $current);
        }
        return $dates;
    }

    public function isItAHoliday($date)
    {

        $holiday_rows = DB::select('select * from holiday_list where date = ?', [$date]);
        if ($holiday_rows) {
            return true;
        } else {
            return false;
        }
    }
    public function isItAWeekend($date)
    {
        $weekend = $this->isWeekend($date);
        // echo "in weekend";
        // var_dump($weekend);
        if ($weekend) {
            return true;
        } else {
            return false;
        }
    }

    public function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
    }

    public function isSaturday($date)
    {

        $datename_by_name = date('l', strtotime($date));
        $date_in_lower = strtolower($datename_by_name);
        // echo ($date_in_lower == "saturday");

        // var_dump($date_in_lower == "saturday");
        return ($date_in_lower == "saturday");
    }
    public function getDateDiff($date1, $date2)
    {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
        $interval = $date1->diff($date2);
        // var_dump($interval);die;
        return $interval->d;
    }

    public function getExpectedDateOfDilivery($pincode)
    {
        $pincodelist = DB::select('select * from pincode_option_n_tat where pincode = ?', [$pincode]);
        // var_dump($pincodelist);die;
        $servicesAvailableCheck = array();
        if ($pincodelist) {

            foreach ($pincodelist as $pincodeRow) {

                $servicesAvailableCheck['pincode'] = $pincodeRow->pincode;
                $servicesAvailableCheck['cod'] = $pincodeRow->cod;
                $servicesAvailableCheck['delivery'] = $pincodeRow->delivery;
                $servicesAvailableCheck['TAT'] = $pincodeRow->tat;
            }
            return $servicesAvailableCheck;
            // var_dump($pincodelist);die;
        } else {
            return null;
        }
    }

    public function getMaxDate($tat)
    {

        // var_dump($tat);die;

        $current_date = date("Y/m/d");
        $max_days = explode('-', $tat);
        $max_days = end($max_days);
        $max_days_final = (int) $max_days;
        $lastDate = date("d-M-y", strtotime($current_date . " +" . $max_days_final . " day"));
        return $lastDate;
        // echo $lastDate;die;


    }

    public function getAllDelhiveryPincode()
    {

        $dataArray11 = [];
        // $filter_codes = '122001';
        // $filter_codes = '122015';

        // if ($filter_codes == null || $filter_codes == "") {
        //     $res_error = 'Invalid Pincode';
        //     $servicesAvailableCheck['pincode'] =  $pincode;
        //     $servicesAvailableCheck['error'] =  true;
        //     $servicesAvailableCheck['errorMessage'] =  $res_error;
        //     return $servicesAvailableCheck;
        // }


        $client = new \GuzzleHttp\Client();

        try {

            $response = $client->request('GET', 'https://track.delhivery.com/c/api/pin-codes/json/', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Token 64c3514b553bf81c86c52f259b834de41a5f0dae',
                ]

            ]);

            // echo $response->getBody();die;
            $resultDel = json_decode($response->getBody(), true);
            // dd($resultDel); die;
            // var_dump($resultDel);die;
            // print_r($resultDel);die;
            if ($resultDel['delivery_codes']) {
                //    var_dump($resultDel);die;
                // print_r($resultDel);die;
                $diliveryCodes = $resultDel['delivery_codes'];
                foreach ($diliveryCodes as $postalCodes) {
                    // var_dump($postalCodes);die;
                    // dd($postalCodes);die;
                    $postalCode = $postalCodes['postal_code'];
                    // $centers = $postalCode['center'];
                    $centers = $postalCode['center'];
                    $distirct = $postalCode['district'];
                    $pin = $postalCode['pin'];


                    $dataArray11[] = ['pincode' => $pin, 'city' => $distirct];
                }


                $spreadSheetController = new spreadsheetController();
                $spreadSheetController->AllPincodeCityDeihivery($dataArray11);
                // dd($dataArray11);
                // print_r($dataArray11);die;
                // dd($resultDel);
                // $dataArray[] = array($pin, $distirct);
                // print_r($servicesAvailableCheck);die;
                // return $servicesAvailableCheck;


            } else {
                // $res_error = 'Invalid Pincode';
                // $servicesAvailableCheck['pincode'] =  $pincode;
                // $servicesAvailableCheck['error'] =  true;
                // $servicesAvailableCheck['errorMessage'] =  $res_error;
                // return $servicesAvailableCheck;
            }
        } catch (Exception $e) {
            echo "unable to call bluedart - <br/>error : " . $e->getMessage();
        }
    }

    public function internalGetTatNDeliveryStatus()
    {
        $myRequest = new Request();
        $myRequest->setMethod('GET');
        $myRequest->query->add(['pincode' => '110064']);
        $response = $this->getTatNDeliveryStatus($myRequest);
        dd($response);

    }
}
