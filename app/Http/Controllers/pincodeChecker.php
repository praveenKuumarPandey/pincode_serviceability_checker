<?php



namespace App\Http\Controllers;

// require 'vendor/autoload.php';

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\False_;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
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

    public function downloadPincodeServiceabilityDataUploadTemplate($filename = "pincodeServiceabilityUploadTemplate.xlsx")
    {
        $path = 'app/public/uploads/pincodeServiceabilityDetails';
        $path = storage_path($path . "/" . $filename);
        $reffid = Str::orderedUuid();

        $fileName_parts = explode('.', $filename);
        $extention = end($fileName_parts);
        $fileName_new = $fileName_parts[0] . "-" . $reffid . "." . $extention;
        // dd($fileName_new);

        // Download file with custom headers
        return response()->download($path, $fileName_new, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $fileName_new . '"'
        ]);
    }

    public function uploadPincodeServiceabilityDetailsSheet(Request $req)
    {
        // dd($req);


        // $this->validate($req, [
        //         'productUploadfile' => 'required|file|mimes:xls,xlsx'
        // ]);

        // $validatedData = $req->validate([
        //         'productUploadfile' => 'required',
        //         'productUploadfile.*' => 'mimes:csv,xlx,xls,xlsx'
        // ]);

        $productUploadeFile = $req->file('productAttributeUploadfile');
        $uploadFile = $req->input('uploadfile');

        // print_r($productUploadeFile);
        // print_r($uploadFile);die;
        $spreadsheetController = new spreadsheetController();
        $productAttributeArray = $spreadsheetController->exceltoArrayProductAttributes($productUploadeFile);

        // dd($productAttributeArray);

        return $this->updateServiceabilityDetails($productAttributeArray);
        // $productController = new productsController();
        // $result_returned = $productController->updateProductDetails($productAttributeArray);
        // if($result_returned['error']['is_any_error'] == true){

        // }
        // dd($result_returned);
        // return $result_returned;
    }

    function updateServiceabilityDetails($pincodeServiceabilityData)
    {

        $index = 0;
        $errors_tracker = [];
        $is_any_error = false;

        // dd($pincodeServiceabilityData);
        if (isset($pincodeServiceabilityData)) {
            foreach ($pincodeServiceabilityData as $pincodeServiceabilityDetails) {
                // dd($pincodeServiceabilityDetails);
                if ($pincodeServiceabilityDetails['delivery_partner'] == 'delhivery') {
                    //update delivery table
                    // dd('delivery_table');
                    $tableName = 'pincode_option_n_tat';
                    try {
                        $recordUpdated = DB::table($tableName)
                            ->updateOrInsert(
                                ['pincode' => $pincodeServiceabilityDetails['pincode']],
                                ['cod' => $pincodeServiceabilityDetails['cod'], 'delivery' => $pincodeServiceabilityDetails['delivery'], 'tat' => $pincodeServiceabilityDetails['tat']]
                            );


                        $errors_tracker[$index]['row'] = $index;
                        $errors_tracker[$index]['error'] = false;


                        // dd($recordUpdated);
                    } catch (Exception $exp) {

                        // dd("unable to update at row ", $exp);
                        $errors_tracker[$index]['row'] = $index;
                        $errors_tracker[$index]['error'] = true;
                        // $errors_tracker[$index]['error'] = true;
                        $is_any_error = true;

                    }


                } else if ($pincodeServiceabilityDetails['delivery_partner'] == 'ecom express') {
                    //update Ecom Express table
                    $tableName = 'pincode_option_n_tat_ecom_express';
                    // dd('Ecom_table');

                    try {
                        $recordUpdated = DB::table($tableName)
                            ->updateOrInsert(
                                ['pincode' => $pincodeServiceabilityDetails['pincode']],
                                ['cod' => $pincodeServiceabilityDetails['cod'], 'delivery' => $pincodeServiceabilityDetails['delivery'], 'tat' => $pincodeServiceabilityDetails['tat']]
                            );

                        // dd($recordUpdated);
                        $errors_tracker[$index]['row'] = $index;
                        $errors_tracker[$index]['error'] = false;

                    } catch (Exception $exp) {

                        // dd("unable to update at row EE", $exp);
                        $errors_tracker[$index]['row'] = $index;
                        $errors_tracker[$index]['error'] = true;
                        $is_any_error = true;

                    }
                }

                $index++;
            }
            return array(['status' => true, 'error' => ['error_tracker' => $errors_tracker, 'is_any_error' => $is_any_error]]);
        } else {
            return array(['status' => false, 'error' => ['error_tracker' => $errors_tracker, 'is_any_error' => $is_any_error]]);
        }

    }

    public function getExpectedDateofDelivery_BlueDart($pincode)
    {
        $servicesAvailableCheck = [];
        $servicesAvailableCheck['couriorService'] = 'BlueDart';
        // $apitype = 's';
        // $area = 'GGN';
        // $customercode = '397526';
        // $licencekey = 'c54903831b8feeffd010b3fdf2c8b098';
        // $loginid = 'GG397526';
        // $password = "";
        $apitype = Config::get('app.bluedart_api_vars.apitype');
        $area = Config::get('app.bluedart_api_vars.area');
        $customercode = Config::get('app.bluedart_api_vars.customercode');
        $licencekey = Config::get('app.bluedart_api_vars.licencekey');
        $loginid = Config::get('app.bluedart_api_vars.loginid');
        $password = Config::get('app.bluedart_api_vars.password');
        // dd($apitype,
        //     $area,
        //     $customercode,
        //     $licencekey,
        //     $loginid,
        //     $password);
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
                        $servicesAvailableCheck['originalTatInfo'] = $ExpdeliveryDate;
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
                    'Authorization' => 'Token ' . Config::get('app.delhivery_api_vars.apikey'),
                ]

            ]);

            // echo $response->getBody();die;
            // die;
            // $json = json_decode($str, true);

            //returns an array.
            $resultDel = json_decode($response->getBody(), true);
            //    var_dump($resultDel);die;
            // dd($resultDel);


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
                        throw new Exception('pincode does not available in TAT Table');
                    }
                    // var_dump($pincodell);die;
                    $expectedDeliveryDate = $this->getMaxDate($pincodell['TAT']);
                    // var_dump($expectedDeliveryDate);die;
                    $servicesAvailableCheck['tat'] = $expectedDeliveryDate;
                    $servicesAvailableCheck['originalTatInfo'] = $pincodell['TAT'];
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


    public function getExpectedDateofDelivery_Ecom_express($pincode)
    {
        $servicesAvailableCheck = [];
        $servicesAvailableCheck['couriorService'] = 'EcomExpress';
        $filter_codes = $pincode;



        try {
            $pincodelist = DB::select('select * from pincode_option_n_tat_ecom_express where pincode = ?', [$pincode]);
            if ($pincodelist) {

                foreach ($pincodelist as $pincodeRow) {

                    $servicesAvailableCheck['tat'] = $this->getMaxDate($pincodeRow->tat);
                    $servicesAvailableCheck['originalTatInfo'] = $pincodeRow->tat;
                    $servicesAvailableCheck['pincode'] = $pincodeRow->pincode;
                    $servicesAvailableCheck['cod'] = $pincodeRow->cod;
                    $servicesAvailableCheck['delivery'] = $pincodeRow->delivery;
                    $servicesAvailableCheck['error'] = false;
                    $servicesAvailableCheck['errorMessage'] = '';
                }

                // var_dump($pincodelist);die;
            } else {
                $res_error = 'Invalid Pincode';
                $servicesAvailableCheck['pincode'] = $pincode;
                $servicesAvailableCheck['error'] = true;
                $servicesAvailableCheck['errorMessage'] = $res_error;
            }

            return $servicesAvailableCheck;
        } catch (Exception $e) {
            // echo $e->getMessage();
            // die;
            $servicesAvailableCheck['pincode'] = $pincode;
            $servicesAvailableCheck['error'] = true;
            $servicesAvailableCheck['errorMessage'] = "Unexpected Error Please Try Again later";
            return $servicesAvailableCheck;
        }
    }

    public function getExpectedDateofDeliveryinString($pincode = null)
    {
        if (!$pincode) {
            return null;
        }
        $pincode_delivery_response = $this->getTatNDeliveryStatus(new Request(['pincode' => $pincode]));
        //return desired output date format
        $pincode_delivery_response = json_decode($pincode_delivery_response);
        if ($pincode_delivery_response->status == true) {
            return $pincode_delivery_response->data->bufferedDeliveryDate;
        } else {
            return null;
        }
        // dd($pincode_delivery_response);
    }

    public function getTatNDeliveryStatus(Request $request)
    {
        $pincode = $request->get('pincode');
        // dd($pincode);
        if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            // not valid
            // Message::Case 0
            $invalidPincodeMsg = "Invalid Pincode, Pincode Must be of 6 digit, Not starting with 0";
            $invalidPincode = '<i class="and-cancel" aria-hidden="true"></i>
            <div class="error">
                <small>Delivery</small>
                <div>' . $invalidPincodeMsg . '</div>
            </div>';
            $pincodeResult = ['html' => $invalidPincode];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => -1, 'dispMessage' => $invalidPincodeMsg];
            return response()->json($arr);
        }
        // echo "heloo , ".$pincode;die;



        $servicibilityDetails = $this->getExpectedDateofDelivery_Ecom_express($pincode);
        if ($servicibilityDetails && ($servicibilityDetails['error'] == true || $servicibilityDetails['delivery'] == 2)) {
            $servicibilityDetails = $this->getExpectedDateofDelivery_Delhivery($pincode);
        }

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
            //Message::case 1
            $isServicable = "Pincode Not Serviceable";
            $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
            <div class="error">
            <small>Delivery</small>
            <div>' . $isServicable . '</div>
            </div>';
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $isServicable];
            return response()->json($arr);
        }
        if (($servicibilityDetails === NULL || $servicibilityDetails['error'] == true) && !isset($delhivery_servicibilityDetails['delivery'])) {
            //   echo "hi there";die;
            // kind of invalid pincode 
            //  not deliverable by blue dart and delivery both and not serviciable by any one 
            //Message::case 1
            $isServicable = "Pincode Not Serviceable";
            $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
            <div class="error">
            <small>Delivery</small>
            <div>' . $isServicable . '</div>
            </div>';
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $isServicable];
            return response()->json($arr);
        }

        if (isset($delhivery_servicibilityDetails['delivery'])) {
            if ($delhivery_servicibilityDetails['delivery'] === 1) {
                //Message::case 2,3,4
                $isDeliveryAvailMsg = "Your Pincode is Serviceable";

                // descusess what message to show 
            } else {
                //Message::case 1
                $isDeliveryAvailMsg = "Pincode Not Serviceable";
            }
        } else {
            //Message::case 1
            $isDeliveryAvailMsg = "Pincode Not Serviceable";
        }



        if ($servicibilityDetails) {
            // echo "hi there";die;
            if (isset($delhivery_servicibilityDetails['delivery'])) {
                // echo "hi there";die;

                if ($servicibilityDetails['error'] === true && $delhivery_servicibilityDetails['delivery'] == 2) {
                    // not deliverable by blue dart and delivery both
                    //Message::case 1
                    $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
                    <div class="error">
                        <small>Delivery</small>
                        <div>' . $servicibilityDetails['errorMessage'] . '</div>
                    </div>';
                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $servicibilityDetails['errorMessage']];
                    return response()->json($arr);
                } else if ($servicibilityDetails['error'] === true && $delhivery_servicibilityDetails['delivery'] == 1) {
                    //Message::case 2
                    // not deliverable by blue dart but deliverable by delhivery without pre order tat information

                    $deliveryDetail = '<i class="and-check" aria-hidden="true"></i>
            <div class="success">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail, "Test Attributes" => ["dataFromCourierPartner" => [$servicibilityDetails, $delhivery_servicibilityDetails]]];
                    $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 2, 'dispMessage' => $isDeliveryAvailMsg];
                    return response()->json($arr);
                }
            } else if ($servicibilityDetails['error'] === true) {
                //Message::case 1
                // echo "hi there";die;
                $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
                <div class="error"> 
                    <small>Delivery</small>
                        <div class="">' . $isDeliveryAvailMsg . '</div>
                </div>';

                $pincodeResult = ['html' => $deliveryDetail];
                $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $isDeliveryAvailMsg];
                return response()->json($arr);
            }
        } else {
            if (isset($delhivery_servicibilityDetails['delivery'])) {

                if ($delhivery_servicibilityDetails['delivery'] === 1) {
                    //Message::case 2
                    $deliveryDetail = '<i class="and-check" aria-hidden="true"></i>
            <div class="success">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail, "Test Attributes" => ["dataFromCourierPartner" => [$servicibilityDetails, $delhivery_servicibilityDetails]]];
                    $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult, 'messageCase' => 2, 'dispMessage' => $isDeliveryAvailMsg];
                    return response()->json($arr);

                    // descusess what message to show 
                } else if ($delhivery_servicibilityDetails['delivery'] === 2) {
                    //Message::case 1
                    $deliveryDetail = '<i class="and-check" aria-hidden="true"></i>
            <div class="error">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $isDeliveryAvailMsg];
                    return response()->json($arr);
                }
            } else {
                // echo "hi there";die;
                //Message::case 1
                $deliveryDetail = '
                <i class="and-check" aria-hidden="true"></i>
            
            <div class="error">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                $pincodeResult = ['html' => $deliveryDetail];
                $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $isDeliveryAvailMsg];
                return response()->json($arr);
            }
        }

        $pincodelist[] = $servicibilityDetails;
        // var_dump($pincodelist);die;
        // dd($pincodelist);
        // $pincodelist = DB::select('select * from pincode_option_n_tat where pincode = ?', [$pincode]);
        // echo "hello";die;
        // var_dump($pincodelist);die;
        //Message::case 1
        $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
        <div class="error">
                <small>Delivery</small>
                <div>Not Available</div>
        </div>';

        $pincodeResult = [];
        if ($pincodelist == null) {
            // echo "Pincode null";die;
            //Message::case 1
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => 'Pincode Not Serviceable'];
            return response()->json($arr);
        }

        foreach ($pincodelist as $pincoderow) {
            // var_dump($pincoderow);
            // die;
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

            // $current_date = date("Y/m/d", strtotime("2023/10/20")); // for testing purposes
            $current_date = date("Y/m/d");
            $tat = $pincoderow['tat'];
            // var_dump($tat);
            // die;
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
                //Message:Case 0
                $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
                <div class="error">
                <small>Delivery</small>
                <div>Unexpected Error Please try Again Later</div>
                </div>';
                $pincodeResult = ['html' => $deliveryDetail];
                $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 0, 'dispMessage' => 'Unexpected Error Please try again Later'];
                return response()->json($arr);
            }



            $total_tat = (int) ($max_days + 4);
            // echo $total_tat; die;
            // echo $max_days; die;
            // var_dump($max_days);die;
            // date('d-m-Y',strtotime($startdate . ' +1 day'));
            $max_days_final = (int) ($max_days + 4);
            // $max_days_final = (int) $max_days + 2; 
            // $lastDate = date("Y/m/d", strtotime($current_date . " +" . $max_days_final . " day"));
            $lastDate = date("Y/m/d", strtotime($current_date . " +" . $max_days_final . " day"));

            // echo $lastDate;die;


            $period = $this->displayDates($current_date, $lastDate);
            //  var_dump($period); die;
            foreach ($period as $date) {
                // echo $total_tat."<br/>";
                if ($this->isItAHoliday($date)) {
                    $total_tat = $total_tat + 1;
                    //check if date is week end if yes no need to further increase tat.
                    if ($this->isItAWeekend($date)) {
                        continue;
                    }
                } else if ($this->isSunday($date)) {
                    //check if date is week end if yes no need to further increase tat.
                    $total_tat = $total_tat + 1;
                }
                // echo $total_tat."<br/>";
            }
            // echo $total_tat;die;

            $estimatedDeliveryDate = date("Y/m/d", strtotime($current_date . " +" . $total_tat . " day"));
            // $estimatedDeliveryDatewithBuffer = date("Y/m/d", strtotime($current_date . " +" . $total_tat . " day"));
            // var_dump($estimatedDeliveryDate);die;

            $deliveryAvail = "";
            // ----------- start standard and Express delivery dates------------

            $dayDifference = ceil((strtotime($estimatedDeliveryDate) - strtotime($current_date)) / 86400);
            $checkColor = '<i class="and-check" aria-hidden="true"></i>';
            // dd($pincoderow);
            // if ($dayDifference <= 2) {

            //     if (isset($pincoderow['couriorService']) && $pincoderow['couriorService'] == "BlueDart") {
            //         // echo "hi there 1"; die;
            //         $incrementedDays = 1;
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 3 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 5 day");
            //         $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 3 day");
            //         $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //     } else {
            //         // Dilivery and Ecom Express 
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 4 day");
            //         $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 1 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
            //         $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //     }
            // } elseif ($dayDifference == 3) {
            //     if (isset($pincoderow['couriorService']) && $pincoderow['couriorService'] == "BlueDart") {
            //         // echo "hi there 2"; die;
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 3 days");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 5 days");
            //         $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 3 days");
            //         $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //     } else {
            //         // Dilivery and Ecom Express 
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 days");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 4 days");
            //         $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 1 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 2 days");
            //         $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //     }
            // } elseif ($dayDifference > 3) {
            //     // echo "hi there 3"; die;
            //     if (isset($pincoderow['couriorService']) && $pincoderow['couriorService'] == "BlueDart") {
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 3 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 5 day");
            //         $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //         $ExpressExpDelivery = "NA";
            //         $checkColor = '<i class="and-cancel" aria-hidden="true"></i>';
            //     } else {
            //         // Dilivery and Ecom Express 
            //         $startDelivery = strtotime($estimatedDeliveryDate . "+ 2 day");
            //         $endDelivery = strtotime($estimatedDeliveryDate . "+ 4 day");
            //         $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            //         $ExpressExpDelivery = "NA";
            //         $checkColor = '<i class="and-cancel" aria-hidden="true"></i>';
            //     }
            // }

            $startDelivery = strtotime($estimatedDeliveryDate . "-2 day");
            $endDelivery = strtotime($estimatedDeliveryDate . "+1 day");
            $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            $startDelivery = strtotime($estimatedDeliveryDate . "- 3 day");
            $endDelivery = strtotime($estimatedDeliveryDate . "+1 day");
            $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);

            // ----------- end standard and Express delivery dates--------------\
            //Message:: case 3,4
            $deliveryAvail .= '<i class="and-check" aria-hidden="true"></i>										
										<div class="success">
											<small>Standard Shipping</small>
											<div class="timelines">' . $standardExpDelivery . '</div>
										</div>';


            // --------------Cod Available html element---------------
            if ($pincoderow['cod'] == 1) {
                //Message:: case 4
                $cod_html = '<i class="and-check" aria-hidden="true"></i>
<div class="success">
<small>Cash on Delivery</small>
<div class="timelines"> Available</div>
</div>';
            } else {
                //Message:: case 2,3
                $cod_html = '<i class="and-cancel" aria-hidden="true"></i>
    <div class="error">
    <small>Cash on Delivery</small>
    <div class="timelines">Not Available</div>
    </div>';
            }
            $returnExchangLine = '<p style="padding-top: 5px; clear: both;"> This item qualifies for our 60 days returns/exchange with full money back and store credit.</p>';

            // --------------Cod Available html element---------------

            $pincodeResult[] = ['pincode' => $pincoderow['pincode'], 'cod' => $pincoderow['cod'], 'delivery' => $pincoderow['delivery'], 'tat' => $total_tat, 'estimatedDeliveryDate' => $estimatedDeliveryDate, 'standardExpDelivery' => $standardExpDelivery, 'expressExpDelivery' => $ExpressExpDelivery, 'html' => $deliveryAvail, 'cod_html' => $cod_html, 'returnExchText' => $returnExchangLine, 'bufferedDeliveryDate' => date("Y/m/d", strtotime($estimatedDeliveryDate)), "Test Attributes" => ["currentDate" => $current_date, "lastDate" => $lastDate, "total_tat" => $total_tat, "estimatedDeliveryDate" => $estimatedDeliveryDate, "dayDifference" => $dayDifference, "originalTat" => $tat, "max_day" => $max_days, "max_day_final" => $max_days_final, "period" => $period, "dataFromCourierPartner" => $pincoderow]];
        }
        $arr = ['status' => true, 'message' => 'Result Found', 'data' => $pincodeResult, 'messageCase' => ($pincoderow['cod'] == 1) ? 4 : 3, 'dispMessage' => ($pincoderow['cod'] == 1) ? "Pincode Serviceable and COD Available" : "Pincode Serviceable but COD not Available"];
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
        return (date('N', strtotime($date)) > 6);
    }

    public function isSunday($date)
    {

        $datename_by_name = date('l', strtotime($date));
        $date_in_lower = strtolower($datename_by_name);
        // echo ($date_in_lower == "sunday");


        // var_dump($date_in_lower == "saturday");
        return ($date_in_lower == "sunday");
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
            // var_dump($pincodelist);die;
            return $servicesAvailableCheck;
        } else {
            return null;
        }
    }

    public function getMaxDate($tat)
    {

        // var_dump($tat);die;


        // $current_date = date("Y/m/d", strtotime("2023/10/20")); // for testing purposes
        $current_date = date("Y/m/d");
        $max_days = explode('-', $tat);
        $max_days = end($max_days);
        $max_days_final = (int) $max_days;
        $lastDate = date("d-M-y", strtotime($current_date . " +" . $max_days_final . " day"));
        // echo $lastDate;die;
        return $lastDate;
    }

}