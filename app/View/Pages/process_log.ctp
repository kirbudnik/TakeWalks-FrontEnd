<?php

function clean($data){
    $data = str_replace('_',' ', $data);
    $data = ucfirst($data);
    return $data;
}

foreach($logs as $log):
    $log = json_decode($log, 1);

    //split the headers and data
    $headers = array('type','created','ip');
    $data = array(clean($log['type']), $log['created'], $log['ip']);
    $class = $log['type'];
    unset($log['type']);
    unset($log['created']);
    unset($log['ip']);

    foreach($log as $head => $val){
        $headers[] = clean($head);
        $data[] = is_array($val) ? '<pre>' . print_r($val,1) . '</pre>' : $val;
    }
    ?>
    <style>
        table{
            margin-top: 10px;
            border: 1px solid #ccc;
            border-right: none;
            border-bottom: none;

        }
        td, th{
            border-right:1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 7px;
            font-size: 13px;
            vertical-align: top;
        }
        th{
            background: #eaeaea;
        }
        .payment_response{
            background: #EAF7ED;
        }
        .payment{
            background: #F7F6EA;
        }
        .purchased_item{
            background: #EAF3F7;
        }
        .add_to_cart{
            background: #F2EAF7;
        }
    </style>

    <table class="<?php echo $class ?>" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <th><?php echo implode('</th><th>', $headers) ?></th>
        </tr>
        <tr>
            <td><?php echo implode('</td><td>', $data) ?></td>
        </tr>
    </table>


<?php endforeach ?>