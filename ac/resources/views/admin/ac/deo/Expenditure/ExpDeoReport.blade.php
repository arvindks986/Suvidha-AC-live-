@extends('admin.layouts.ac.report-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Scrutiny Details Form')
@section('description', '')
@section('content') 
<main role="main" class="inner cover mb-3">

    <!--FILTER STARTS FROM HERE--
    <div class="card-header pt-3">
    <div class="container-fluid">
    <div class="row pt-3 pb-3">
    <div class="col-sm-12 text-center"><h4><b> Welcome DEO AJMER</b></h4></div>
    </div>
    </div>
    <form method="get" action="http://localhost/suvidha/public/eci/expentiture_listing" id="EciCustomReportFilter" novalidate="novalidate">
            <div class=" row">
                <div class="col-sm-3">
                    <label for="district_id"> Name of State</label>
                    <input type="text" disabled="" placeholder="Rajasthan" class="form-control">
                </div>
                <div class="col-sm-3">
                    <label for="district_id"> Name of District</label>
                    <input type="text" disabled="" placeholder="AJMER" class="form-control">
                </div>
                <div class="col-sm-3">
                    <label for="district_id"> Election</label>
                    <input type="text" disabled="" placeholder="Assembly Election 2019" class="form-control">
                </div>
                <div class="col-sm-3">
                    <label for=""> Select Constituency</label>
                    <select id="Select_Constituency" name="district_id" class="form-control" onchange="myFunction()">
                        <option value="" selected=""> Select Constituency</option>
                        <option value="Kishangarh-98"> Kishangarh-98</option>
                        <option value="Option 2"> Option 2</option>
                        <option value="Option 3"> Option 3</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!--FILTER ENDS HERE-->

    <!--<section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="card text-left" style="width:100%;">
                 <!--SELECT CANDIDATE--
                <div id="select_candidate">

                    <div class=" card-header">
                        <div class=" row d-flex align-items-center">
                            <div class="col"><h4> Select Candidate</h4></div>
                        </div>
                    </div>

                    <div class="card-body">  
                        <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                 <!-- <th>Serial No</th> --
                                    <th>Sr. No</th> 
                                    <th>Candidate Name</th>
                                    <th>Party Name</th> 
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td>1.</td>
                                    <td><a href="javascript:void(0)" id="nathuram">Nathu Ram Sinodiya</a></td>
                                    <td>IND</td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td><a href="javascript:void(0)">Sajjan Kanwar</a></td>
                                    <td>BYS</td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td><a href="javascript:void(0)">Gopal Maheshwari</a></td>
                                    <td>ARJP</td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td><a href="">Gopal Sharma</a></td>
                                    <td>HSS</td>
                                </tr>
                                <tr>
                                    <td>5.</td>
                                    <td><a href="">Taruna Sharma</a></td>
                                    <td>BlockSP</td>
                                </tr>
                                <tr>
                                    <td>6.</td>
                                    <td><a href="">Bhawani Singh Khangarot</a></td>
                                    <td>IND</td>
                                </tr>
                                <tr>
                                    <td>7.</td>
                                    <td><a href="">Nanda Ram</a></td>
                                    <td>INC</td>
                                </tr>
                                <tr>
                                    <td>8.</td>
                                    <td><a href="">Dr. Mahesh Kumbhaj</a></td>
                                    <td>IND</td>
                                </tr>
                                <tr>
                                    <td>9.</td>
                                    <td><a href="">Umrav Choudhary</a></td>
                                    <td>AAP</td>
                                </tr>
                                <tr>
                                    <td>10.</td>
                                    <td><a href="">Matadeen</a></td>
                                    <td>IND</td>
                                </tr>
                                <tr>
                                    <td>11.</td>
                                    <td><a href="">Vikash Choudhary</a></td>
                                    <td>BJP</td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
   
                </div>
                <!--END OF SELECT CANDIDATE-->

<!--CANDIDATE REPORT-->
<div id="candidate_report">

        <div class=" card-header">
            <div class=" row d-flex align-items-center">
                <div class="col-sm-10"><h4> DEO's Scrutiny Report on Election Expenses of the Candidate Under Rule 89 of C.E. Rules, 1961</h4></div>
                <div class="col-sm-2"><p class="mb-0 text-right">
                <button type="button" id="Back" class="btn btn-primary">Back</button>
                </p></div>
            </div>
        </div>

            <div class="card-body">  
                <!-- Nav tabs -->
            <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#home">Account Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu1">Defects In Format</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu2">Expense Understated</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu3">Fund Given By Political Party/Other Sources</a>
            </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
            <!-- ACCOUNT DETAILS FORM -->
            <div class="tab-pane container active" id="home">
            <div class="row pt-3 pb-3">
                <div class="col-sm-10 text-center"><h6> Account Details Of Nathu Ram Sinodiya</h6></div>
                <div class="col-sm-2"><p class="mb-0 text-right">
                <input type="button" value="Edit Details" name="Cancel" class="btn mb-2">
                </p></div>
                
                <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                <form method="get" action="http://localhost/suvidha/public/eci/expentiture_listing" id="EciCustomReportFilter" novalidate="novalidate">
                            <thead>
                                <tr>
                                 <!-- <th>Serial No</th> -->
                                    <th>Sr. No</th> 
                                    <th>Description</th>
                                    <th>To be Filled up by the DEO</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><label for=""> 1.</label></td>
                                    <td><label for=""> Name & Address of the Candidate</label></td>
                                    <td><label for="">Nathu Ram Sinodiya, Village Sinodiya, Tehsil Roopangarh, Dist. Ajmer Sinodiya</td>
                                </tr>
                                <tr>
                                    <td><label for=""> 2.</label></td>
                                    <td><label for=""> Political Party Affliation, If Any</label></td>
                                    <td><input type="text" disabled="" placeholder="Independant" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 3.</label></td>
                                    <td><label for=""> No. and Name of Assembly/Parliamentry Constituency </label></td>
                                    <td><input type="text" disabled="" placeholder="Kishangarh-98" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 4.</label></td>
                                    <td><label for=""> Name of the Elected Candidate</label></td>
                                    <td><input type="text" disabled="" placeholder="Shankar Patil Munenakoppa" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 5.</label></td>
                                    <td><label for=""> Date of Declaration of Result</label></td>
                                    <td><input type="date" disabled="" name="date_of_receipt" id="date_of_receipt" class="form-control" placeholder="Date &amp; time"></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 6.</label></td>
                                    <td><label for=""> Date of Account Reconciliation Meeting</label></td>
                                    <td><input type="date" disabled="" name="date_of_receipt" id="date_of_receipt" class="form-control" placeholder="Date &amp; time"></td>
                                </tr>
                                <tr>
                                    <td rowspan="2"><label for=""> 7.</label></td>
                                    <td><label for="">(i) Whether the Candidate or his Agent had been informed about the Date of Account Reconciliation Meeting in writing</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></td>
                                </tr>
                                <tr>
                                <td><label for="">(ii) Whether he or his Agent has attended the Meeting</label></td>
                                <td>
                                    <select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> 8.</label></td>
                                    <td><label for="">Whether all the defects Reconciled by the Candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 19)</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 9.</label></td>
                                    <td><label for=""> Last Date Prescribed for Lodging Account</label></td>
                                    <td><input type="date" disabled="" name="date_of_receipt" id="date_of_receipt" class="form-control" placeholder="Date &amp; time"></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 10.</label></td>
                                    <td><label for=""> Whether the Candidate has Lodged the Account</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td rowspan="3"><label for=""> 11.</label></td>
                                    <td><label for=""> If the Candidate has Lodged the Account, Date of Lodging of Account by the Candidate:</label></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><label for="">(i) Original Account</label></td>
                                    <td><input type="date" disabled="" name="date_of_receipt" id="date_of_receipt" class="form-control" placeholder="Date &amp; time"></td>
                                </tr>
                                <tr>
                                    <td><label for="">(ii) Revised Account after the Account Reconciliation Meeting</label></td>
                                    <td><input type="date" disabled="" name="date_of_receipt" id="date_of_receipt" class="form-control" placeholder="Date &amp; time"></td>
                                </tr>

                                <tr>
                                    <td><label for=""> 12.</label></td>
                                    <td><label for=""> Whether Account Lodged in Time</label></td>
                                    <td>
                                    <select name="return_status" id="return_status" class="form-control">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 12A.</label></td>
                                    <td><label for=""> If not Lodged in Time, Period of Delay</label></td>
                                    <td>          
                                    <div class="d-flex">
                                    <input type="text" disabled="" placeholder="N/A" class="form-control">
                                    <label class="mt-2 ml-2">In&nbsp;Days</label>
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> 13.</label></td>
                                    <td><label for="" class="mr-3"> If Account not Lodged or not Lodged in Time, Whether DEO called for Explanation from the Candidate.
                                    If not, reason thereof.</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 14.</label></td>
                                    <td><label for=""> Explanation, if any, given by the Candidate</label></td>
                                    <td><textarea disabled="" placeholder="N/A" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 14A.</label></td>
                                    <td><label for=""> Comments of the DEO on the Explanation if any, of the Candidate</label></td>
                                    <td><textarea disabled="" placeholder="N/A" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea></td>
                                </tr>
                                <tr>
                                    <td><label for=""> 15.</label></td>
                                    <td><label for=""> Grand Total of all Election Expenses Reported by the Candidate in Part-II of the Abstract Statement</label></td>
                                    <td>
                                        <div class="d-flex">
                                        <input type="text" disabled="" placeholder="12100" class="form-control">
                                        <label class="mt-2 ml-2">In&nbsp;Rupees</label>
                                        </div>
                                    </td>
                                </tr>
                                
                            </tbody>
                            <tfoot>
                        <tr>
                            <td colspan=3 class="text-center" align="center">
                            <label for="">&nbsp;</label>
                                <div><input type="submit" value="UPDATE" class="btn btn-primary"></div>
                            </td>
                        </tr>
                    </tfoot>
            </form>
            </table>
            </div>
            </div>
            <!-- END OF ACCOUNT DETAILS FORM -->

            <!-- DEFECTS IN FORMAT FORM -->
            <div class="tab-pane container fade" id="menu1">
            <div class="row pt-3 pb-3">
                <div class="col-sm-10 text-center"><h6> Defects In Format of Nathu Ram Sinodiya</h6></div>
                <div class="col-sm-2"><p class="mb-0 text-right">
                <input type="button" value="Edit Details" name="Cancel" class="btn mb-2">
                </p></div>
                <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                <form method="get" action="http://localhost/suvidha/public/eci/expentiture_listing" id="EciCustomReportFilter" novalidate="novalidate">
                            <thead>
                                <tr>
                                 <!-- <th>Serial No</th> -->
                                    <th>Sr. No</th> 
                                    <th>Description</th>
                                    <th>To be Filled up by the DEO</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><label for=""> 16.</label></td>
                                    <td><label for=""> Whether in the DEO's Opinion, the Account of Election Expenses of the Candidate has been Lodged
                                    <br />in the manner required by the R.P. Act 1951 and C.E. Rules, 1961.</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select></td>
                                </tr>
                                <tr>
                                    <td rowspan="6"><label for=""> 17.</label></td>
                                    <td><label for=""> If No, then please mention the following defects with details</label></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><label for=""> (i) Whether Election Expenditure Register Comprising of the Day to Day Account Register,
                                    <br />Cash Register, Bank Register, Abstract Statement has been Lodged</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> (ii) Whether duly sworn in affidavit has been submitted by the Candidate</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> (iii) Whether requisite Vouchers in respect of items of Election Expenditure Submited</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> (iv) Whether  seprate Bank Account Opened by for Election</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> (v) Whether all Expenditure (Except petty Expenditure) routed through bank Account</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3"><label for=""> 18.</label></td>
                                    <td><label for=""> (i) Whether the DEO had issued a notice to the Candidate for Rectifying the Defect</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> (ii) Whether the Candidate Rectified the Defect</label></td>
                                    <td><select name="return_status" id="return_status" class="form-control">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                    <textarea disabled="" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label for=""> (iii) Comments of the DEO on the above, i.e. whether the defect was rectified or not.</td>
                                    <td><textarea disabled="" placeholder="N/A" class="form-control mt-2" id="exampleFormControlTextarea1" rows="2"></textarea></td>
                                </tr>
                            </tbody>
                            <tfoot>
                        <tr>
                            <td colspan=3 class="text-center" align="center">
                            <label for="">&nbsp;</label>
                                <div><input type="submit" value="UPDATE" class="btn btn-primary"></div>
                            </td>
                        </tr>
                    </tfoot>
            </form>
            </table>
            

            </div>

            </div>
            <!-- END DEFECTS IN FORMAT FORM -->

            <!-- EXPENSE UNDERSTAND FORM -->

            <div class="tab-pane container fade" id="menu2">
            <div class="row pt-3 pb-3">
                <div class="col-sm-10 text-center"><h6> Expense Understand of Nathu Ram Sinodiya</h6></div>
                <div class="col-sm-2"><p class="mb-0 text-right">
                <input type="button" value="Edit Details" name="Cancel" class="btn mb-2">
                </p></div> 
                <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                <form method="get" action="http://localhost/suvidha/public/eci/expentiture_listing" id="EciCustomReportFilter" novalidate="novalidate">
                    <thead>
                        <tr>
                        <th>S. No.</th>
                        <th>Discription</th>   
                        <th>To be filled up by the DEO</th>       
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>19</td>
                            <td><label>Whether the items of Election Expenses Reported by the Candidate correspond with the Expenses shown in the Shadow Observation Register and Folder of Evidance.
                             If no then mention the following.</label></td>    
                            <td> 
                                <select class="form-control" id="exampleFormControlSelect1">
                                <option>Yes</option>
                                <option>No</option>                                
                                </select>                            
                            </td>      
                        </tr>
                        <tr>
                            <td colspan="3">
                                <table width="100%" CELLPADDING="0">
                                    <thead>
                                        <tr>
                                            <th>Item&nbsp;of&nbsp;Expenditure&nbsp;</th>
                                            <th>Date</th>   
                                            <th>Page no of Shadow Oburvation Register / folder of evidence</th>  
                                            <th>Mention amount as per the shadow observation register/ folder of evidence</th>
                                            <th>Amount as per the account submitted by the candidate</th>   
                                            <th>Amount understated by the Candidate </th> 
                                            <th>Description</th>
                                            <th>Action</th>     
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan='8'>No Records</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <select class="form-control" id="exampleFormControlSelect1">
                                                    <option>Expenditure</option>
                                                    <option>Expenditure</option>                                
                                                </select> 
                                            </td>
                                            <td><input type="text" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" class="form-control" value="" placeholder="" disabled></td>
                                            <td><textarea disabled="" placeholder="" class="form-control" id="exampleFormControlTextarea1" rows="1"></textarea></td>
                                            <td>
                                            <div class="d-flex">
                                            <button class="btn btn-success">Submit</button> &nbsp;<button class="btn btn-danger">Cancel</button>
                                            </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                                  
                        </tr>
                        <tr>
                            <td>20</td>
                            <td> Did the Candidate produce his Register of the Accounting Election Expenditure Register for Inspection by the Observer/RO/Authorized persons 3 times during Campaign Period</td> 
                            <td>                                
                                <select class="form-control" id="exampleFormControlSelect1">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>                                
                                </select>  
                                <div class="mt-2"></div>                             
                                <textarea class="form-control" rows="2" id="exampleFormControlTextarea1" disabled></textarea>
                                                                                       
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="6">21</td>
                            <td></label>If DEO does not agree with the facts Mentioned aginast Row No. 19 referred to above, give the following Details </label></td>    
                            <td></td>      
                        </tr>
                        <tr>                            
                            <td>(i) Were the defects notice by the DEO brought to the notice of the Candidate during Campaign Period or during the Account Reconcialation Meeting</td>    
                            <td>                        
                                <select class="form-control" id="exampleFormControlSelect1">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>                                
                                </select> 
                                <div class="mt-2"></div>
                                <textarea class="form-control" rows="2" id="exampleFormControlTextarea1" disabled></textarea>
                                                                                      
                            </td>      
                        </tr>
                        <tr>                            
                            <td>(ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice. </td>    
                            <td><textarea  class="form-control" rows="2" disabled></textarea></td>      
                        </tr>
                        <tr>                            
                            <td>(iii) Did the Candidate give any reply to the Notice ?</td>    
                            <td>                             
                                <select class="form-control" id="exampleFormControlSelect1">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>                                
                                </select> 
                                <div class="mt-2"></div>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" disabled></textarea>                                                                                      
                            </td>         
                        </tr>
                        <tr>                            
                            <td>(iv) If Yes, please Annex copies of such Explanation received, (With the English translation of the same, if it is in regional language) and mention Date of Reply</td>    
                            <td><textarea class="form-control" id="exampleFormControlTextarea1" rows="2" disabled></textarea></td>      
                        </tr>
                        <tr>                            
                            <td>(V) DEO's Comments/Observations on the Candidate's Explanation </td>    
                            <td><textarea class="form-control" id="exampleFormControlTextarea1" rows="2" disabled></textarea></td>      
                        </tr>
                        <tr> 
                            <td>22</td>                           
                            <td>Whether the DEO Agrees that the Expenses are correctly Reported by the Candidate. should be similar to Column no. 8 of Summary Repods of DEO</td>    
                            <td>
                                <select class="form-control" id="exampleFormControlSelect1">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>                                
                                </select> 
                                <div class="mt-2"></div>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" disabled></textarea>
                            </td>      
                        </tr>

                        
                        <tr>
                            <td>23</td>
                            <td><label>Comments, If Any by the Expenditure Observer*</label></td>
                            <td><textarea class="form-control" id="exampleFormControlTextarea1" palceholder="N/A" rows="2" disabled></textarea></td>
                        </tr>
                        <tr>
                            <td colspan='3'></td>                           
                        </tr>
                        <tr>
                            <td colspan='3'>
                            <p>* If the Expenditure Observer has some more facts that have not been covered in the DEO's Report, he may annex seprate to that effect.</p>
                            <p>** The DEO scrutiny report is to be complited by the CEO and forwarded to the Commission.</p>
                            <p> If the CEO feels like given additional comments, he or she may forward the comments separately.</p>
                            </td>                           
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan=3 class="text-center" align="center">
                            <label for="">&nbsp;</label>
                                <div><input type="submit" value="UPDATE" class="btn btn-primary"></div>
                            </td>
                        </tr>
                    </tfoot>
                    </form>
                    </table>
                
            </div>
            
            </div>

            <!-- END OF EXPENSE UNDERSTAND FORM -->

            <!-- FUND GIVEN BY POLITICAL PARTY FORM -->
            <div class="tab-pane container fade" id="menu3">
            <div class="row pt-3 pb-3">
                <div class="col-sm-10 text-center"><h6> Fund Given by Political Party/Other Sources of Nathu Ram Sinodiya</h6></div>
                <div class="col-sm-2"><p class="mb-0 text-right">
                <input type="button" value="Edit Details" name="Cancel" class="btn mb-2">
                </p></div> 
                <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                <form method="get" action="http://localhost/suvidha/public/eci/expentiture_listing" id="EciCustomReportFilter" novalidate="novalidate">
                    <thead>
                        <tr>
                        <th colspan="2" class="text-center" color="#ffffff">Fund Given By Political Party</th>
                        <tr>    
                    </thead>
                    <tbody>
                            <tr>
                                <td><label class="text-right">By Cash</label></td>
                                <td><input type="text" placeholder="0" class="form-control" disabled></td>
                            </tr>
                            <tr>
                                <td><label class="text-right">By Cheque</label></td>
                                <td><input type="text" placeholder="0" class="form-control" disabled></td>
                            </tr>
                            <tr>
                                <td><label class="text-right">By In Kind</label></td>
                                <td><input type="text" placeholder="0" class="form-control" disabled></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">                                     
                                    <button class="btn btn-primary btn-lg">Update </button>
                                </td>
                                
                            </tr>
                        </tbody>

                        
                    <h4 class="head-bg">Panel with panel-success class</h4>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Mode of Payment</th>
                                <th>Amount</th>
                                <th>Operation</th>
                            <tr>    
                        </thead>
                        <tbody>
                            <tr>
                                <td><label>No Record Found</td>
                                <td colspan="4"><label>&nbsp;</label></td>
                            </tr> 
                            <tr>
                                <td></td>
                                <td><input type="text" class="form-control" placholder="0"></td>
                                <td><select class="form-control" id="exampleFormControlSelect1">
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="By In Kind">By In Kind</option>                                
                                </select> 
                                </td>
                                <td><input type="text" class="form-control" placholder="0"></td>
                                <td><button class="btn btn-primary">Save </button> <button class="btn btn-primary">Cancel </button> </td>
                                
                            </tr>   
                        </tbody>    
                    </table> 
                </form>
                </table>
            </div>
            
            </div>
            <!-- END OF FUND GIVEN BY POLITICAL PARTY FORM -->
            </div>
            </div>

    </div>
        <!--END OF CANDIDATE REPORT-->
                    
            </div>

            </div>
        </div>
    </section>
</main>


<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">


                                        //*******************EXTRA VALIDATION METHODS STARTS********************//
                                        //maxsize
                                        $.validator.addMethod('maxSize', function (value, element, param) {
                                            return this.optional(element) || (element.files[0].size <= param)
                                        });
                                        //minsize
                                        $.validator.addMethod('minSize', function (value, element, param) {
                                            return this.optional(element) || (element.files[0].size >= param)
                                        });
                                        //alphanumeric
                                        $.validator.addMethod("alphnumericregex", function (value, element) {
                                            return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
                                        });
                                        //alphaonly
                                        $.validator.addMethod("onlyalphregex", function (value, element) {
                                            return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
                                        });
                                        //without space
                                        $.validator.addMethod("noSpace", function (value, element) {
                                            return value.indexOf(" ") < 0 && value != "";
                                        }, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//

//*******************ECI FILTER FORM VALIDATION STARTS********************//
                                        $("#EciCustomReportFilter").validate({
                                            rules: {
                                                state: {required: true, noSpace: true},
                                                ScheduleList: {number: true},
                                            },
                                            messages: {
                                                state: {
                                                    required: "Select state name.",
                                                    noSpace: "State name must be without space.",
                                                },
                                                ScheduleList: {
                                                    number: "Scedule ID should be numbers only.",
                                                },
                                            },
                                            errorElement: 'div',
                                            errorPlacement: function (error, element) {
                                                var placement = $(element).data('error');
                                                if (placement) {
                                                    $(placement).append(error)
                                                } else {
                                                    error.insertAfter(element);
                                                }
                                            }
                                        });
//********************ECI FILTER FORM VALIDATION ENDS********************//

</script>
<script>
function myFunction() {
    
            var selector = document.getElementById('Select_Constituency');
            var valueBox = selector[selector.selectedIndex].value;  
            if(valueBox){                        
                document.getElementById("select_candidate").style.display = "block";               
            }else{
                document.getElementById("select_candidate").style.display = "none";
                document.getElementById("candidate_report").style.display = "none";
            }        
        }
        document.getElementById("nathuram").addEventListener("click", function(){
            document.getElementById("select_candidate").style.display = "none";
            document.getElementById("candidate_report").style.display = "block";

        });
        document.getElementById("Back").addEventListener("click", function(){
            document.getElementById("select_candidate").style.display = "block";
            document.getElementById("candidate_report").style.display = "none";
            
        }); 
</script>
<!--**********FORM VALIDATION ENDS*************-->
@endsection



