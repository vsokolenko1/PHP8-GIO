<!DOCTYPE html>
<html>
    <head>
        <title>Transactions</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
            }

            table tr th, table tr td {
                padding: 5px;
                border: 1px #eee solid;
            }
            
            table tr td.positive {
                color: darkgreen;
            }
            
            table tr td.negative {
                color: darkred;
            }

            tfoot tr th, tfoot tr td {
                font-size: 20px;
            }

            tfoot tr th {
                text-align: right;
            }
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check #</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($transactions) && !is_null($transactions)):?>
                    <?php foreach($transactions as $transaction):?>
                    <tr>
                        <td><?= date('M j, Y',strtotime($transaction['date']))?></td>
                        <td><?=$transaction['check']?></td>
                        <td><?=$transaction['description']?></td>
                        <td class="<?=$transaction['amount'] > 0 ? 'positive' : 'negative'?>"><?=serviceTransformAmount($transaction['amount'])?></td>
                    </tr>
                    <?php endforeach;?>
                <?php endif?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Income:</th>
                    <td><?=serviceTransformAmount($income)?></td>
                </tr>
                <tr>
                    <th colspan="3">Total Expense:</th>
                    <td><?= serviceTransformAmount($expense)?></td>
                </tr>
                <tr>
                    <th colspan="3">Net Total:</th>
                    <td><?=serviceTransformAmount($profit)?></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>
