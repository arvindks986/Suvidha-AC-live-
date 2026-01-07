<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Candidate & Counting  Management System</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="stylesheet" href="{{ asset('theme/css/sumoselect.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap-datetimepicker.css')}}"> 
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="{{ asset('theme/vendor/font-awesome/css/font-awesome.min.css') }}">    <!-- Fontastic Custom icon font-->
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{ asset('theme/css/fontastic.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/table.css') }}">     <!-- Google fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">    <!-- jQuery Circle-->
    <link rel="stylesheet" href="{{ asset('theme/css/grasp_mobile_progress_circle-1.0.0.min.css') }}">    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="{{ asset('theme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">    
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('theme/css/style.red.css') }}" id="theme-stylesheet">    
	<link rel="stylesheet" href="{{ asset('theme/css/daterangepicker.css') }}">    
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{ asset('theme/css/custom.css') }}">    <!-- Favicon-->
    <link rel="shortcut icon" href="{{ asset('theme/img/favicon.ico') }}">
    <link rel="stylesheet" href="{{asset('theme/css/loadingbar.css')}}"> 

    <link rel="stylesheet" href="{{asset('theme/css/slicknav.css')}}">    <!-- Favicon-->
    <link rel="stylesheet" href="{{asset('theme/css/jquery.stickytable.css')}}">    <!-- Favicon-->
   
    <link rel="stylesheet" href="{{asset('theme/css/poll.css')}}">    <!-- Favicon-->
    <link href="{!! url('admintheme/css/jquery.toast.css') !!}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('admintheme/css/animate.css') }}">
 <link rel="stylesheet" href="{{ asset('theme/css/progressbar.css') }}"> 
    
<script>
       window.Laravel = <?php echo json_encode([
           'csrfToken' => csrf_token(),
       ]); ?>
   </script>

   <script type="text/javascript">
       var APP_URL = {!! json_encode(url('/')) !!}
       var csrf  = <?php echo json_encode([ csrf_token(),]); ?>
   
   </script>
    <style type="text/css">
      
      <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
      
     
      div.dataTables_wrapper {margin:0 auto;} 
  </style>
  <script type="text/javascript" src="{{ asset('theme/js/jquery.min.js') }}"></script>
</head>

<body class="d-flex flex-column h-100">
<!--  <div class="border-top"></div> -->
      @include('admin/central/common/header')
      @if(Auth::user())
	  @include('admin/central/common/breadcrumb')
      @endif
      <!--main content start-->
      @yield('content')
      <!--main content end-->
	  @include('admin/central/common/footer')
 

    
    <script src="{{ asset('theme/vendor/popper.js/umd/popper.min.js') }}"> </script>
    <script src="{{ asset('theme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('theme/js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
    <script src="{{ asset('theme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
       
    <!-- Main File-->
    <script src="{{ asset('theme/js/front.js') }}"></script>
    <script type="text/javascript" src="{{ asset('theme/js/dataTables.fixedColumns.min.js') }}"></script>
    <script src="{{ asset('theme/js/moment.min.js') }}"  type="text/JavaScript"></script> 
	
	<script type="text/javascript" src="{{asset('theme/js/daterangepicker.js') }}"></script>
  <!--  <script src="{{ asset('theme/js/bootstrap-datepicker.js') }}"  type="text/JavaScript"></script>  -->
    <script src="{{ asset('theme/js/bootstrap-datetimepicker.js') }}"  type="text/JavaScript"></script>
    <script src="{{ asset('js/common.js')}}" type="text/JavaScript"></script>  
    <script type="text/javascript" src="{{ asset('theme/js/jquery.slicknav.js') }}"></script>
    <script type="text/javascript" src="{{ asset('theme/js/slider-menu.jquery.js') }}"></script>
  
     
 
    <script type="text/javascript" src="{{ asset('theme/js/utils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('theme/js/jquery.stickytable.js') }}"></script>
    <script src="{{ url('theme/js/jquery.toast.js') }}"></script>
    <script src="{{ url('theme/js/jquery.loadingbar.js') }}"></script>
	 <script src="{{ url('theme/js/progressbar.js') }}"></script>

    <script type="text/javascript" src="{{ asset('theme/js/sumoselect.min.js') }}"></script>
    <script src="{{ asset('theme/vendor/chart.js/Chart.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('theme/js/chartjs-plugin-labels.js') }}"></script>
    
<script>
$(document).ready(function() {
    var table = $('#example').DataTable({       
        scrollX:        true,
        scrollCollapse: true,
        paging:         true,
        fixedColumns:   {
            leftColumns: 3,
            rightColumns: 1
        }
    });
  
  $('.datepicker').datetimepicker({
       format: "D-M-YY"
  });
      
      

 $('.affidavit').bind('change', function () {
  var filename = $(".affidavit").val();
  if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen..."); 
  }
  else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", "")); 
      }
});
  
  
  
}); 
</script> 
  
<script>$(document).ready(function() {
    $('#example').DataTable();
    $('#list-table,#list-table1,#list-table2,#list-table3').DataTable();
  
  });
</script>
<script type="text/javascript">
if($(window).width() < 767)
{
   $(document).ready(function(){
 $('.datatable').wrap('<div class="table-responsive"></div>');
});
} else {
   $(document).ready(function(){
 $('.datatable').wrap('<div class="sticky-table sticky-ltr-cells"></div>');
});
}
</script>
<script type="text/javascript">
if($(window).width() < 767)
{
   $(document).ready(function(){
 $('.table').wrap('<div class="table-responsive"></div>');
});
} else {
  
}


</script>
<script type="text/javascript">
// in this demo, it is triggered by a click event
// you may use any trigger in your apps
$("#show").click(function() {
  if ($("#progress").length === 0) {
    // inject the bar..
    $("body").append($("<div><b></b><i></i></div>").attr("id", "progress"));
    
    // animate the progress..
    $("#progress").width("101%").delay(800).fadeOut(1000, function() {
      // ..then remove it.
      $(this).remove();
    });  
  }
});
</script>
<script type="text/javascript">
   $(function () {
                $('.datetimepicker').datetimepicker();
            });
</script>
<script type="text/javascript">

var x, i, j, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("custom-select");
for (i = 0; i < x.length; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];
        /*for each element, create a new DIV that will act as the selected item:*/
        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        x[i].appendChild(a);
        /*for each element, create a new DIV that will contain the option list:*/
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < selElmnt.length; j++) {
              /*for each option in the original select element,
              create a new DIV that will act as an option item:*/
              c = document.createElement("DIV");
              c.innerHTML = selElmnt.options[j].innerHTML;
              c.addEventListener("click", function(e) {
              /*when an item is clicked, update the original select box,
              and the selected item:*/
              var y, i, k, s, h;
              s = this.parentNode.parentNode.getElementsByTagName("select")[0];
              h = this.parentNode.previousSibling;
        for (i = 0; i < s.length; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
                s.selectedIndex = i;
                h.innerHTML = this.innerHTML;
                y = this.parentNode.getElementsByClassName("same-as-selected");
            for (k = 0; k < y.length; k++) {
                y[k].removeAttribute("class");
            }
                this.setAttribute("class", "same-as-selected");
                break;
          }
        }
              h.click();
    });
          b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
}
function closeAllSelect(elmnt) {
  /*a function that will close all select boxes in the document,
  except the current select box:*/
  var x, y, i, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  for (i = 0; i < y.length; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < x.length; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}
/*if the user clicks anywhere outside the select box,
then close all select boxes:*/
document.addEventListener("click", closeAllSelect);

</script>
<script>
	$(function(){$('.mainmenu').slicknav();	});	
</script>
<script>
( function( $ ) {
  $( function() {
    $( '.mainmenu' ).sliderMenu();
  });
})( jQuery );
</script>
<script type="text/javascript">
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }

    function openNavR() {
        document.getElementById("mySidenavR").style.width = "250px";
    }

    function closeNavR() {
        document.getElementById("mySidenavR").style.width = "0";
    }
</script>
 <script>  window.fwSettings={  'widget_id':44000000524  };  !function(){if("function"!=typeof window.FreshworksWidget){var n=function(){n.q.push(arguments)};n.q=[],window.FreshworksWidget=n}}() </script> 
<script src="{{ asset('theme/js/bootstrap.js') }}"> </script>
@include('admin/common/supporting-header')
@yield('script')


</body>
</html>