  @extends('admin.header')
  @section('title','Register Form')  
  
  @section('content')
 
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>User List</h1>
            <a href="{{url('webpanel/register')}}" class="btn btn-primary"><i class="fa fa-list"></i> Add User</a>            

            @if($alert = Session::get('alert'))
              <div class="alert alert-{{$alert}} alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                <strong>{{ Session::get('message') }}</strong>
              </div>
            @endif            
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
              <li class="breadcrumb-item active">List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <?php
        if(!empty(Session::get('userSearch'))){
          $srch = Session::get('userSearch');          
        }
        $per_page = !empty($srch['per_page'])?$srch['per_page']:'';
    ?>
    <!-- Main content -->
    <section class="content">
      <form method="post" action="{{url('webpanel/userList')}}">
        @csrf
        <div class="row">
          <div class="form-group col-md-2">
            <select class="form-control" name="per_page">
              <option value="" hidden="">Show record</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="40">40</option>
              <option value="60">60</option>
              <option value="100">100</option>
              <option value="150">150</option>
              <option value="300">300</option>
              <option value="500">500</option>
              <option value="1000">1000</option>
            </select>
          </div>

          <div class="form-group col-md-2">
            <input type="text" name="userId" class="form-control" placeholder="Enter user ID" autocomplete="off" value="{{empty($srch['userId'])?'':$srch['userId']}}" onkeypress="return onlyNumbers();">
          </div>
          <div class="form-group col-md-2">
            <input type="text" name="username" class="form-control" placeholder="Enter username" autocomplete="off" value="<?=empty($srch['username'])?'':$srch['username'];?>" onkeypress="return onlyLatters();">
          </div>
          <div class="form-group col-md-2">
            <input type="text" name="name" class="form-control" placeholder="Enter name" autocomplete="off" value="<?=empty($srch['name'])?'':$srch['name'];?>" onkeypress="return onlyLatters();">
          </div>
          <div class="form-group col-md-2">
            <input type="text" name="mobile" class="form-control" placeholder="Enter mobile" autocomplete="off" value="<?=empty($srch['mobile'])?'':$srch['mobile'];?>" onkeypress="return onlyNumbers('mobile');">
          </div>
          <div class="form-group col-md-2">
            <input type="text" name="email" class="form-control" placeholder="Enter email" autocomplete="off" value="<?=empty($srch['email'])?'':$srch['email'];?>">
          </div>
          <div class="form-group col-md-2">
            <input type="text" name="from_date" class="form-control" placeholder="Enter start date" autocomplete="off" value="<?=empty($srch['from_date'])?'':$srch['from_date'];?>">
          </div>
          <div class="form-group col-md-2">
            <input type="text" name="to_date" class="form-control" placeholder="Enter end date" autocomplete="off" value="<?=empty($srch['to_date'])?'':$srch['to_date'];?>">
          </div>
          <div class="form-group col-md-2">
            <button type="submit" class="btn btn-info" title="Search record"><i class="fa fa-search"></i></button>
            <button class="btn btn-danger" type="submit" name="session_des" title="Show all record"><i class="fas fa-undo-alt"></i></button>
          </div>
        </div>  
      </form>


      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title text-danger">
                Total users: ({{$total}})
              </h3>
            </div>
            <div class="card-body table-responsive tableFixHead">
              
              <table id="example12" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sno</th>
                  <th>User ID</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Register Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <body>
                  <?php   
                  //$var = new AdminController();
                  if(!empty($record)):
                  $sno;
                  foreach($record as $row)
                  { 
                      $user_id = $row->user_id;
                      $userId  = App\Http\Controllers\CoreController::id_string($user_id);
                      //$userId  = $var->id_string1($user_id);
                    ?>
                    <tr>
                      <td>{{$sno++}}</td>
                      <td>{{$row->user_id}}</td>
                      <td>{{$row->Name}}</td>
                      <td>{{$row->username}}</td>
                      <td>{{$row->Mobile}}</td>
                      <td>{{$row->Email}}</td>
                      <td>{{$row->created_date}}</td>
                      <td>
                        <a href="{{url('webpanel/editUser')}}/{{$userId}}/userList" class="btn btn-sm btn btn-primary"><i class="fa fa-edit"></i></a>
                        <a href="javascript:void(0);" class="btn btn-sm btn btn-danger" onclick="recordDelete('{{$userId}}','userList');"><i class="fa fa-trash"></i></a>

                      </td>
                    </tr>
                  <?php
                  } endif;?>  
                </body>
              </table>
              <?php
              if(empty($record)){?>
                <p class="alert alert-danger text-center">No record found...</p>
              <?php } ?>
            </div>  
            <p>
                <?php
                    if(!empty($pagination))
                      echo $pagination;
                ?>

            </p>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <script type="text/javascript">
    $(document).ready(function(){
      var per_page = '{{$per_page}}';
      $('select[name="per_page"]').val(per_page);
    });
  </script>

  @endsection