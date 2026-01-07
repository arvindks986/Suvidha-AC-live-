<div class="fullwidth">
<div class="container">
<div class="fullwidth">
@if(Session::has('flash-message'))
                @if(Session::has('status'))
                    <?php
                    $status = Session::get('status');
                    if($status==1){
                     $class = 'alert-success';
                    }
                    else{
                        $class = 'alert-danger';
                    }
                    ?>
                @endif
                <div class="alert <?php echo $class; ?> fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ Session::get('flash-message') }}
                </div>
            @endif




            @if(Session::has('flash_column_name'))
           
                <div class="alert alert-warning fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ Session::get('flash_column_name') }}
                </div>
            @endif


            </div>
            </div>
            </div>




            