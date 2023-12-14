    public function getTatNDeliveryStatus(Request $request)
    {
        $pincode = $request->get('pincode');
        
        if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            
            
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
        



        $servicibilityDetails = $this->getExpectedDateofDelivery_Ecom_express($pincode);
        if ($servicibilityDetails && ($servicibilityDetails['error'] == true || $servicibilityDetails['delivery'] == 2)) {
            $servicibilityDetails = $this->getExpectedDateofDelivery_Delhivery($pincode);
        }

        $delhivery_servicibilityDetails = $servicibilityDetails;

        
        
        if ($servicibilityDetails) {
            if ($servicibilityDetails['error'] == true || $servicibilityDetails['delivery'] == 2) {
                $servicibilityDetails = $this->getExpectedDateofDelivery_BlueDart($pincode);
            }
        } else {
            
            $servicibilityDetails = $this->getExpectedDateofDelivery_BlueDart($pincode);
        }
        
        

        if ($servicibilityDetails == NULL && $delhivery_servicibilityDetails == NULL) {
            
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
                
                $isDeliveryAvailMsg = "Your Pincode is Serviceable";

                
            } else {
                
                $isDeliveryAvailMsg = "Pincode Not Serviceable";
            }
        } else {
            
            $isDeliveryAvailMsg = "Pincode Not Serviceable";
        }



        if ($servicibilityDetails) {
            
            if (isset($delhivery_servicibilityDetails['delivery'])) {
                

                if ($servicibilityDetails['error'] === true && $delhivery_servicibilityDetails['delivery'] == 2) {
                    
                    
                    $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
                    <div class="error">
                        <small>Delivery</small>
                        <div>' . $servicibilityDetails['errorMessage'] . '</div>
                    </div>';
                    $pincodeResult = ['html' => $deliveryDetail];
                    $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => $servicibilityDetails['errorMessage']];
                    return response()->json($arr);
                } else if ($servicibilityDetails['error'] === true && $delhivery_servicibilityDetails['delivery'] == 1) {
                    
                    

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
                    
                    $deliveryDetail = '<i class="and-check" aria-hidden="true"></i>
            <div class="success">
                <small>Delivery</small>
                <div class="">' . $isDeliveryAvailMsg . '</div>
            </div>';


                    $pincodeResult = ['html' => $deliveryDetail, "Test Attributes" => ["dataFromCourierPartner" => [$servicibilityDetails, $delhivery_servicibilityDetails]]];
                    $arr = ['status' => false, 'message' => 'No Result Found, Tat Not Available', 'data' => $pincodeResult, 'messageCase' => 2, 'dispMessage' => $isDeliveryAvailMsg];
                    return response()->json($arr);

                    
                } else if ($delhivery_servicibilityDetails['delivery'] === 2) {
                    
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
        
        
        
        
        
        
        $deliveryDetail = '<i class="and-cancel" aria-hidden="true"></i>
        <div class="error">
                <small>Delivery</small>
                <div>Not Available</div>
        </div>';

        $pincodeResult = [];
        if ($pincodelist == null) {
            
            
            $pincodeResult = ['html' => $deliveryDetail];
            $arr = ['status' => false, 'message' => 'No Result Found', 'data' => $pincodeResult, 'messageCase' => 1, 'dispMessage' => 'Pincode Not Serviceable'];
            return response()->json($arr);
        }

        foreach ($pincodelist as $pincoderow) {
            
            $current_date = date("Y/m/d");
            $tat = $pincoderow['tat'];
           



            if (DateTime::createFromFormat('d-M-y', $tat) !== false) {
                
                

                $tatDate = DateTime::createFromFormat('d-M-y', $tat)->getTimestamp();
                
                $tatDateFormated = date("Y/m/d", $tatDate);
                

                $max_days = $this->getDateDiff($current_date, $tatDateFormated);
            } else {
                
                
                
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
            
            
            
            
            $max_days_final = (int) ($max_days + 4);
            
            
            $lastDate = date("Y/m/d", strtotime($current_date . " +" . $max_days_final . " day"));

            


            $period = $this->displayDates($current_date, $lastDate);
            
            foreach ($period as $date) {
                
                if ($this->isItAHoliday($date)) {
                    $total_tat = $total_tat + 1;
                    
                    if ($this->isItAWeekend($date)) {
                        continue;
                    }
                } else if ($this->isSunday($date)) {
                    
                    $total_tat = $total_tat + 1;
                }
                
            }
            

            $estimatedDeliveryDate = date("Y/m/d", strtotime($current_date . " +" . $total_tat . " day"));
            
            

            $deliveryAvail = "";
            

            $dayDifference = ceil((strtotime($estimatedDeliveryDate) - strtotime($current_date)) / 86400);
            $checkColor = '<i class="and-check" aria-hidden="true"></i>';
            
            

            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            

            $startDelivery = strtotime($estimatedDeliveryDate . "-2 day");
            $endDelivery = strtotime($estimatedDeliveryDate . "+1 day");
            $standardExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);
            $startDelivery = strtotime($estimatedDeliveryDate . "- 3 day");
            $endDelivery = strtotime($estimatedDeliveryDate . "+1 day");
            $ExpressExpDelivery = date("j", $startDelivery) . ' - ' . date("j M", $endDelivery);

            
            
            $deliveryAvail .= '<i class="and-check" aria-hidden="true"></i>										
                                        <div class="success">
                                            <small>Standard Shipping</small>
                                            <div class="timelines">' . $standardExpDelivery . '</div>
                                        </div>';


            
            if ($pincoderow['cod'] == 1) {
                
                $cod_html = '<i class="and-check" aria-hidden="true"></i>
<div class="success">
<small>Cash on Delivery</small>
<div class="timelines"> Available</div>
</div>';
            } else {
                
                $cod_html = '<i class="and-cancel" aria-hidden="true"></i>
    <div class="error">
    <small>Cash on Delivery</small>
    <div class="timelines">Not Available</div>
    </div>';
            }
            $returnExchangLine = '<p style="padding-top: 5px; clear: both;"> This item qualifies for our 60 days returns/exchange with full money back and store credit.</p>';

            

            $pincodeResult[] = ['pincode' => $pincoderow['pincode'], 'cod' => $pincoderow['cod'], 'delivery' => $pincoderow['delivery'], 'tat' => $total_tat, 'estimatedDeliveryDate' => $estimatedDeliveryDate, 'standardExpDelivery' => $standardExpDelivery, 'expressExpDelivery' => $ExpressExpDelivery, 'html' => $deliveryAvail, 'cod_html' => $cod_html, 'returnExchText' => $returnExchangLine, 'bufferedDeliveryDate' => date("Y/m/d", strtotime($estimatedDeliveryDate)), "Test Attributes" => ["currentDate" => $current_date, "lastDate" => $lastDate, "total_tat" => $total_tat, "estimatedDeliveryDate" => $estimatedDeliveryDate, "dayDifference" => $dayDifference, "originalTat" => $tat, "max_day" => $max_days, "max_day_final" => $max_days_final, "period" => $period, "dataFromCourierPartner" => $pincoderow]];
        }
        $arr = ['status' => true, 'message' => 'Result Found', 'data' => $pincodeResult, 'messageCase' => ($pincoderow['cod'] == 1) ? 4 : 3, 'dispMessage' => ($pincoderow['cod'] == 1) ? "Pincode Serviceable and COD Available" : "Pincode Serviceable but COD not Available"];
        
        return response()->json($arr);
        



    }
