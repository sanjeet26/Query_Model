<?php  
class Website_model extends CI_Model  {
	public function __construct() {
        parent::__construct();
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
		$this->load->helper('array');
        $this->load->model('query_model'); 
        $this->load->model('website_model'); 
		$this->load->library("pagination");
    }

	public function check_buy_status($user_id,$pdf_id){
		$this->db->where('user_id', $user_id);
		$this->db->where('pdf_id', $pdf_id);
		$num_rows=$this->db->count_all_results('pdf_book_tbl');
		if($num_rows>0){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function emi_validity_date($user_id,$pdf_id){
			$query = $this->db->query('SELECT * from pdf_book_tbl where user_id='.$user_id.' AND pdf_id='.$pdf_id.' ');  		 
			$row=$query->row();
			if(!empty($row)){
			$tot_ins=$row->pub_Tot_Ins;
			$paid_ins=$row->no_of_sub_paid;
			$book_date=$row->add_date_time;
			$day_tmp=($paid_ins*90);
			$tmp=strtotime(''.$book_date.' +'.$day_tmp.' days');
			return date("d M Y",$tmp);
		}else{
			return false;
		}
	}

	public function get_expiry_date($user_id,$pdf_id){
		$query = $this->db->query('SELECT * from pdf_book_tbl where user_id='.$user_id.' AND pdf_id='.$pdf_id.' ');  		 
		$row=$query->row();
		if(!empty($row)){
		$tot_ins=$row->pub_Tot_Ins;
		$paid_ins=$row->no_of_sub_paid;
		$book_date=$row->add_date_time;
		$day_tmp=(($paid_ins*90)+15);
		$tmp=strtotime(''.$book_date.' +'.$day_tmp.' days');
		if($tot_ins==$paid_ins){
			return "LIFETIME";
		}
		else
		if($row->sale_type==1){
			return "LIFETIME";	
		}
		else{
			return date("d M Y",$tmp);
		}
		}else{
			return false;
		}
	}
	public function get_email_id($id){
		$query = $this->db->query('SELECT * from user where id='.$id.' ');  		 
		$row=$query->row();
		return $row->email_id;
	}
	public function get_publisher_name($id){
		$query = $this->db->query('SELECT * from publisher_tbl where user_id='.$id.' ');  		 
		$row=$query->row();
		return $row->first_name.' '.$row->last_name;
	}
	public function pdf_details($id){
		$this->db->where('isDeleted', '0');
		$this->db->where('id', $id);
		$query = $this->db->get('pdf_tbl');
        $row=$query->row();
		return $row;
	}

	public function website_pdf_details($id){
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->where('id', $id);
		$query = $this->db->get('pdf_tbl');  
        $row=$query->row();
		return $row;
	}
	public function getUserType($id){
		$query = $this->db->query('SELECT * from user where id='.$id.' ');  		 
		$row=$query->row();
		return $row->user_type;
	}
	public function select_all_free_book(){
		$this->db->limit(7);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->where('isDonate', '1');
		$this->db->where('isPrime', '0');
		$query = $this->db->get('pdf_tbl');  
        return $query;
	}
	
	public function published_books($id){
		$this->db->where('publish_by', $id);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$num_rows = $this->db->count_all_results('pdf_tbl');
		return $num_rows;
	}
	
	public function published_books_limit($id,$limit, $start){
		$this->db->where('publish_by', $id);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->limit($limit, $start);
		$query = $this->db->get('pdf_tbl');
         if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}

	public function select_pub_book(){
		$this->db->where('publish_by', $_SESSION['id_pub']);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$num_rows = $this->db->count_all_results('pdf_tbl');
		return $num_rows;
	}
	public function select_pub_book_limit($limit, $start){
		$this->db->where('publish_by', $_SESSION['id_pub']);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->limit($limit, $start);
		$query = $this->db->get('pdf_tbl');  
         if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	public function get_username($id){
		$query = $this->db->query('SELECT * from user where id='.$id.' ');  		 
		$row=$query->row();
		return $row->username;
	}
	
	public function get_full_name($id){
		$query = $this->db->query('SELECT * from user_details where user_id='.$id.' ');
		$row=$query->row();
		return $row->full_name;
	}
	
	public function get_pubname($id){
		$userType=$this->getUserType($id);
		if($userType == "publisher"){
			$query = $this->db->query('SELECT * from publisher_tbl where user_id='.$id.' ');  		 
			$row=$query->row();
			return $row->pub_comp_name;
		}else{
			return $this->get_full_name($id);
		}
	}
	
	public function get_pubprofile($id){
		$query = $this->db->query('SELECT * from publisher_tbl where user_id='.$id.' ');  		 
		$row=$query->row();
		if(!empty($row->profile_pic)){
				return base_url().'uploads/publisher/profile/thumbnail/'.$row->profile_pic; 
		}
		else{
			return base_url()."public/website/img/no-logo.png";
		}
	}

	public function user_details($id){
		$query = $this->db->query('SELECT * from user_details where user_id='.$id.' ');  		 
		$row=$query->row();
		return $row;
	}

	public function user_full_name($id){
		$row = $this->user_details($id);
		return $row->full_name;
	}

	public function get_userprofile($id){
		$query = $this->db->query('SELECT * from user_details where user_id='.$id.' ');
		$row=$query->row();
		return base_url().'uploads/user/profile/'.$row->profile_pic;
	}
	
	public function pdf_cat($bookId){
		$this->db->select('category');
		$this->db->where('id', $bookId);
		$result = $this->db->get('pdf_tbl')->row(); 
		return $result->category;
	}

	public function pdf_keywords($bookId){
		$this->db->select('meta_keyword');
		$this->db->where('id', $bookId);
		$result = $this->db->get('pdf_tbl')->row(); 
		return $result->meta_keyword;
	}

	public function select_more_product($bookId){
		$keywords=$this->pdf_keywords($bookId);
		if (strpos($keywords, ',') !== false) {
			$keywords=explode(",",$keywords);
			foreach($keywords as $kw){
				if(!empty($kw)){
					$data[]=$kw;
				}
			}
			$keywords=$data;
		}else{
			$keywords=$keywords;
		}
		if(count($keywords) >1){
			foreach($keywords as $keyword){
				if (strpos($keyword, ';') !== false) {
					if (($key = array_search($keyword, $keywords)) !== false) {
						unset($keywords[$key]);
					}
					$kwords=explode(";",$keyword);
					foreach($kwords as $kword){
						array_push($keywords,$kword);
					}
				}
			}
		}else{
			if(strpos($keywords, ';') !== false){
				$keywords=explode(";",$keywords);
				
			}
		}
		
		if(!empty($keywords[0])){
			$s=0;
			$n=count($keywords) - 1;
		}else{
			$s=1;
			$n=count($keywords);
		}
		if(count($keywords) > 1){
		for($i=$s; $i<=$n; $i++ ){
				if($i == $s){
					if(!empty(trim($keywords[$i]))){
						$like=" meta_keyword LIKE '%".trim($this->db->escape_like_str($keywords[$i]))."%' ";
					}
				}else{
					if(!empty(trim($keywords[$i]))){
						$like.="OR meta_keyword LIKE '%".trim($this->db->escape_like_str($keywords[$i]))."%' ";
					}
				}
			}
		}
		else{
			$like=" meta_keyword LIKE '%".trim($this->db->escape_like_str($keywords))."%' ";
		}
		$sql="SELECT * from pdf_tbl where `isDeleted`='0' && `status`='1' &&".$like." ;";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				if($row->id !== $bookId && $row->status !== "0"){
					$dataM[] = $row;
				}
			}
		}
        //print_r($dataM);
		return $dataM;
	}
	
	public function select_related_product($bookId,$more){
		$data=$more;
		$cat=$this->pdf_cat($bookId);
		$this->db->limit(7);
		$this->db->where("(category LIKE '%".$cat."%' )", NULL, FALSE);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->where('id !=', $bookId);
		$this->db->where_not_in('id', $more);
		$query = $this->db->get('pdf_tbl');
		if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
				if (!in_array($row->id, $more))
				{
					array_push($data,$row->id);
				}
            }
        }
		foreach($data as $id){
			$similar[]=$this->website_pdf_details($id);
		}
		return $similar;
	}
	
	public function get_views($bookId){
		$this->db->select('');
		$this->db->where('id !=', $bookId);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->order_by("tot_view", "desc");
		$query = $this->db->get('pdf_tbl');
		$i=1;
		if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					if($i <= 20){
						$data[$row->tot_view][] = $row->id;
					}
					$i++;
				}
			}
        return $data;
	}
	public function get_random_popular($bookId){
		$most_viewed=$this->get_views($bookId);
		foreach($most_viewed as $viewed){
			$r=(rand(0,count($viewed)-1));
			$data[]=$this->website_pdf_details($viewed[$r]);
		}
		return $data;
	}
	public function select_popular_book($bookId){
		$this->db->limit(14);
		$this->db->select('');
		$this->db->where('id !=', $bookId);
		$this->db->where('status', '1');
		$this->db->where('isDeleted', '0');
		$this->db->order_by("tot_view", "desc");
		$query = $this->db->get('pdf_tbl');
    //    $query = $this->db->query('SELECT * from pdf_tbl where isDeleted="0" && status="1" order by tot_view desc limit 7 ');  
        return $query;
	}
	
	public function select_popular_book_by_category(){
		$query = $this->db->query('SELECT * from cat_tbl where isDeleted="0" order by view_count desc limit 7 ');
        foreach($query->result() as $row){
			$cat_id[]=$row->id;
		}
		for($i=0;$i<=count($cat_id)-1;$i++){
			$query1 = $this->db->query('SELECT * from pdf_tbl where isDeleted="0" && status="1" && category like "%'.$cat_id[$i].'%" ');
			foreach($query1->result() as $row1){
				//$pdf[]=$row1->id;
				return $query1;
			}
		}
		//return $pdf;
	}
	########### Book Category ###########
	public function category_name($id){
		$this->db->select('cat_name');
		$this->db->where('id', $id);
		$result = $this->db->get('cat_tbl')->row();
		return $result->cat_name;
	}
	
	############ Get Banners ############
	public function get_banners(){
		$this->db->select('*');
		$query = $this->db->get('banner_tbl');  
         if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	
	public function get_banner_for($banner_for){
		$this->db->select('image_name');
		$this->db->where('banner_for',$banner_for);
		$result = $this->db->get('banner_tbl')->row();
        return $result->image_name;
	}
	
	######### Prime Membership #########
	public function getPrimeSubs(){
		$this->db->select('*');
		$this->db->where('user_id',$_SESSION['user_id']);
		$this->db->order_by("id", "desc");
		$query = $this->db->get('buy_subscrption_tbl');
		if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}
	public function isActivePrime(){
		$this->db->select('expire_date');
		$this->db->where('user_id',$_SESSION['user_id']);
		$this->db->order_by("id", "desc");
		$result = $this->db->get('buy_subscrption_tbl')->row();
		$today=date('Y-m-d H:i:s');
		$expiry=$result->expire_date;
		
		if(time() < strtotime($expiry)){
			return "Active";
		}else{
			return "Inactive";
		}
	}

	public function primeStatus($expiry){
		if(time() < strtotime($expiry)){
			return "<span style='color:green; fotn-weight:600; font-size:12.5px; '>Active</span>";
		}else{
			return "<span style='color:red; fotn-weight:500; font-size:12.5px; '>Expired</span>";
		}
	}
	
	public function getExpiry(){
		$this->db->select('expire_date');
		$this->db->where('user_id',$_SESSION['user_id']);
		$this->db->order_by("id", "desc");
		$result = $this->db->get('buy_subscrption_tbl')->row();
		$today=date('Y-m-d H:i:s');
		return $result->expire_date;
	}
	
	public function daysLeft(){
		$this->db->select('expire_date');
		$this->db->where('user_id',$_SESSION['user_id']);
		$this->db->order_by("id", "desc");
		$result = $this->db->get('buy_subscrption_tbl')->row();
		$today=date('Y-m-d H:i:s');
		$expiry= $result->expire_date;
		$secs = strtotime($expiry) - strtotime($today);
		echo $days = round($secs / 86400,0);
	}
	
	########### Prime Book ############
	public function bootType($bookId){
		$this->db->where("id", $bookId);
		$result=$this->db->get('pdf_tbl')->row();
		if($result->isDonate == 0){
			return "Buy";
		}else{
			if($result->isPrime == 1){
				return "Prime";
			}else{
				return "Free";
			}
		}
	}
	
	########### Prime Book ############
	public function isUserPurchase($book){
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$query=$this->db->get('pdf_book_tbl');
		if ($query->num_rows() > 0) {
            return true;
        }else{
			return false;
		}
	}
	
	public function issueActivated($book){
		$this->db->select('actvate_status');
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$result=$this->db->get('pdf_book_tbl')->row();
		return $result->actvate_status;
	}
	
	public function getActivateId($book){
		$this->db->select('id');
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$result=$this->db->get('pdf_book_tbl')->row();
		return $result->id;
	}

	public function isIssueLifetime($book){
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$result=$this->db->get('pdf_book_tbl')->row();
		if(!empty($result)){
			if($result->no_of_sub_paid == $result->pub_Tot_Ins || $result->pub_Tot_Ins == ""){
				return "Yes";
			}else{
				return "No";
			}
		}else{
			return false;
		}
	}
	public function getIssueExpiryDate($book){
		$this->db->select('*');
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$this->db->order_by("id", "desc");
		$result=$this->db->get('pdf_book_tbl')->row();
		$pur_date=$result->add_date_time;
		$expiry_days= 90 * $result->no_of_sub_paid;
		if(!empty($pur_date)){
			return $expiryDate=date('Y-m-d H:i:s', strtotime($pur_date. ' + '.$expiry_days.' days'));
		}else{
			return false;
		}
	}

	public function getIssueExpiryDays($book){
		$expiryDate=$this->getIssueExpiryDate($book);
		$today=date('Y-m-d H:i:s');
		$secs = strtotime($expiryDate) - strtotime($today);
		return $days = round($secs / 86400,0);
	}
	
	public function isIssueExpired($book){
		$expiry=$this->getIssueExpiryDate($book);
		if(time() >  strtotime($expiry)){
			return "Expired";
		}
	}
	public function retailerName($ret_id){
		$this->db->select('*');
		$this->db->where("user_id", $ret_id);
		$result=$this->db->get('retailer_tbl')->row();
		return $result->pub_comp_name;
	}

	public function retailerLogo($ret_id){
		$this->db->select('*');
		$this->db->where("user_id", $ret_id);
		$result=$this->db->get('retailer_tbl')->row();
		if(!empty($result->logo)){
		    return base_url()."uploads/retailer/logo/".$result->logo;
		}else{
		    return base_url().'public/retailer/img/default-logo.png';
		}
	}

	public function subsPaid($book){
		$this->db->select('*');
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$this->db->order_by("id", "desc");
		$result=$this->db->get('pdf_book_tbl')->row();
		return $result->no_of_sub_paid;
	}

	public function subsDetail($book){
		$this->db->select('*');
		$this->db->where("user_id", $_SESSION['user_id']);
		$this->db->where("pdf_id", $book);
		$this->db->order_by("id", "ASC");
		$query=$this->db->get('pdf_book_tbl');
		if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
	}

	public function isGraceOver($book){
		$expiry=$this->website_model->getIssueExpiryDate($book);
		if(!empty($expiry)){
			$gracePeriod=date('Y-m-d H:i:s', strtotime($expiry. ' + 15 days'));
			if(time() >  strtotime($gracePeriod)){
				return "Over";
			}else{
				return "Due";
			}
		}else{
			return false;
		}
	}
	
	public function getUserEmail(){
		$this->db->select('email_id');
		$this->db->where("id", $_SESSION['user_id']);
		$result=$this->db->get('user')->row();
		return $result->email_id;
	}
	
	public function retailerSTatus($id){
		$this->db->select('status');
		$this->db->where("id", $id);
		$this->db->where("user_type", 'retailer');
		$result=$this->db->get('user')->row();
		return $result->status;
	}
	
	public function getEmail($id){
		$this->db->select('email_id');
		$this->db->where("id", $id);
		$result=$this->db->get('user')->row();
		return $result->email_id;
	}
	
	public function getUsername($id){
		$this->db->select('username');
		$this->db->where("id", $id);
		$result=$this->db->get('user')->row();
		return $result->username;
	}
	
	public function getUserQR($id){
		$this->db->select('qr_image');
		$this->db->where("user_id", $id);
		$result=$this->db->get('user_details')->row();
		return $result->qr_image;
	}
	
	public function getBookThumb($pdfId){
		$this->db->where("id", $pdfId);
		$result= $this->db->get('pdf_tbl')->row();
		if(!empty($result->thumb_new)){
			return "thumb_new/200/".$result->thumb_new;
		}else{
			return "thumb/200/".$result->thumb;
		}
	}
	
	public function bookTitle($pdfId){
		$this->db->where("id", $pdfId);
		$result= $this->db->get('pdf_tbl')->row();
		return $result->title;
	}
	
	public function stepper($actual_link, $step){
		$steps=explode("&step=",$actual_link);
		return $steps[0]."&step=".$step;
	}
	
	public function userSessionChk(){
		$this->db->select('login_session_id');
		$this->db->where('id',$_SESSION['user_id']);
		$res=$this->db->get('user')->row();

		if($res->login_session_id !== $_SESSION['my_session_id']){
			$this->session->sess_destroy();
			$this->session->set_flashdata('err', 'Your session expired');
			redirect('login', 'refresh');
		}else{
			return false;
		}
	}

	public function publishedBy($id){
		if(strtolower($this->getUserType($id)) == "user"){
			return $this->get_full_name($id);
		}else{
			return $this->query_model->publisher_name($id);
		}
	}
	
	public function alreadyPurchased($book){
		$this->db->where('pdf_id',$book);
		$this->db->where('user_id',$_SESSION['user_id']);
		$query = $this->db->get('pdf_book_tbl');
		if ($query->num_rows() > 0) {
            return true;
        }
		else{
			return false;
		}
	}
	
	public function emiNo($book){
		$details=$this->pdf_details($book);
		if(empty($details->Tot_Ins)){
			return 1;
		}else{
			return $details->Tot_Ins;
		}
	}
	
	public function offerPrice($book){
		$details=$this->pdf_details($book);
		return  $details->offer_cost;
	}
	
	public function dealAmt($book){
		$details=$this->pdf_details($book);
		return ($details->offer_cost * $details->deal_percentage)/100;
	}

	public function helfCost($book){
		return round($this->offerPrice($book) - $this->dealAmt($book),2);
	}

	public function retailerPrice($book){
		$retailerMargin=$this->query_model->margins()->retailer;
		return round(($this->helfCost($book)*(100 + $retailerMargin))/100,2);
	}

	public function onlinePrice($book){
		$onlineMargin=$this->query_model->margins()->online;
		return $this->round(($this->helfCost($book)*(100 + $onlineMargin))/100);
	}
	
	public function onlineEMI($book){
		return $this->round($this->onlinePrice($book)/$this->emiNo($book));
	}
	
	
	public function notify_publisher($pub_id, $user_id, $sell_by, $pdf_id, $mode, $subs="" ){
	    $title=$this->pdf_details($pdf_id)->title;
	    $author=$this->pdf_details($pdf_id)->author_name;
	    $total_emi=$this->pdf_details($pdf_id)->Tot_Ins;
	    
	    $dealAmt=$this->dealAmt($pdf_id);
	    if(!empty($total_emi)){
	        $emi_price=$dealAmt/$total_emi;
	    }else{
	        $emi_price=$dealAmt;
	    }
	    
	    $full_name=$this->get_full_name($user_id);
		$this->load->library('email');
		$this->email->set_mailtype("html");
		if($mode == "D"){
			$this->email->subject('Direct sale by '.$sell_by);
		}else{
			$this->email->subject('Sale based on subscription by '.$sell_by);
		}
		$this->email->to($this->user_details($pub_id)->email_id);
		$this->email->from('info@helf.in', 'HELF');
		$this->email->cc('saha.shubhajeet@gmail.com,arunsingh035@gmail.com');
		$dear="Publisher";

		$msg='<!doctype html>
			<html>
			<head>
			<meta charset="utf-8">
			<title>Online Invoice</title>
			</head>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:600 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:0px !important; padding:0px !important;color:#000 !important;">
			Dear '.$dear.',
			</p></td></tr> </table>
			<table width="500" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important; text-align:center !important; line-height:18px !important; padding:0px !important;color:#000 !important;">';
				$msg.='For your information, HELF informs you that your BOOK '.$title.' has been given to the user on the Date"'.date('d-M-Y h:i:s').'" for ';

			if($mod == "D"){
				$msg.='Lifetime.<br/>';
			}else{
				$msg.='<strong>BOOK EMI- '.$total_emi.', '.$subs.' Completed</strong> <br/>';
			}
			$msg.='</p><p>User Name: '.$full_name.'<br/> '.$title.' / '.$author.' <br/>';
			if($mode == "D"){
			    $msg.='The rate imposed by the publisher '.$dealAmt.'<br/>';
			}else{
			   $msg.='The rate imposed by the publisher '.$emi_price.' /EMI <br/>';
			}
			$msg.='</p></td></tr></table>';
			$msg.='<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:30px 0px 30px 0px !important; padding:0px !important;color:#000 !important;">Regards,</p></td>
			</tr> 
			</table>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:600 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:0px 0px 0px 0px !important; padding:0px !important;color:#000 !important;">HELF Innovation </p></td>
			</tr> 
			</table>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:10px 0px 30px 0px !important; padding:0px !important;color:#000 !important; margin-bottom:0 !important;">
			The Digital Library<br/>
			<img style="margin-top:6px !important;width:150px;" src="'.base_url().'public/image/helf.png" />
			</p></td>
			</tr> 
			</table>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:10px 0px 0px 0px !important; padding:0px !important;color:#000 !important;">Email : <a href="mailto:support@helf.in" style="color:#000 !important; text-decoration:none !important">support@helf.in</a></p></td>
			</tr> 
			</table>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:10px 0px 0px 0px !important; padding:0px !important;color:#000 !important;">URL : <a href="http://helf.in/" style="color:#000 !important; text-decoration:none !important">www.helf.in</a></p></td>
			</tr> 
			</table>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:10px 0px 0px 0px !important; padding:0px !important;"><a href="https://www.facebook.com/helflibrary/?ref=bookmarks" style="color:#1a7dc6 !important; text-decoration:none !important">https://www.facebook.com/helflibrary/?ref=bookmarks</a></p></td>
			</tr> 
			</table>
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td><p style="font-size:14px !important; font-weight:500 !important; font-family:Arial, Helvetica, sans-serif !important;line-height:14px !important; margin:10px 0px 0px 0px !important; padding:0px !important;"><a href="#" style="color:#000 !important; text-decoration:none !important; font-weight:bold;">Contact: 9044408886 </a></p></td>
			</tr> 
			</table>
			<body>
			</body>
			</html>
		';
		$this->email->message($msg);
		$this->email->send();
	}
	
	public function round($amt){
		list($rs,$ps)=explode(".",$amt);
		if((int)$ps > 49){
			return $rs + 1;
		}else{
			return $rs;
		}
	}
	
	public function create_thumb(){
	    $this->load->helper('directory');
	    $dir='./uploads/library/thumb/';
	    $files = directory_map($dir);
	    print_r($map);
	    foreach($files as $file){
	        $source_path =  $dir . $file;
            $target_path =  $dir . '/200/';
	        if(!is_dir($source_path)){
                $config_manip = array(
                    'image_library' => 'gd2',
                    'source_image' => $source_path,
                    'new_image' => $target_path,
                    'maintain_ratio' => TRUE,
                    'width' => 200,
                );
                $this->load->library('image_lib');
                $this->image_lib->initialize($config_manip);
				if($this->image_lib->resize()){
        	        echo $file;
        	        echo "<br/>";
				}
                if(!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                // clear //
                $this->image_lib->clear();
	        }
        }
	}
}  
?>