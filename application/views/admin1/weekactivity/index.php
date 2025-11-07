<section class="pc-container">
    <div class="pcoded-content">

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <h4>Week activity</h4>
                        <hr>


                        <form method="GET" class="form-horizontal">

                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm">Date from</span>
                                        </div>
                                        <input type="date" class="form-control form-control-sm" id="time_start"
                                               name="timeFrom"
                                               value="<?php echo date('Y-m-d', strtotime($timeFrom)) ?>">
                                    </div>
                                </div>


                                <div class="non-form-control">
                                    <input class="btn btn-primary btn-sm btn-round" type="submit"
                                           value="<?php echo __('Поиск') ?>"/>
                                </div>
                                <div>
                                    <a class="btn btn-sm btn-round btn-outline-secondary"
                                       href="/enter/report"><?php echo __('Очистить') ?></a>
                                </div>

                                <div class="form-group col-md-12">
                                    <a class="btn btn-sm btn-round btn-outline-secondary"
                                       href="<?php echo url::query(['xls' => 'go']) ?>"><?php echo __('Export to Excel start day ') . date('D d-m-Y', $date) ?></a>
                                </div>

                                <!--<div>
                                        <a class="btn btn-sm btn-round btn-outline-warning" href="javascript:showChart();"><?php echo __('Chart') ?></a>
                                    </div>-->
                            </div>
                        </form>


                    <button onclick="javascript:hideNormal()">Show only red</button>

                    </div>

                    <script>
                        function hideNormal() {
                            document.querySelectorAll('tbody tr').forEach(function(el) {
                                let show=false;
                                Array.from(el.children).forEach(function(c) {
                                    if(c.getAttribute('bgcolor')) {
                                        show=true;
                                    }
                                });
                                if(!show) {
                                    el.style.display='none';
                                }
                            });
                        }
                    </script>

                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table supertable-hover table-bordered tableEvenOdd dataTable">
                                <thead>

                                <th> <?php echo __('Time') . '/' . __('Date') ?> </th>

                                <th> <?php echo date('D d-m-Y', $date) ?> </th>
                                <th> <?php echo date('D d-m-Y', $date - 60 * 60 * 24 * 1) ?> </th>
                                <th> <?php echo date('D d-m-Y', $date - 60 * 60 * 24 * 2) ?> </th>
                                <th> <?php echo date('D d-m-Y', $date - 60 * 60 * 24 * 3) ?> </th>

                                <th> <?php echo date('D d-m-Y', $date - 60 * 60 * 24 * 4) ?> </th>
                                <th> <?php echo date('D d-m-Y', $date - 60 * 60 * 24 * 5) ?> </th>
                                <th> <?php echo date('D d-m-Y', $date - 60 * 60 * 24 * 6) ?> </th>


                                </thead>
                                <tbody>


                                <?php $tPrev = [];
                                $tNow = [];
                                for ($i = 0; $i < 60 * 24; $i++): ?>
                                    <tr>

                                        <td> <?php echo (floor($i / 60) < 10 ? '0' : '') . floor($i / 60) . ':' . (($i % 60) < 10 ? '0' : '') . $i % 60 ?></td>
                                        <?php
                                        $color = false;
                                        $tNow[1] = $data[date('Y-m-d', $date + 60 * $i)][$date + 60 * $i] ?? 0;
                                        if (($tPrev[1] ?? 0) * 0.8 > $tNow[1]) {
                                            $color = true;
                                        }
                                        $tPrev[1] = $tNow[1];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date + 60 * $i)][$date + 60 * $i] ?? 0 ?>
                                        </td>
                                        <?php
                                        $color = false;
                                        $tNow[2] = $data[date('Y-m-d', $date - 60 * 60 * 24 * 1 + 60 * $i)][$date - 60 * 60 * 24 * 1 + 60 * $i] ?? 0;
                                        if (($tPrev[2] ?? 0) * 0.8 > $tNow[2]) {
                                            $color = true;
                                        }
                                        $tPrev[2] = $tNow[2];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date - 60 * 60 * 24 * 1 + 60 * $i)][$date - 60 * 60 * 24 * 1 + 60 * $i] ?? 0 ?>
                                        </td>
                                        <?php
                                        $color = false;
                                        $tNow[3] = $data[date('Y-m-d', $date - 60 * 60 * 24 * 2 + 60 * $i)][$date - 60 * 60 * 24 * 2 + 60 * $i] ?? 0;
                                        if (($tPrev[3] ?? 0) * 0.8 > $tNow[3]) {
                                            $color = true;
                                        }
                                        $tPrev[3] = $tNow[3];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date - 60 * 60 * 24 * 2 + 60 * $i)][$date - 60 * 60 * 24 * 2 + 60 * $i] ?? 0 ?>
                                        </td>
                                        <?php
                                        $color = false;
                                        $tNow[4] = $data[date('Y-m-d', $date - 60 * 60 * 24 * 3 + 60 * $i)][$date - 60 * 60 * 24 * 3 + 60 * $i] ?? 0;
                                        if (($tPrev[4] ?? 0) * 0.8 > $tNow[4]) {
                                            $color = true;
                                        }
                                        $tPrev[4] = $tNow[4];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date - 60 * 60 * 24 * 3 + 60 * $i)][$date - 60 * 60 * 24 * 3 + 60 * $i] ?? 0 ?>
                                        </td>

                                        <?php
                                        $color = false;
                                        $tNow[5] = $data[date('Y-m-d', $date - 60 * 60 * 24 * 4 + 60 * $i)][$date - 60 * 60 * 24 * 4 + 60 * $i] ?? 0;
                                        if (($tPrev[5] ?? 0) * 0.8 > $tNow[5]) {
                                            $color = true;
                                        }
                                        $tPrev[5] = $tNow[5];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date - 60 * 60 * 24 * 4 + 60 * $i)][$date - 60 * 60 * 24 * 4 + 60 * $i] ?? 0 ?>
                                        </td>
                                        <?php
                                        $color = false;
                                        $tNow[6] = $data[date('Y-m-d', $date - 60 * 60 * 24 * 5 + 60 * $i)][$date - 60 * 60 * 24 * 5 + 60 * $i] ?? 0;
                                        if (($tPrev[6] ?? 0) * 0.8 > $tNow[6]) {
                                            $color = true;
                                        }
                                        $tPrev[6] = $tNow[6];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date - 60 * 60 * 24 * 5 + 60 * $i)][$date - 60 * 60 * 24 * 5 + 60 * $i] ?? 0 ?>
                                        </td>
                                        <?php
                                        $color = false;
                                        $tNow[7] = $data[date('Y-m-d', $date - 60 * 60 * 24 * 6 + 60 * $i)][$date - 60 * 60 * 24 * 6 + 60 * $i] ?? 0;
                                        if (($tPrev[7] ?? 0) * 0.8 > $tNow[7]) {
                                            $color = true;
                                        }
                                        $tPrev[7] = $tNow[7];
                                        ?>
                                        <td <?php if ($color) {
                                            echo 'bgcolor="red"';
                                        } ?>>
                                            <?php echo $data[date('Y-m-d', $date - 60 * 60 * 24 * 6 + 60 * $i)][$date - 60 * 60 * 24 * 6 + 60 * $i] ?? 0 ?>
                                        </td>


                                    </tr>
                                <?php endfor ?>


                                </tbody>
                            </table>


                        </div>
                    </div>


                </div>
            </div>

        </div>
        <!-- [ Main Content ] end -->
    </div>
</section>


<script>
    function showChart() {
        let search = window.location.search;

        if (search.length == 0) {
            search = '?chart=1';
        } else {
            search += '&chart=1';
        }

        window.open(window.location.origin + window.location.pathname + search, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=800,width=1024');
    }
</script>



