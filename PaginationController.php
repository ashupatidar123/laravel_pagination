Code by Ashvin patidar

url route on web.php
Route::any('userList/{page?}','PaginationController@userList');


===================


<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\PaginationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class PaginationController extends Controller
{
	protected $pgModel;
	public function __construct(){
		parent::__construct();
		$this->pgModel = new PaginationModel();     	

  	}

	public function displayPagination($per_page,$page,$total,$page_url)
	{
	    $page_url = url($page_url); //url laravel base url
		    	
	    $adjacents = "2"; 

		$page = ($page == 0 ? 1 : $page);  
		$start = ($page - 1) * $per_page;								
		
		$prev = $page - 1;							
		$next = $page + 1;
	    $setLastpage = ceil($total/$per_page);
		$lpm1 = $setLastpage - 1;
		
		$setPaginate = "";
		if($setLastpage > 1)
		{	
			$setPaginate .= "<ul class='setPaginate'>";
	                $setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
			if ($setLastpage < 7 + ($adjacents * 2))
			{	
				for ($counter = 1; $counter <= $setLastpage; $counter++)
				{
					if ($counter == $page)
						$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
					else
						$setPaginate.= "<li><a href='{$page_url}/$counter'>$counter</a></li>";					
				}
			}
			elseif($setLastpage > 5 + ($adjacents * 2))
			{
				if($page < 1 + ($adjacents * 2))		
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}/$counter'>$counter</a></li>";					
					}
					$setPaginate.= "<li class='dot'>...</li>";
					$setPaginate.= "<li><a href='{$page_url}/$lpm1'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}/$setLastpage'>$setLastpage</a></li>";		
				}
				elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$setPaginate.= "<li><a href='{$page_url}/1'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}/2'>2</a></li>";
					$setPaginate.= "<li class='dot'>...</li>";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}/$counter'>$counter</a></li>";					
					}
					$setPaginate.= "<li class='dot'>..</li>";
					$setPaginate.= "<li><a href='{$page_url}/$lpm1'>$lpm1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}/$setLastpage'>$setLastpage</a></li>";		
				}
				else
				{
					$setPaginate.= "<li><a href='{$page_url}/1'>1</a></li>";
					$setPaginate.= "<li><a href='{$page_url}/2'>2</a></li>";
					$setPaginate.= "<li class='dot'>..</li>";
					for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
					{
						if ($counter == $page)
							$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
						else
							$setPaginate.= "<li><a href='{$page_url}/$counter'>$counter</a></li>";					
					}
				}
			}
			
			if ($page < $counter - 1){ 
				$setPaginate.= "<li><a href='{$page_url}/$next'>Next</a></li>";
	            $setPaginate.= "<li><a href='{$page_url}/$setLastpage'>Last</a></li>";
			}else{
				$setPaginate.= "<li><a class='current_page'>Next</a></li>";
	            $setPaginate.= "<li><a class='current_page'>Last</a></li>";
	        }

			$setPaginate.= "</ul>\n";		
		}
	    return $setPaginate;
	}

	public function userSearch($search)
	{
		$where='';
		if(!empty($userId=$search['userId'])){
			$where .= "AND user_id='$userId' ";
		}
		if(!empty($username=$search['username'])){
			$where .= "AND username LIKE '$username%' ";
		}
		if(!empty($name=$search['name'])){
			$where .= "AND Name LIKE '$name%' ";
		}
		if(!empty($mobile=$search['mobile'])){
			$where .= "AND Mobile='$mobile' ";
		}
		if(!empty($email=$search['email'])){
			$where .= "AND Email LIKE '$email%' ";
		}
		if(!empty($from_date=$search['from_date'])){
			$where .= "AND date(created_date)>='$from_date' ";
		}
		if(!empty($to_date=$search['to_date'])){
			$where .= "AND date(created_date)<='$to_date' ";
		}
		return $where;
	}


	public function userList($page='1',Request $req)
	{
		$search_page=0;
		if(isset($_POST['session_des'])){
			Session::forget('userSearch');
			return redirect('userList');
		}
		$where = "WHERE user_status=1 ";
		if(!empty($search = $req->input())){	
			Session::put('userSearch',$search);
			$where .= $this->userSearch($search);

			if(!empty($search['per_page'])){
				$search_page = $search['per_page'];
			}
		}
		else if(!empty(Session::get('userSearch'))){	
			$where .= $this->userSearch(Session::get('userSearch'));
			$pg = Session::get('userSearch');
			$search_page = $pg['per_page'];
		}
		
		$per_page = ($search_page>0)?$search_page:15;
		$start = ($page * $per_page) - $per_page;
		
		$total 	  = $this->pgModel->customCountQuery('users',$where);
		$userData = $this->pgModel->customFetchQuery('*',$tbl='users',$where,'ORDER BY user_id DESC',$limit="LIMIT $start,$per_page");

		$userData['record'] = $userData;
		$userData['pagination'] = $this->displayPagination($per_page,$page,$total,'webpanel/userList');
		//AdminController::print($userData);
		$userData['sno'] = ($page==1)?1:$page*$per_page-($per_page-1);
		$userData['total'] = $total;
		return view('userList',$userData);
	}

}