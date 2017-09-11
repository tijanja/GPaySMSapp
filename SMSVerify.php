<?php
date_default_timezone_set("Africa/Lagos");

class Transaction
{
    var $response,$param,$customerId,$amount,$date,$ref,$status,$account,$customerName,$token,$unit,$payType,$preOut,$outSt,$carriedOv;
    function __construct($param)
    {
        
        $this->param = $param;
        $keyWord = strtoupper(trim($param['keyword']));
        $keyWordValue = trim($param['value']);
        
                switch ($keyWord)
                {
                    case 'CUSTOMER':
                        
                            $sql1 = "select * from customer where account='$keyWordValue' or meter='$keyWordValue' limit 1;";
                            $obj = mysql_fetch_object(mysql_query($sql1));
                            
                            $account = $obj->id;
                            if(isset($account))
                            {
                                    $sql = "select * from transaction where customerID='$account' or gpayRef='$account' order by id desc limit 1;";
                                    $res = mysql_fetch_object(mysql_query($sql));

                                    if(isset($res->id))
                                    {
                                            $this->amount = $res->amount;
                                            $this->date = $res->date;
                                            $this->status= $res->status;

                                            $this->ref = $res->gPayRef;
                                            $customer = new Customer($res->customerID);
                                            if(strtoupper($customer->getCategory())=='PREPAID')
                                            {
                                                $this->account = $customer->getMeter();
                                            }
                                            else
                                            {
                                                $this->account = $customer->getAccount();
                                            }

                                            $this->payType = strtoupper($customer->getCategory());
                                            $this->customerName = $customer->getName();

                                            if(strtoupper($customer->getCategory())=='PREPAID')
                                            {
                                                $this->unit = $res->unit;
                                                $this->token = $res->token;
                                            }
                                            else
                                            {
                                                $this->preOut = $res->previousOutstanding;
                                                $this->outSt = $res->outstanding;
                                                $this->carriedOv = $res->carriedOver;

                                            }
                                    }
                                    else
                                    {
                                        $sql2 = "select * from agent_new_transaction where account='$account' or gpayRef='$account' order by id desc limit 1;";
                                        $res2 = mysql_fetch_object(mysql_query($sql2));

                                        $this->amount = $res2->amount;
                                            $this->date = $res2->date;
                                            $this->status= $res2->status;
                                            $this->customerName = $res2->customerName;
                                            $this->ref = $res2->gpayRef;
                                            $this->payType = $res2->payType;
                                            $this->account = $res2->account;

                                            if($res2->payType=='PREPAID')
                                            {
                                                $this->unit = $res2->unit;
                                                $this->token = $res2->token;
                                            }
                                            else
                                            {
                                                $this->preOut = $res2->preOutstanding;
                                                $this->outSt = $res2->outstanding;
                                                $this->carriedOv = $res2->carriedOver;

                                            }
                                    }
                            }
                            else
                            {
                                
                                $a = explode("/",$keyWordValue);
                                $ref = $a[1];
                                $sql = "select * from transaction where gpayRef='$ref' order by id desc limit 1;";
                                $res = mysql_fetch_object(mysql_query($sql));

                                    if(isset($res->id))
                                    {
                                            $this->amount = $res->amount;
                                            $this->date = $res->date;
                                            $this->status= $res->status;

                                            $this->ref = $res->gPayRef;
                                            $customer = new Customer($res->customerID);
                                            if(strtoupper($customer->getCategory())=='PREPAID')
                                            {
                                                $this->account = $customer->getMeter();
                                            }
                                            else
                                            {
                                                $this->account = $customer->getAccount();
                                            }

                                            $this->payType = strtoupper($customer->getCategory());
                                            $this->customerName = $customer->getName();

                                            if(strtoupper($customer->getCategory())=='PREPAID')
                                            {
                                                $this->unit = $res->unit;
                                                $this->token = $res->token;
                                            }
                                            else
                                            {
                                                $this->preOut = $res->previousOutstanding;
                                                $this->outSt = $res->outstanding;
                                                $this->carriedOv = $res->carriedOver;

                                            }
                                    }
                                    else
                                    {
                                        $sql2 = "select * from agent_new_transaction where account='$account' or gpayRef='$account' order by id desc limit 1;";
                                        $res2 = mysql_fetch_object(mysql_query($sql2));

                                        $this->amount = $res2->amount;
                                            $this->date = $res2->date;
                                            $this->status= $res2->status;
                                            $this->customerName = $res2->customerName;
                                            $this->ref = $res2->gpayRef;
                                            $this->payType = $res2->payType;
                                            $this->account = $res2->account;

                                            if($res2->payType=='PREPAID')
                                            {
                                                $this->unit = $res2->unit;
                                                $this->token = $res2->token;
                                            }
                                            else
                                            {
                                                $this->preOut = $res2->preOutstanding;
                                                $this->outSt = $res2->outstanding;
                                                $this->carriedOv = $res2->carriedOver;

                                            }
                                    }
                            }
                            
                            
                           $this->response = $this->getTransDetails();
                break;
            
                case 'AGENT': 
                $agentPin = $keyWordValue;
                $sql = "select * from vtu_masterdistributordetails where pin='$agentPin';";
                $obj = mysql_fetch_object(mysql_query($sql));
                
                $string = 'Company: '.$obj->companyName."\nName: ".$obj->last_name." ".$obj->frist_name."\nAddr: ".$obj->address."\nPhone: ".$obj->Phone;
                $this->sendSMS($this->param['sender'], $string);
                
                break;
            
            case 'SUBAGENT':
                 $subNumber = $keyWordValue;
                $sql = "select * from sub_distributor where pin='$subNumber';";
                $obj = mysql_fetch_object(mysql_query($sql));
                
                $pin = $obj->pin;
                $sql2 = "select * from vtu_masterdistributordetails where pin='$pin';";
                $obj2 = mysql_fetch_object(mysql_query($sql2));
                
                $string = 'Company: '.$obj2->companyName."\nName: ".$obj->name."\nAddr: ".$obj2->address."\nPhone: ".$obj->Phone;
                $this->sendSMS($this->param['sender'], $string);
                break;
            
            case 'CASHIER':
                $sql = "select * from user where username='$keyWordValue' limit 1";
                $obj = mysql_fetch_object(mysql_query($sql));
                
                if(isset($obj->id))
                {
                    $cashier = new Cashier($obj->id);
                    $string = 'Name: '.$cashier->getFirstName()." ".$cashier->getLastName()."\nCash Office: ".$cashier->getCashOfficeName()."\nAddress: ".$cashier->getCashOfficeAddres()."\nService Center: ".$cashier->getServiceCenter();
                    $this->sendSMS($this->param['sender'], $string);
                }
                else
                {
                    $this->sendSMS($this->param['sender'], "No such cashier with G-Pay");
                }
                
                
                break;
        }
        
         
    }
    
    function getTransDetails()
    {
        if($this->payType=="POSTPAID")
        {
            $msg = 'Name: '.$this->customerName."\nAccount: ".$this->account."\nAmount: ".$this->amount."\nDate: ".$this->date."\nRef: ".$this->ref;
        }
        else
        {
            $msg = 'Name: '.$this->customerName."\nAccount: ".$this->account."\nAmount: ".$this->amount."\nDate: ".$this->date."\nToken: ".$this->token."\nUnit: ".$this->unit."\nRef: ".$this->ref;
        }
        
       return $this->sendSMS($this->param['sender'], $msg);
    }
    
    function sendSMS($number1,$message)
    {
           
            
            SMSHulk_get($number1, $message);
            
      }
      
      function getResponse()
      {
          return $this->response;
      }
}

class Customer
{
    var $name,$account,$meter,$category,$tariff,$address,$email,$ibcID,$createdBy,$date,$phone,$discoId;
    function __construct($customerId)
    {
        $sql = "select * from customer where id='$customerId' limit 1;";
        $obj = mysql_fetch_object(mysql_query($sql));
        
        $this->name = $obj->name;
        $this->account = $obj->account;
        $this->meter = $obj->meter;
        $this->category = $obj->custCategory;
        $this->tariff = $obj->tariff;
        $this->address = $obj->address;
        $this->email = $obj->email;
        $this->phone = $obj->phone;
        $this->ibcID = $obj->ibcID;
        $this->createdBy = $obj->createdBy;
        $this->date = $obj->date;
        $this->discoId = $obj->discoID;
     
    }
    
    function getName() {
        return $this->name;
    }

    function getAccount() {
        return $this->account;
    }

    function getMeter() {
        return $this->meter;
    }

    function getCategory() {
        return $this->category;
    }

    function getTariff() {
        return $this->tariff;
    }

    function getAddress() {
        return $this->address;
    }

    function getEmail() {
        return $this->email;
    }

    function getIbcID() {
        return $this->ibcID;
    }

    function getCreatedBy()
    {
        $user = new Cashier($this->createdBy);
        return $user->username;
    }

    function getDate() {
        return $this->date;
    }

    function getPhone() {
        return $this->phone;
    }

    function getDiscoId() {
        return $this->discoId;
    }


}

