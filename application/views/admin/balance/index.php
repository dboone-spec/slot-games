  <style>
                .datatable td,.datatable th{
                    border: 1px solid black;
                    text-align: center;
                    vertical-align: middle;
                    min-width: 150px;
                    padding: 2px 4px;
                }
                .tddate {
                    min-width: 90px !important;
                }
                .datatable th {
                    background-color: #6d9dff; 
                }
                
                
                .datatable tr:nth-child(even) {
                    background-color: #dce7fe; 
                }


                #head, #left_col{
                    display: grid;
                    position: absolute;
                    grid-row-gap: 0px;
                    grid-column-gap: 0px;
                    background-color: white;
                    border-bottom: 1px solid black !important;
                    border-right: 1px solid black !important;
                }
                #head div, #left_col div{
                    padding: 10px;
                    text-align: center;
                    border-left: 1px solid black !important;
                    border-top: 1px solid black !important;
                }
                .sort{
                    cursor: pointer;
                    position: relative;
                }
                .desc::before{
                    content: '\25bc';
                    position: absolute;
                    bottom: 5px;
                    left: calc(50% - 7px);
                }
                .asc::before{
                    content: '\25b2';
                    position: absolute;
                    bottom: 5px;
                    left: calc(50% - 7px);
                }
                thead {
                    /*visibility: hidden;*/
                }
                #left_col {
                    /*visibility: hidden;*/
                    /*background: none;*/
                }
                .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {padding: 5px;}
            </style>

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1>Balance report</h1>

        
          
             <div class="row">
                <div class="col-sm-12" id='scrollblock' style="overflow-x: scroll;  padding-left: 0px; margin-left: 15px;">
                    <div id="head"></div>
                    <div id="left_col"></div>
            
                      <table class="datatable"  >

                        <tr>
                            <th class="tddate"> Date </th>
                            <th class="tddate"> Who </th>
                             <th > Object </th>
                            <th > Amount </th>
                        </tr>


                        <?php foreach($data as $row):?>
                            <tr >
                                <td > <?php echo date('H:m:i  d-m-Y',$row['created']) ?> </td>
                                <td > <?php echo $row['who'] ?> </td>
                                <td > <?php echo $row['object'] ?> </td>
                                <td > <?php echo $row['amount'] ?> </td>
                            </tr >
                        <?php endforeach ?>
                        
            

                        <tbody>
                        </tbody>
                    </table>
                
                    </div>
            </div>
                    
                    
          </div>
    </div>

</div>     
            
<script>
        $(function(){
                $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
        });
</script>