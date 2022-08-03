<?php

namespace aw2\finvu;

use \ltajniaa\FinvuCommunicator\talker;

\aw2_library::add_service('finvu.raise_consent_request','raise consent request',['namespace'=>__NAMESPACE__]);
function raise_consent_request($atts,$content=null,$shortcode=null){

	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( \aw2_library::shortcode_atts( array(
		'finvu_template'=>'',
        'config'=>''
		), $atts) );

        $res=common_config($config);
        
        if(!isset($res['status']) || $res['status']=='error'){
            return $res;
        }

        $finvuTalker = $res['finvuTalker'];

        $result = $finvuTalker->raiseConsentRequest($config['finvu_user'], 'consent for statement read', $finvu_template, $config['api_key']);

        $resultArr = json_decode($result);

        if($resultArr->status == "success"){
        $data['ConsentHandle'] = $resultArr->store->ConsentHandle;
        $data['encryptedRequest'] = $resultArr->store->encryptedRequest;
        $data['requestDate'] = $resultArr->store->requestDate;
        $data['encryptedFiuId'] = $resultArr->store->encryptedFiuId;
            return array('status'=>'success','status_code'=>'200','response'=>$data,'message'=>'request raised!');
        }
        else{
            return array('status'=>'error','status_code'=>'501','message'=>'Some error!','response'=>$result);
        }

    }

    \aw2_library::add_service('finvu.check_consent_status','Check consent status',['namespace'=>__NAMESPACE__]);
    function check_consent_status($atts,$content=null,$shortcode=null){

        if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	    extract( \aw2_library::shortcode_atts( array(
        'config'=>'',
        'consent_handle'=>''
		), $atts) );


        $res=common_config($config);
        
        if(!isset($res['status']) || $res['status']=='error'){
            return $res;
        }

        $finvuTalker = $res['finvuTalker'];


        $result = $finvuTalker->checkConsentStatus($config['finvu_user'], $consent_handle);

        $resultArr = json_decode($result);

        if($resultArr->status == "success"){
            $data['consentStatus'] = $resultArr->store->consentStatus;
            $data['consentId'] = $resultArr->store->consentId;
            return array('status'=>'success','status_code'=>'200','response'=>$data,'message'=>'request raised!');
        }
        else{
            return array('status'=>'error','status_code'=>'501','message'=>'Some error!','response'=>$result);
        }

    }

    \aw2_library::add_service('finvu.trigger_data_request','trigger data request',['namespace'=>__NAMESPACE__]);
    function trigger_data_request($atts,$content=null,$shortcode=null){
        
        if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	    extract( \aw2_library::shortcode_atts( array(
        'config'=>'',
        'data'=>''
		), $atts) );

        $res=common_config($config);
        
        if(!isset($res['status']) || $res['status']=='error'){
            return $res;
        }

        $finvuTalker = $res['finvuTalker'];

        $dateTimeRangeFrom = $data['from_date'];
        $dateTimeRangeTo = $data['to_date'];

        $result = $finvuTalker->triggerDataRequest($config['finvu_user'], $data['consent_id'], $data['consent_handle'], $dateTimeRangeFrom, $dateTimeRangeTo);

        $resultArr = json_decode($result);
        if($resultArr->status == "success"){
            $res_data['sessionId'] = $resultArr->store->sessionId;
            return array('status'=>'success','status_code'=>'200','response'=>$res_data,'message'=>'request raised!');
        }
        else{
            return array('status'=>'error','status_code'=>'501','message'=>'Some error!','response'=>$result);
        }

    }

    \aw2_library::add_service('finvu.fetch_request_status','Fetch Request Status',['namespace'=>__NAMESPACE__]);
    function fetch_request_status($atts,$content=null,$shortcode=null){
        
        if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	    extract( \aw2_library::shortcode_atts( array(
        'config'=>'',
        'data'=>''
		), $atts) );

        $res=common_config($config);
        
        if(!isset($res['status']) || $res['status']=='error'){
            return $res;
        }

        $finvuTalker = $res['finvuTalker'];

        $dateTimeRangeFrom = $data['from_date'];
        $dateTimeRangeTo = $data['to_date'];

        $result = $finvuTalker->checkFetchRequestStatus($data['consent_id'], $config['session_id'], $data['consent_handle'], $config['finvu_user']);

        $resultArr = json_decode($result);

        if($resultArr->status == "success"){
        $data['fiRequestStatus'] = $resultArr->store->fiRequestStatus;
            return array('status'=>'success','status_code'=>'200','response'=>$data,'message'=>'request raised!');
        }
        else{
            return array('status'=>'error','status_code'=>'501','message'=>'Some error!','response'=>$result);
        }

    }

    \aw2_library::add_service('finvu.fetch_data','Fetch data',['namespace'=>__NAMESPACE__]);
    function fetch_data($atts,$content=null,$shortcode=null){
        
        if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	    extract( \aw2_library::shortcode_atts( array(
        'config'=>'',
        'data'=>''
		), $atts) );

        $res=common_config($config);
        
        if(!isset($res['status']) || $res['status']=='error'){
            return $res;
        }

        $finvuTalker = $res['finvuTalker'];

        $dateTimeRangeFrom = $data['from_date'];
        $dateTimeRangeTo = $data['to_date'];

        $result = $finvuTalker->fetchData($config['finvu_user'], $data['consent_id'], $data['session_id']);
	
        $resultArr = json_decode($result);
        
        if($resultArr->status == "success"){
            $data['fetchedData'] = $resultArr->store->fetchedData;
            return array('status'=>'success','status_code'=>'200','response'=>$data,'message'=>'request raised!');
        }
        else{
            return array('status'=>'error','status_code'=>'501','message'=>'Some error!','response'=>$result);
        }

    }

    function common_config($config){

        if(!isset($config['user_name']) || !isset($config['password']) || $config['user_name']=='' || $config['password']=''  )
        return array('status'=>'error','status_code'=>'501','message'=>'user_name or password is empty in config');


        $data['finvuTalker'] = new Talker($config['talker']); 

        $data['result_token'] = $data['finvuTalker']->getAccessToken($config['user_name'], $config['password']);

        return $data;
    }

?>
