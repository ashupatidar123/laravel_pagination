<?php

namespace App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminModel extends Model
{
	public function print()
	{
		
		$queries = DB::getQueryLog();
		echo "<pre>";
		print_r($queries);
		echo "</pre>";
		dd();
	}

	public function countQuery($tbl,$where=NULL)
	{
		DB::enableQueryLog();
		$query = DB::table($tbl);
		if(!empty($where)){
			$query->where($where);
		}

		//$query->count();
		//AdminModel::print();
		return $query->count();
	}

	public function fetchQuerySingle($tbl,$select,$where=NULL,$orderName=NULL,$ascDESC=NULL,$or_where=NULL,$start=NULL,$end=NULL)
	{
		DB::enableQueryLog();
		$query = DB::table($tbl);
		$query->select($select);
		if(!empty($where))
			$query->where($where);
		if(!empty($or_where))
			$query->orWhere($or_where);			
		if(!empty($orderName))
			$query->orderBy($orderName,$ascDESC);
		return $query->first();
		$query->first();
		AdminModel::print();
	}

	public function fetchQuery($tbl,$select,$where=NULL,$or_where=NULL,$orderName=NULL,$ascDESC=NULL,$groupBy=NULL,$start=NULL,$end=NULL)
	{
		DB::enableQueryLog();
		
		$query = DB::table($tbl);
		$query->select($select);
		if(!empty($where))
			$query->where($where);
		if(!empty($or_where)){
			$query->orWhere($or_where);			
		}
		if(!empty($groupBy))
			$query->groupBy($groupBy);
		if(!empty($orderName))
			$query->orderBy($orderName,$ascDESC);
		if($end>0)
			offset($start)->limit($end); //offset 0,limit 10 page2 10,10 page3 20,10
		
		//$query->get()->toArray();
		//AdminModel::print();
		return $query->get()->toArray();
	}

	public function customFetchQuery($select='*',$tbl='',$where='',$orderBy='', $limit='',$groupBY='')
	{	
		$query = "SELECT $select FROM $tbl $where $groupBY $orderBy $limit";
		$data = DB::select($query);
		//echo $query; //die();
		//return json_decode(json_encode($data), True);
		return $data;
	}

	public function customCountQuery($tbl,$where=NULL)
	{
		$query = "SELECT count(*) as total FROM $tbl $where";
		$data = DB::select($query);
		return $data[0]->total;		
	}

	public function insertQuery($tbl,$data){
		$id = DB::table($tbl)->insertGetId($data);
		return $id;
	}

	public function updateQuery($tbl,$data,$where=''){
		
		$query = DB::table($tbl);
		if(!empty($where)){
			$query->where($where)->limit(1);
		}
		$query->update($data);		
		return true;
	}

	public function deleteQuery($tbl,$where=''){		
		$query = DB::table($tbl);
		if(!empty($where)){
			$query->where($where);
		}
		return $query->delete();		
	}

} // PaginationModel class closed