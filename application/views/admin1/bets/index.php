<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Bets</h4>
                        <hr>

                        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?php if ($action == 'id') echo 'active' ?>  text-uppercase"
                                   id="id-tab"
                                   data-toggle="tab" href="#id" role="tab" aria-controls="id">id</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link  <?php if ($action == 'office') echo 'active' ?>
                                                                         text-uppercase" id="office-tab"
                                   data-toggle="tab" href="#office" role="tab" aria-controls="office">office</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if ($action == 'user') echo 'active' ?>
                                                                         text-uppercase" id="user-tab" data-toggle="tab"
                                   href="#user" role="tab" aria-controls="user">user id</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?php if ($action == 'partneruser') echo 'active' ?>
                                                                         text-uppercase" id="partneruser-tab"
                                   data-toggle="tab"
                                   href="#partneruser" role="tab" aria-controls="partneruser">partner user id</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if ($action == 'game') echo 'active' ?>
                                                                         text-uppercase" id="game-tab" data-toggle="tab"
                                   href="#game" role="tab" aria-controls="game">game</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if ($action == 'owner') echo 'active' ?>  text-uppercase"
                                   id="owner-tab"
                                   data-toggle="tab" href="#owner" role="tab" aria-controls="owner">owner</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">

                            <div class="tab-pane <?php if ($action == 'id') echo 'active show' ?> fade" id="id"
                                 role="tabpanel"
                                 aria-labelledby="id-tab">
                                <form method="GET" class="form-horizontal">

                                    <div class="form-row row">
                                        <div class="col-4">
                                            <?php echo form::label('id',$labels['id']??'id'); ?>
                                            <input type="text" pattern="[0-9]+" name="id" value="<?php echo $_GET['id']??''; ?>" class="form-control form-control-sm">
                                            <input type="hidden" name="action" value="id">
                                        </div>

                                        <div class="non-form-control ml-auto">
                                            <input class="btn btn-primary btn-sm btn-round" type="submit"
                                                   value="Search">
                                        </div>

                                        <div>
                                            <a class="btn btn-sm btn-round btn-outline-secondary"
                                               href="/enter/bets">Clear</a>
                                        </div>


                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade <?php if ($action == 'office') echo 'active show' ?> " id="office"
                                 role="tabpanel" aria-labelledby="office-tab">
                                <form method="GET" class="form-horizontal">

                                    <div class="form-row row">
                                        <div class="col-3">
                                            <?php echo form::label('office_id',$labels['office_id']??'office_id'); ?>
                                            <?php echo $vidgets['office_id']->render($search_vars, 'search') ?>
                                            <input type="hidden" name="action" value="office">

                                        </div>
                                        <div class="col-4">
                                            <?php echo form::label('created',$labels['created']??'period'); ?>
                                            <?php echo $vidgets['created']->render($search_vars, 'search'); ?>
                                        </div>


                                        <div class="non-form-control ml-auto">
                                            <input class="btn btn-primary btn-sm btn-round" type="submit"
                                                   value="Search">
                                        </div>


                                        <div>
                                            <a class="btn btn-sm btn-round btn-outline-secondary"
                                               href="/enter/bets">Clear</a>
                                        </div>


                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade <?php if ($action == 'user') echo 'active show' ?>" id="user"
                                 role="tabpanel" aria-labelledby="user-tab">
                                <form method="GET" class="form-horizontal">

                                    <div class="form-row row">
                                        <div class="col-3">
                                            <?php echo form::label('user_id',$labels['user_id']??'user_id'); ?>
                                            <input type="text" name="user_id" value="<?php echo $_GET['user_id']??''; ?>" pattern="[0-9]+"
                                                   class="form-control form-control-sm">
                                            <input type="hidden" name="action" value="user">
                                        </div>
                                        <div class="col-4">
                                            <?php echo form::label('created',$labels['created']??'period'); ?>
                                            <?php echo $vidgets['created']->render($search_vars, 'search'); ?>

                                        </div>


                                        <div class="non-form-control ml-auto">
                                            <input class="btn btn-primary btn-sm btn-round" type="submit"
                                                   value="Search">
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-round btn-outline-secondary"
                                               href="/enter/bets">Clear</a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade <?php if ($action == 'partneruseruser') echo 'active show' ?>"
                                 id="partneruser" role="tabpanel"
                                 aria-labelledby="partneruser-tab">
                                <form method="GET" class="form-horizontal">

                                    <div class="form-row row">
                                        <div class="col-3">
                                            <?php echo form::label('partneruseruser_id',$labels['partneruseruser_id']??'Pаrtner User ID'); ?>
                                            <input type="text" name="partneruseruser_id" value=""
                                                   class="form-control form-control-sm">
                                            <input type="hidden" name="action" value="partneruser">
                                        </div>
                                        <div class="col-4">
                                            <?php echo form::label('created',$labels['created']??'period'); ?>
                                            <?php echo $vidgets['created']->render($search_vars, 'search'); ?>

                                        </div>

                                        <div class="non-form-control ml-auto">
                                            <input class="btn btn-primary btn-sm btn-round" type="submit"
                                                   value="Search">
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-round btn-outline-secondary"
                                               href="/enter/bets">Clear</a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade <?php if ($action == 'game') echo 'active show' ?>" id="game"
                                 role="tabpanel" aria-labelledby="game-tab">
                                <form method="GET" class="form-horizontal">

                                    <div class="form-row row">
                                        <div class="col-2">
                                            <?php echo form::label('game_id',$labels['game_id']??'period'); ?>
                                            <?php echo $vidgets['game_id']->render($search_vars, 'search') ?>
                                            <input type="hidden" name="action" value="game">
                                        </div>
                                        <div class="col-4">
                                            <?php echo form::label('created',$labels['created']??'period'); ?>
                                            <?php echo $vidgets['created']->render($search_vars, 'search'); ?>

                                        </div>

                                        <div class="non-form-control ml-auto">
                                            <input class="btn btn-primary btn-sm btn-round" type="submit"
                                                   value="Search">
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-round btn-outline-secondary"
                                               href="/enter/bets">Clear</a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade <?php if ($action == 'owner') echo 'active show' ?>" id="owner"
                                 role="tabpanel" aria-labelledby="owner-tab">
                                <form method="GET" class="form-horizontal">

                                    <div class="form-row row">
                                        <div class="col-2">
                                            <?php echo form::label('owner',$labels['owner']??'owner'); ?>
                                            <?php echo $vidgets['owner']->render($search_vars, 'search') ?>
                                            <input type="hidden" name="action" value="owner">
                                        </div>

                                        <div class="col-4">
                                            <?php echo form::label('created',$labels['created']??'period'); ?>
                                            <?php echo $vidgets['created']->render($search_vars, 'search'); ?>

                                        </div>


                                        <div class="non-form-control ml-auto">
                                            <input class="btn btn-primary btn-sm btn-round" type="submit"
                                                   value="Search">
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-round btn-outline-secondary"
                                               href="/enter/bets">Clear</a>
                                        </div>

                                    </div>
                                </form>
                            </div>


                        </div>
                    </div>


                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table id="table" class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>
                                <tr>
                                    <?php foreach ($fields as $l): ?>
                                        <th
                                                <?php if($l==$sort): ?>
                                                class="sorting_asc superSortdesc"
                                                <?php endif; ?>
                                        >
                                            <?php echo $labels[$l] ?? $l; ?>
                                        </th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data as $c): ?>
                                    <tr>
                                        <?php foreach ($fields as $l): ?>
                                            <?php if (isset($vidgets[$l])): ?>
                                                <td><?php echo $vidgets[$l]->render($c, 'list'); ?></td>
                                            <?php else: ?>
                                                <td><?php echo $c->$l; ?></td>
                                            <?php endif; ?>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>

                        <?php echo $page; ?>
                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>

<!--<style>-->
<!--    td:first-child {-->
<!--        position: fixed;-->
<!--        position: -webkit-sticky;-->
<!--        position: sticky;-->
<!--        left: 0px;-->
<!--        background: #fff;-->
<!--    }-->
<!--</style>-->


<script>
    $(document).ready(function () {
        $('input').addClass('form-control');
        $('input').addClass('form-control-sm');
        $('select').addClass('form-control');
        $('select').addClass('form-control-sm');
        $('.non-form-control input').removeClass('form-control');
        $('.non-form-control input').removeClass('form-control-sm');


    });


</script>
<script type="text/javascript">
    var sortedOn = 0;
    reverseArr = function (a) {
        var arr = [];
        for (var i = a.length - 1; i--;) {
            arr.push(a[i]);
        }
        ;
        arr.push(a[a.length - 1]);
        return arr;
    }

    function sortTable(sortOn) {
        var table = document.getElementById('table');
        var tbody = table.getElementsByTagName('tbody')[0];
        var rows = tbody.getElementsByTagName('tr');
        var rowArray = new Array();
        for (var i = 0, length = rows.length; i < length; i++) {
            rowArray[i] = new Object;
            rowArray[i].oldIndex = i;
            rowArray[i].freeze = false;
            rowArray[i].value = rows[i].getElementsByTagName('td')[sortOn].firstChild.nodeValue;
            if (i == rows.length - 1) {
                rowArray[i].freeze = true;
            }
        }

        if (sortOn == sortedOn) {
//            rowArray.reverse();
            rowArray = reverseArr(rowArray);
        } else {
            sortedOn = sortOn;
            /*
             Decide which function to use from the three:RowCompareNumbers,
             RowCompareDollars or RowCompare (default).
             For first column, I needed numeric comparison.
             */
            if (sortedOn == 0) {
                rowArray.sort(RowCompareNumbers);
            } else {
                rowArray.sort(RowCompare);
            }
        }

        var newTbody = document.createElement('tbody');
        for (var i = 0, length = rowArray.length; i < length; i++) {
            newTbody.appendChild(rows[rowArray[i].oldIndex].cloneNode(true));
        }
        table.replaceChild(newTbody, tbody);
    }

    function RowCompare(a, b) {
        var aVal = parseFloat(a.value.replace(/ /g, ''));
        var bVal = parseFloat(b.value.replace(/ /g, ''));
        aVal = isNaN(aVal) ? 0 : aVal;
        bVal = isNaN(bVal) ? 0 : bVal;
        return (((aVal == bVal) || a.freeze || b.freeze) ? 0 : (aVal > bVal ? 1 : -1));
    }

    // Compare number
    function RowCompareNumbers(a, b) {
        var aVal = parseInt(a.value);
        var bVal = parseInt(b.value);
        return (aVal - bVal);
    }

    // compare currency
    function RowCompareDollars(a, b) {
        var aVal = parseFloat(a.value.substr(1));
        var bVal = parseFloat(b.value.substr(1));
        return (aVal - bVal);
    }
</script>





