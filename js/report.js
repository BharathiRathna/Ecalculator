$(document).ready(function()
{
    $('#viewReport').click(function()
    {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var action = 'getReports';
        $.ajax({
            url:'ReportAction.php',
            method:"POST",
            data:{fromDate:fromDate,toDate:toDate,action:action},
            dataType:"json",
            success:function(respData)
            {
                if(respData.data.length>0) {
                    var reportHTML = '';
                    var totalExpense = 0;
                    $('#reportTable').hide();
                    $('#noRecords').hide();
                    respData.data.forEach(function(item)
                    {
                        reportHTML+= '<tr>';
                        reportHTML+= '<td>₹'+item['amount']+'</td>';
                        reportHTML+= '<td>'+item['date']+'</td>';
                        reportHTML+= '<td>'+item['category']+'</td>';
                        reportHTML+= '</tr>';
                        totalExpense = totalExpense + parseInt(item['amount']);
                        $('#reportTable').show();
                    });
                    $('#listReports').html(reportHTML);
                    $('#detailSection').hide();
                    $('#totalIncome').text("");
                    $('#totalExpense').text("");
                    $('#totalSavings').text("");
                    respData.income.forEach(function(income)
                    {
                        $('#totalIncome').text("₹"+income['total']);
                        $('#totalExpense').text("₹"+totalExpense);
                        var finalTotal = income['total'] - totalExpense;
                        $('#totalSavings').text("₹"+finalTotal);
                        $('#detailSection').show();
                    });
                }
                else
                {
                    $('#noRecords').html("<strong>No record found!</strong>").show();
                }

            }

        });
    });
    
});